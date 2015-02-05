<?php
function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;
	global $smcFunc;

	// Let them know, if their report was a success!
	if ($context['report_sent'])
	{
		echo '
			<div class="windowbg" id="profile_success">
				', $txt['report_sent'], '
			</div>';
	}

	// Show the anchor for the top and for the first message. If the first message is new, say so.
	echo '
			<a id="top"></a>
			<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '';

	// Is this topic also a poll?
	if ($context['is_poll'])
	{
		echo '
			<div id="poll">
				<div class="cat_bar">
					<h3 class="catbg">
						<span class="ie6_header floatleft"><img src="', $settings['images_url'], '/topic/', $context['poll']['is_locked'] ? 'normal_poll_locked' : 'normal_poll', '.gif" alt="" class="icon" /> ', $txt['poll'], '</span>
					</h3>
				</div>
				<div class="windowbg">
					<span class="topslice"><span></span></span>
					<div class="content" id="poll_options">
						<h4 id="pollquestion">
							', $context['poll']['question'], '
						</h4>';

		// Are they not allowed to vote but allowed to view the options?
		if ($context['poll']['show_results'] || !$context['allow_vote'])
		{
			echo '
					<dl class="options">';

			// Show each option with its corresponding percentage bar.
			foreach ($context['poll']['options'] as $option)
			{
				echo '
						<dt class="middletext', $option['voted_this'] ? ' voted' : '', '">', $option['option'], '</dt>
						<dd class="middletext statsbar', $option['voted_this'] ? ' voted' : '', '">';

				if ($context['allow_poll_view'])
					echo '
							', $option['bar_ndt'], '
							<span class="percentage">', $option['votes'], ' (', $option['percent'], '%)</span>';

				echo '
						</dd>';
			}

			echo '
					</dl>';

			if ($context['allow_poll_view'])
				echo '
						<p><strong>', $txt['poll_total_voters'], ':</strong> ', $context['poll']['total_votes'], '</p>';
		}
		// They are allowed to vote! Go to it!
		else
		{
			echo '
						<form action="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], '" method="post" accept-charset="', $context['character_set'], '">';

			// Show a warning if they are allowed more than one option.
			if ($context['poll']['allowed_warning'])
				echo '
							<p class="smallpadding">', $context['poll']['allowed_warning'], '</p>';

			echo '
							<ul class="reset options">';

			// Show each option with its button - a radio likely.
			foreach ($context['poll']['options'] as $option)
				echo '
								<li class="middletext">', $option['vote_button'], ' <label for="', $option['id'], '">', $option['option'], '</label></li>';

			echo '
							</ul>
							<div class="submitbutton">
								<input type="submit" value="', $txt['poll_vote'], '" class="button_submit" />
								<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
							</div>
						</form>';
		}

		// Is the clock ticking?
		if (!empty($context['poll']['expire_time']))
			echo '
						<p><strong>', ($context['poll']['is_expired'] ? $txt['poll_expired_on'] : $txt['poll_expires_on']), ':</strong> ', $context['poll']['expire_time'], '</p>';

		echo '
					</div>
					<span class="botslice"><span></span></span>
				</div>
			</div>
			<div id="pollmoderation">';

		// Build the poll moderation button array.
		$poll_buttons = array(
			'vote' => array('test' => 'allow_return_vote', 'text' => 'poll_return_vote', 'image' => 'poll_options.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start']),
			'results' => array('test' => 'show_view_results_button', 'text' => 'poll_results', 'image' => 'poll_results.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start'] . ';viewresults'),
			'change_vote' => array('test' => 'allow_change_vote', 'text' => 'poll_change_vote', 'image' => 'poll_change_vote.gif', 'lang' => true, 'url' => $scripturl . '?action=vote;topic=' . $context['current_topic'] . '.' . $context['start'] . ';poll=' . $context['poll']['id'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'lock' => array('test' => 'allow_lock_poll', 'text' => (!$context['poll']['is_locked'] ? 'poll_lock' : 'poll_unlock'), 'image' => 'poll_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lockvoting;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
			'edit' => array('test' => 'allow_edit_poll', 'text' => 'poll_edit', 'image' => 'poll_edit.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;topic=' . $context['current_topic'] . '.' . $context['start']),
			'remove_poll' => array('test' => 'can_remove_poll', 'text' => 'poll_remove', 'image' => 'admin_remove_poll.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['poll_remove_warn'] . '\');"', 'url' => $scripturl . '?action=removepoll;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		);

		template_button_strip($poll_buttons);

		echo '
			</div>';
	}

	// Does this topic have some events linked to it?
	if (!empty($context['linked_calendar_events']))
	{
		echo '
			<div class="linked_events">
				<div class="title_bar">
					<h3 class="titlebg headerpadding">', $txt['calendar_linked_events'], '</h3>
				</div>
				<div class="windowbg">
					<span class="topslice"><span></span></span>
					<div class="content">
						<ul class="reset">';

		foreach ($context['linked_calendar_events'] as $event)
			echo '
							<li>
								', ($event['can_edit'] ? '<a href="' . $event['modify_href'] . '"> <img src="' . $settings['images_url'] . '/icons/modify_small.gif" alt="" title="' . $txt['modify'] . '" class="edit_event" /></a> ' : ''), '<strong>', $event['title'], '</strong>: ', $event['start_date'], ($event['start_date'] != $event['end_date'] ? ' - ' . $event['end_date'] : ''), '
							</li>';

		echo '
						</ul>
					</div>
					<span class="botslice"><span></span></span>
				</div>
			</div>';
	}

	// Build the normal button array.
	$normal_buttons = array(
		'reply' => array('test' => 'can_reply', 'text' => 'reply', 'image' => 'reply.gif', 'lang' => true, 'url' => $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';last_msg=' . $context['topic_last_message'], 'active' => true),
		'add_poll' => array('test' => 'can_add_poll', 'text' => 'add_poll', 'image' => 'add_poll.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;add;topic=' . $context['current_topic'] . '.' . $context['start']),
		/*'notify' => array('test' => 'can_mark_notify', 'text' => $context['is_marked_notify'] ? 'unnotify' : 'notify', 'image' => ($context['is_marked_notify'] ? 'un' : '') . 'notify.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . ($context['is_marked_notify'] ? $txt['notification_disable_topic'] : $txt['notification_enable_topic']) . '\');"', 'url' => $scripturl . '?action=notify;sa=' . ($context['is_marked_notify'] ? 'off' : 'on') . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),*/
		'mark_unread' => array('test' => 'can_mark_unread', 'text' => 'mark_unread', 'image' => 'markunread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=topic;t=' . $context['mark_unread_time'] . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		/*'send' => array('test' => 'can_send_topic', 'text' => 'send_topic', 'image' => 'sendtopic.gif', 'lang' => true, 'url' => $scripturl . '?action=emailuser;sa=sendtopic;topic=' . $context['current_topic'] . '.0'),*/
		'print' => array('text' => 'print', 'image' => 'print.gif', 'lang' => true, 'custom' => 'rel="new_win nofollow"', 'url' => $scripturl . '?action=printpage;topic=' . $context['current_topic'] . '.0'),
	);

	// Allow adding new buttons easily.
	call_integration_hook('integrate_display_buttons', array(&$normal_buttons));

	// Article related buttons...
	if (!empty($modSettings['articleactive']))
	{
		if ($context['can_add_article'] && !$context['topic_is_article'])
			$normal_buttons['add_article'] = array('text' => 'sp-add_article', 'image' => 'addarticle.gif', 'lang' => true, 'url' => $scripturl . '?action=portal;sa=addarticle;message=' . $context['topic_first_message'] . ';return=' . $context['current_topic'] . '.' . $context['start'] . ';sesc=' . $context['session_id']);
		if ($context['can_remove_article'] && $context['topic_is_article'])
			$normal_buttons['remove_article'] = array('text' => 'sp-remove_article', 'image' => 'removearticle.gif', 'lang' => true, 'url' => $scripturl . '?action=portal;sa=removearticle;message=' . $context['topic_first_message'] . ';return=' . $context['current_topic'] . '.' . $context['start'] . ';sesc=' . $context['session_id']);
	}

	// Show the page index... "Pages: [1]".
	echo '
			<div class="pagesection">
				<div class="nextlinks">', $context['previous_next'], '</div>', template_button_strip($normal_buttons, 'right'), '
				<div class="pagelinks floatleft">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#lastPost"><strong>' . $txt['go_down'] . '</strong></a>' : '', '</div>
			</div>';

	// Show the topic information - icon, subject, etc.
	echo '
			<div id="forumposts">
				<div class="cat_bar">
					<h3 class="catbg">
						<img src="', $settings['images_url'], '/topic/', $context['class'], '.gif" align="bottom" alt="" />
						', $context['subject'], '
					</h3>
				</div>';

	if (!empty($settings['display_who_viewing']))
	{
		echo '
				<p id="whoisviewing" class="smalltext">';

		// Show just numbers...?
		if ($settings['display_who_viewing'] == 1)
				echo count($context['view_members']), ' ', count($context['view_members']) == 1 ? $txt['who_member'] : $txt['members'];
		// Or show the actual people viewing the topic?
		else
			echo empty($context['view_members_list']) ? '0 ' . $txt['members'] : implode(', ', $context['view_members_list']) . ((empty($context['view_num_hidden']) || $context['can_moderate_forum']) ? '' : ' (+ ' . $context['view_num_hidden'] . ' ' . $txt['hidden'] . ')');

		// Now show how many guests are here too.
		echo $txt['who_and'], $context['view_num_guests'], ' ', $context['view_num_guests'] == 1 ? $txt['guest'] : $txt['guests'], $txt['who_viewing_topic'], '
				</p>';
	}

	echo '
				<form action="', $scripturl, '?action=quickmod2;topic=', $context['current_topic'], '.', $context['start'], '" method="post" accept-charset="', $context['character_set'], '" name="quickModForm" id="quickModForm" style="margin: 0;" onsubmit="return oQuickModify.bInEditMode ? oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\') : false">';

	$ignoredMsgs = array();
	$removableMessageIDs = array();
	$alternate = false;

	// Get all the messages...
	while ($message = $context['get_message']())
	{
		if ($message['body'] && !isset($bodyChanged))
		{
			$bodyChanged = true;
			$message['body'] = preg_replace_callback('/(<img [^>]*?src=")(http:\/\/img.tapatalk.com\/d\/[0-9]{2}\/[0-9]{2}\/[0-9]{2}\/)([^\/]*?)(".*?>)/i',
			create_function(
				'$matches',
				'return \'<a href="http://tapatalk.com/tapatalk_image.php?img=\'.urlencode(base64_encode($matches[2].\'original/\'.$matches[3])).\'" target="_blank">\'.$matches[1].$matches[2].\'thumbnail/\'.$matches[3].$matches[4].\'</a>\';'
			),
			$message['body']);
			
			$pageProtocol = 'http';
			if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 1)){  //Apache
				$pageProtocol = 'https';
			}elseif(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on')){ //IIS
				$pageProtocol = 'https';
			}elseif(isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443)){ //other
				$pageProtocol = 'https';
			}
			$message['body'] = preg_replace('/\[emoji(\d+)\]/i', '<img src="'.$pageProtocol.'://s3.amazonaws.com/tapatalk-emoji/emoji$1.png" />', $message['body']);
		}

		$ignoring = false;
		$alternate = !$alternate;
		if ($message['can_remove'])
			$removableMessageIDs[] = $message['id'];

		// Are we ignoring this message?
		if (!empty($message['is_ignored']))
		{
			$ignoring = true;
			$ignoredMsgs[] = $message['id'];
		}
		
				//Thread Plates start here. Brace yourself for a lot of code...
		//First, let's gather the flags for group/ranks

		$membergroupflag = 'false';
		$postgroupflag = 'false';
		$extremeflag = 'false';
		$modflag = false;
		$adminflag = false;
		$monitorflag = false;
		$sageflag = 'false';
		$banflag = 'false';
		$threadplatespecial = 'false';
		$colortext = 'default';
		$customprefix = 'default';
		$customrank = 'default';
		$customtitle = 'false';
		$imposter = false;
		$timeonline = 0;

		include('connect.php');

		$con=mysqli_connect($db_server1,$db_user1,$db_passwd1,$db_name1);
		// Check connection
		if (mysqli_connect_errno()) {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		$mysql_input = "SELECT total_time_logged_in FROM smf2_members
		WHERE id_member='" . $message['member']['id'] . "'";

		$result = mysqli_query($con,$mysql_input);

		while($row = mysqli_fetch_array($result)) {
		  $timeonline =  $row['total_time_logged_in'];
		}

		//Convert time into extra positive trust...
		$timetrust = number_format(($timeonline/86400)*10);
		$hoursonline = number_format($timeonline/3600);
		$daysonline = $timeonline/86400;

		if (!empty($message['member']['group']))
		$membergroupflag = 'true';

		$guestflag = 'true';

		if (!$message['member']['is_guest']) {
			$guestflag = 'false';
			if ((empty($settings['hide_post_group']) || $message['member']['group'] == '') && $message['member']['post_group'] != '')
				$postgroupflag = 'true';
		}

		$karmagood = $message['member']['karma']['good'] + $timetrust;
		$karmabad = $message['member']['karma']['bad'];
		if ($message['member']['posts'] < 1) //Make sure their post count isn't zero so the universe doesn't break.
			$postnum = 1;
		else
			$postnum = $message['member']['posts'];
			
		$ppbmodify = 0.30; //This is the original number we were multiplying positive trust by.
			
		/*if ($postnum >= 3000)
			$ppbmodify = 0.95;
		if ($postnum >= 5000)
			$ppbmodify = 0.90;
		if ($postnum >= 7000)
			$ppbmodify = 0.85;*/
			
		//Instead...let's make this more interesting and add on more positive trust depending on their post count.
		if ($postnum >= 2500)
			$ppbmodify = 0.33;
		if ($postnum >= 3000)
			$ppbmodify = 0.40;
		if ($postnum >= 4000)
			$ppbmodify = 0.45;
		if ($postnum >= 5000)
			$ppbmodify = 0.50;
		if ($postnum >= 6000)
			$ppbmodify = 0.55;
		if ($postnum >= 7000)
			$ppbmodify = 0.60;
			
		//$karmabad = $ppbmodify * $karmabad;
		
		$postbadquote = ((($karmabad * 10) * $ppbmodify) / $message['member']['posts']);
		$badperpost = (($karmabad - ($karmagood * $ppbmodify)) / $message['member']['posts']);
		if ($badperpost < 0)
			$badperpost = 0; //If they're so awesome that the number is negative, make it zero instead.
		$postgoodquote = (($karmagood * 10) / $message['member']['posts']);
		$karmaquote = ($karmagood + 1) / (($karmabad + 1) * 0.80);
		$karmarank = $karmagood - $karmabad;
		$mykarmarank = $user_info['karma']['good'] - $user_info['karma']['bad'];
        
        //Staff Hat code follows; must be before the trustranks.php include...
        foreach ($message['member']['custom_fields'] as $custom) {
				if ($custom['title'] == "Staff Hat") {
                    $staffhat = $custom['value'];
				}
			}

		//include("specialplates.php");
		include("nameplates.php");
		include("trustranks.php");
        $staffhat = '';
        
        // if they are warned, then hide their customization!
        if ($message['member']['warning_status'] && $adminflag == false) {
            /*$trusttitle = '';
            $bpbar = '#222222';
            $bpborder = '#222222';*/
            $custompost = false;
            $customplate = false;
            $custombar = false;
            $customborder = false;
            $customtitle = false;
            $threadplatespecial = 'false';
        }

		//End of nameplate prep...

		// Show the message anchor and a "new" anchor if this message is new.
		if ($message['id'] != $context['first_message'])
			echo '
				<a id="msg', $message['id'], '"></a>', $message['first_new'] ? '<a id="new"></a>' : '';

		echo '
				<div class="', $message['approved'] ? ($message['alternate'] == 0 ? 'windowbg' : 'windowbg2') : 'approvebg', '">';
				
				if ($ignoring && $adminflag == false && $modflag == false) {
					echo '<section class="muted"><div class="sp-wrap"><div class="sp-head sp-open">';
					echo $message['member']['name'];
					echo ' | Muted</div><div class="sp-body" style="display: none;">';
				}
				
					echo '<span class="topslice"><span></span></span>
					<div class="post_wrapper">';
					

		// Show information about the poster of this message.
		echo '
						<div class="poster">
							<ul class="reset smalltext" id="msg_', $message['id'], '_extra_info">';

		// Don't show these things for guests.
		if (!$message['member']['is_guest'])
		{
		
			foreach ($message['member']['custom_fields'] as $custom) {
				if (($custom['title'] == "Custom Title Bar Color") && ($custom['value'] != '') && (($custombar == true) || ($context['current_board'] == 8))) {
						//echo '#' . $custom['value'];
						$bpbar = '#' . $custom['value'];
					}
			}
				
			foreach ($message['member']['custom_fields'] as $custom) {
				if (($custom['title'] == "Custom Title Bar Border") && ($custom['value'] != '') && (($customborder == true) || ($context['current_board'] == 8))) {
					//echo '#' . $custom['value'];
					$bpborder = '#' . $custom['value'];
				}
			}
			
			foreach ($message['member']['custom_fields'] as $custom) {
				if (($custom['title'] == "Custom Post Color") && ($custom['value'] != '') && (($custompost == true) || ($context['current_board'] == 8))) {
						//echo '#' . $custom['value'];
						$colortext = '#' . $custom['value'];
					}
			}
		
			// Colored Avatar Box
			echo '<div style="background: ',$bpbar,'; border: 1px solid ',$bpborder,'; width: 95%; margin: -10px 0 0 0;">';	
			//Content Avatar Box
			echo '<div style="width: 95%; margin: 2.5% auto 2.5% auto; max-height: 200px; overflow: hidden;">';
			// Show avatars, images, etc.?
			
			if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']) && !empty($message['member']['avatar']['image']))
				echo '
								
									<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '" title="',$message['member']['blurb'],'">
										', $message['member']['avatar']['image'], '
									</a>
								';

				else
					echo '
								
									<a href="', $scripturl, '?action=profile;u=', $message['member']['id'], '" title="',$message['member']['blurb'],'">
										<img class="avatar" src="'.$settings['theme_url'].'/data/img/MonkeyAvatar.png" border="0" />
									</a>
								';
			
			// Show how many posts they have made.
								echo '</div></div>';
								
			// Is this user allowed to modify this member's karma?
			/*if (($message['member']['karma']['allow']) && ($adminflag != 'true'))
				echo '
								<div style="width: 95%; margin: 5px 0 0 0;">
									<div class="trust_bad">
									<a style="padding: 10% 55% 10% 45%; color: #FFFFFF;" href="', $scripturl, '?action=modifykarma;sa=smite;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';m=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">&#8211;</a>
									</div>
									<div class="trust_good">
										<a style="padding: 10% 55% 10% 45%; color: #FFFFFF;" href="', $scripturl, '?action=modifykarma;sa=applaud;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.' . $context['start'], ';m=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">+</a></div>
									<div style="clear:both;"></div>
								</div>';*/
			

			// Any custom fields to show as icons?
			// Show the profile, website, email address, and personal message buttons.

			// Any custom fields for standard placement?

			// Are we showing the warning status?
			if ($message['member']['can_see_warning'])
				echo $context['can_issue_warning'] ? '<a href="' . $scripturl . '?action=profile;area=issuewarning;u=' . $message['member']['id'] . '">' : '', '', $context['can_issue_warning'] ? '</a>' : '', '<span style="margin: 3px 0 0 0; padding: 3px 0 0 0;"  class="warn_', $message['member']['warning_status'], '">', $txt['warn_' . $message['member']['warning_status']], '</span>';
		}
		if ($message['member']['is_guest']) {
			// Colored Avatar Box
			echo '<div style="background: ',$bpbar,'; border: 1px solid ',$bpborder,'; width: 95%; margin: -10px 0 0 0;">';	
			//Content Avatar Box
			echo '<div style="width: 95%; margin: 2.5% auto 2.5% auto;">';
			// Show avatars, images, etc.?
			if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']) && !empty($message['member']['avatar']['image']) && ($banflag != 'true'))
				echo $message['member']['avatar']['image'];
			else
				echo '<img class="avatar" src="http://sep7agon.net/images/rabbitmask.png" border="0" />';
			echo '</div></div>';
		}
		
					// If the screen is small, let's give the user an expandable set of buttons to use...
			// First, the box that only shows up when they're on mobile...
			echo '<div class="mobilelinks">';
				echo '<div OnClick="OpenMlinks', $message['counter'], '()" id="mlinkshead', $message['counter'],'" style="float: left; background: url(http://sep7agon.net/images/mlinkshead.png) #111111 top center; background-repeat: no-repeat; width: 100%; height: 20px; overflow: hidden; cursor: pointer;">';
					echo '&nbsp;';
        
				echo '</div><!--end mlinkshead-->';
				echo '<div class="mlinksbody" id="mlinksbody', $message['counter'],'" style="display: none;"><center><br /><br />';
					
					if ($banflag2 != 'true') {
					
							if ($message['can_approve'])

							echo '<a href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context

							['session_id'], '"><img src="http://sep7agon.net/images/mlinks_approve.png" border="0" /></a><br /><br />';
					
							if ($context['can_quote'] && !empty($options['display_quick_reply']) && ($banflag != 'true'))

							echo '<a href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], '" onclick="return 

							oQuickReply.quote(', $message['id'], ');"><img src="http://sep7agon.net/images/mlinks_quote.png" border="0" /></a><br /><br />';


							elseif ($context['can_quote'] && ($banflag != 'true'))
							echo '<a href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], 

							'"><img src="http://sep7agon.net/images/mlinks_quote.png" border="0" /></a><br /><br />';
							
							if ($message['can_modify'])

							echo '<a href="', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], '"><img src="http://sep7agon.net/images/mlinks_edit.png" border="0" /></a><br /><br />';

							if ($message['can_remove'])

							echo '<a href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['remove_message'], '?\');"><img src="http://sep7agon.net/images/mlinks_delete.png" border="0" /></a><br /><br />';


							if ($context['can_moderate_forum'] && $message['member']['id'] != 1 && $message['member']['id'] != 2)

							echo '<a href="', $scripturl, '?action=admin;area=ban;sa=add;u=', $message['member']['id'], '"><img src="http://sep7agon.net/images/mlinks_judge.png" border="0" /></a><br /><br />';

							else if (($context['can_report_moderator']) && ($adminflag == false))

							echo '<a href="', $scripturl, '?action=reporttm;topic=', $context['current_topic'], '.', $message['counter'], ';msg=', $message['id'], '"><img src="http://sep7agon.net/images/mlinks_report.png" border="0" /></a><br />';
							}
					
				echo '<br /></center></div><!--end mlinksbody-->';
			echo '</div><!--end mobilelinks-->';
		
		/*if ($context['user']['id'] == 1) {
			print_r($context);
		}*/
		
		// Done with the information about the poster... on to the post itself.
		echo '
							</ul>
						</div>
						<div class="postarea">';
							echo '<div class="nameplates" style="width:auto; height:20px; background: ';
							
							echo $bpbar;
							
							echo' url(';
							
							$printplateflag = false;
							foreach ($message['member']['custom_fields'] as $custom) {
								if (($custom['title'] == "Custom Nameplate") && ($custom['value'] != '') && ($imposter == true) && (($customplate == true) || ($context['current_board'] == 8))) {
									//echo $custom['value'];
									$printplate = $custom['value'];
									$printplateflag = true;
								}
							}
							
							if ($printplateflag == true)
								echo $printplate;
							elseif ($threadplatespecial == 'true')
								echo 'http://sep7agon.net/images/nameplate_',$platename,'.png';
                            else
                                echo '';
							
							echo '); border: 1px solid ';
							
							echo $bpborder;
							
							echo '; background-repeat:no-repeat; background-position: top right; text-align:left; margin: -10px 5px 0 auto; padding: 5px 0 0 5px; overflow:hidden;">';
							
							if ($postgroupflag == 'true')
								echo '<b><a style="color: #F0F8FF;" href="', $scripturl, '?action=profile;u=', $message['member']['id'], '" title="',$message['member']['blurb'],'">';
							else
								echo '<b style="color: #EEEEEE;">';
							
							if ($modflag == true) {
								echo '<div class="icon_mod">&nbsp;</div> ';
							}
							
							if ($monitorflag == true) {
								echo '<div class="icon_monitor">&nbsp;</div> ';
							}
							
							if ($adminflag == true) {
								echo '<div class="icon_admin">&nbsp;</div> ';
							}
							
							echo '<!--<font face="Arial">-->';
							
							if ($postgroupflag == 'true')
								echo $message['member']['name'], '</a></b>';
							else
								echo $message['member']['name'], '</b>';

							echo '<span id="forumrank" style="z-index:50; color:#FFFFFF"> | ';

							//Start karma/trust prefixes...
							//First, get a variable for the karma...
							if ($customtitle != true || $message['member']['title'] == '') {
								if ($customprefix == 'default')
									echo '<span class="trust_title">' . $trusttitle . '</span>';
								else
									echo $customprefix;
									
								if ($postgroupflag == 'true')
								{
									if ($modflag == true)
										$postgroup1 = 'Ninja';
									if ($adminflag == true)
										$postgroup1 = 'Architect';
									if (($modflag == false) && ($adminflag == false))
										$postgroup1 = $message['member']['post_group'];
									
									if ($customrank == 'default')
										echo $postgroup1;
									else
										echo $customrank;
								}
								else
								echo 'Mystery Member';
							}
							if ($customtitle == true && $message['member']['title'] != '') {
								echo substr($message['member']['title'],0,35);
							}
							
							echo '</span>';
							echo '<div style="z-index:50; float:right; padding-right:10px;">';

							echo '<div OnClick="OpenSig', $message['counter'], '()" id="sigarrow', $message['counter'], '" style="float: right; margin: 0 0 0 3px; display: inline; width: 15px; height: 15px; background: url(http://sep7agon.net/images/sigarrow.png) 0 0 no-repeat; cursor: pointer;">&nbsp;</div><span style="float: right;" class="sig_more">more | </span>';
							echo '</div>';
							//ends the control links...

							echo '<!--</font>-->';
							echo '</div>';
							//End of nameplate code...
							
							//This is where the sigs will go...
							echo '<div id="sig_', $message['counter'], '" style="display: block; height: 0; overflow: hidden; margin: 0 5px 0 0;">';
								echo '<div style="background: #333333; height: auto; overflow: hidden; color: #EEEEEE; padding: 7px; font-size: 90%;"><div style="float: left;"><span class="trans">XBL:</span> ';
        
                                foreach ($message['member']['custom_fields'] as $custom) {
									if ($custom['title'] == "XBL Gamertag") {
										echo $custom['value'];
									}
								}
								
								echo '<br /><span class="trans">PSN:</span> ';
								
								foreach ($message['member']['custom_fields'] as $custom) {
									if ($custom['title'] == "PSN ID") {
										echo $custom['value'];
									}
								}
								
								echo '<br /><span class="trans">Steam:</span> ';
								
								foreach ($message['member']['custom_fields'] as $custom) {
									if ($custom['title'] == "Steam ID") {
										echo $custom['value'];
									}
								}
								
								echo '</div>';
								echo '<div style="float: right; text-align: right; font-size: 100%;">';
        
                                if (!$message['member']['is_guest'])
                                    echo '<span class="trans">ID:</span> ' , $message['member']['username'], '<br />';
								
								if ($context['can_send_pm'])
									echo '
										<a href="', $scripturl, '?action=pm;sa=send;u=', $message['member']['id'], '" title="', $message['member']['online']['is_online'] ? $txt['pm_online'] : $txt['pm_offline'], '">message user</a>';
								
		
                                if ($message['can_remove'] || ($context['can_split'] && !empty($context['real_num_replies'])))
                                    echo '<br />';
        
								if ($message['can_remove'])

								echo '<a href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" 

								onclick="return confirm(\'', $txt['remove_message'], '?\');">delete</a>';
								
								if (($message['can_remove']) && ($context['can_split'] && !empty($context['real_num_replies'])))
								echo ' / ';
								
								if ($context['can_split'] && !empty($context['real_num_replies']))
								echo '<a href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $message['id'], '">split</a>';
        
                                if ($message['can_remove'] || ($context['can_split'] && !empty($context['real_num_replies'])))
                                    echo '<br />';
								
								// Can we issue a warning because of this post?  Remember, we can't give guests warnings.
								if ($adminflag == false && $context['can_issue_warning'] && !$message['is_message_author'] && !$message['member']['is_guest'])
									echo '<a href="', $scripturl, '?action=profile;area=issuewarning;u=', $message['member']['id'], ';msg=', $message['id'], '">issue warning</a>';
								
								echo '<br />', number_format($message['member']['posts'],0,'.',','), ' posts';
								
								if ($context['user']['is_admin']) {
									echo '<br />Score: ' . number_format($badperpost,4,'.','');
									echo '<br />Days: ' . number_format($daysonline);
									echo '<br />+Trust: ' . $karmagood;
								}
								
								echo '</div></div>';
								
								// Is this user allowed to modify this member's karma?
								if (($message['member']['karma']['allow']) && ($adminflag == false) /*&& ($modflag != 'true')*/)
									echo '<div style="width: 100%; overflow: hidden; height: auto;">
									<div class="trust_bad">
									<a style="padding: 10% 55% 10% 45%; color: #FFFFFF;" href="', $scripturl, '?action=modifykarma;sa=smite;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';m=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">&#8211;</a>
									</div>
									<div class="trust_good">
										<a style="padding: 10% 55% 10% 45%; color: #FFFFFF;" href="', $scripturl, '?action=modifykarma;sa=applaud;uid=', $message['member']['id'], ';topic=', $context['current_topic'], '.' . $context['start'], ';m=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">+</a></div>
									<div style="clear:both;"></div>
								</div>';
								
								echo '<div style="background: #111111; overflow: hidden; height: 125px; color: #EEEEEE; padding: 7px; font-size: 90%;">';
									if (!empty($message['member']['signature']) && empty($options['show_no_signatures']) && $context['signature_enabled'])
										echo '<div id="msg_', $message['id'], '_signature">', $message['member']['signature'], '</div>';
									else
										echo '&nbsp;';
								echo '</div>
							</div>';
							//end sig
							
							//Oh yes, there will be javascript...
							echo '<script>
							function Open', $message['counter'], '() {
								document.getElementById("action_', $message['counter'], '").style.display="inline";
								document.getElementById("plusminus', $message['counter'], '").innerHTML=" &nbsp; &#8211;";
								document.getElementById("plusminus', $message['counter'], '").onclick=function(){Close', $message['counter'], '();};
							}
	
							function Close', $message['counter'], '() {
								document.getElementById("action_', $message['counter'], '").style.display="none";
								document.getElementById("plusminus', $message['counter'], '").innerHTML=" &nbsp; +";
								document.getElementById("plusminus', $message['counter'], '").onclick=function(){Open', $message['counter'], '();};
							}
							function OpenSig', $message['counter'], '() {
								document.getElementById("sig_', $message['counter'], '").style.height="auto";
								document.getElementById("sigarrow', $message['counter'], '").style.background="url(http://sep7agon.net/images/sigarrow.png) 0 -15px no-repeat";
								document.getElementById("sigarrow', $message['counter'], '").onclick=function(){CloseSig', $message['counter'], '();};
								document.getElementById("signame', $message['counter'], '").onclick=function(){CloseSig', $message['counter'], '();};
							}
	
							function CloseSig', $message['counter'], '() {
								document.getElementById("sig_', $message['counter'], '").style.height="0";
								document.getElementById("sigarrow', $message['counter'], '").style.background="url(http://sep7agon.net/images/sigarrow.png) 0 0 no-repeat";
								document.getElementById("sigarrow', $message['counter'], '").onclick=function(){OpenSig', $message['counter'], '();};
								document.getElementById("signame', $message['counter'], '").onclick=function(){OpenSig', $message['counter'], '();};
							}
							
							function OpenMlinks', $message['counter'], '() {
								document.getElementById("mlinksbody', $message['counter'], '").style.display="block";
								document.getElementById("mlinkshead', $message['counter'], '").style.background="url(http://sep7agon.net/images/mlinkshead.png) #111111 no-repeat bottom center";
								document.getElementById("mlinkshead', $message['counter'], '").onclick=function(){CloseMlinks', $message['counter'], '();};
							}
	
							function CloseMlinks', $message['counter'], '() {
								document.getElementById("mlinksbody', $message['counter'], '").style.display="none";
								document.getElementById("mlinkshead', $message['counter'], '").style.background="url(http://sep7agon.net/images/mlinkshead.png) #111111 no-repeat top center";
								document.getElementById("mlinkshead', $message['counter'], '").onclick=function(){OpenMlinks', $message['counter'], '();};
							}
							</script>';

		// Show the post itself, finally!
		if ($colortext != 'default')
			echo '<span style="color:', $colortext,'">';
			
		echo '
							<div class="post">';
							if ($colortext != 'default')
								echo '</span>';

		if (!$message['approved'] && $message['member']['id'] != 0 && $message['member']['id'] == $context['user']['id'])
			echo '
								<div class="approve_post">
									', $txt['post_awaiting_approval'], '
								</div>';
							
//ban

		if (!$message['approved'] && $message['member']['id'] != 0 && $message['member']['id'] == $context['user']['id'])
			echo '
								<div class="approve_post">
									', $txt['post_awaiting_approval'], '
								</div>';
		
		if ($banflag != 'true' && $ghostflag != 'true')
		{
		echo '
								<div class="inner" id="msg_', $message['id'], '"', '>', $message['body'], '</div>
							</div>';
		}
		if ($banflag == 'true')
		{
		echo '
								<div class="inner" id="msg_', $message['id'], '"', '><span style="color:#FF0000"><i>This user has been blacklisted from posting on the forums. Until the blacklist is lifted, all posts made by this user have been hidden.</i></span></div>
							</div>';
		}
		
		if ($ghostflag == 'true')
		{
		echo '
								<div class="inner" id="msg_', $message['id'], '"', '><span style="color:#8560a8"><i>This user\'s account has been deleted and their posts have been hidden.</i></span></div>
							</div>';
		}

		// Can the user modify the contents of this post?  Show the modify inline image.

		// Assuming there are attachments...
		if (!empty($message['attachment']))
		{
			echo '
							<div id="msg_', $message['id'], '_footer" class="attachments smalltext">
								<div style="overflow: ', $context['browser']['is_firefox'] ? 'visible' : 'auto', ';">';

			$last_approved_state = 1;
			foreach ($message['attachment'] as $attachment)
			{
				// Show a special box for unapproved attachments...
				if ($attachment['is_approved'] != $last_approved_state)
				{
					$last_approved_state = 0;
					echo '
									<fieldset>
										<legend>', $txt['attach_awaiting_approve'];

					if ($context['can_approve'])
						echo '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';

					echo '</legend>';
				}

				if ($attachment['is_image'])
				{
					if ($attachment['thumbnail']['has_thumb'])
						echo '
										<a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a><br />';
					else
						echo '
										<img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/><br />';
				}
				echo '
										<a href="' . $attachment['href'] . '"><img src="' . $settings['images_url'] . '/icons/clip.gif" align="middle" alt="*" />&nbsp;' . $attachment['name'] . '</a> ';

				if (!$attachment['is_approved'] && $context['can_approve'])
					echo '
										[<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
				echo '
										(', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' - ' . $txt['attach_viewed'] : ' - ' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.)<br />';
			}

			// If we had unapproved attachments clean up.
			if ($last_approved_state == 0)
				echo '
									</fieldset>';

			echo '
								</div>
							</div>';
		}

		echo '
						</div>
						<div class="moderatorbar">
							<div class="smalltext modified" id="modified_', $message['id'], '">';

		// Show "� Last Edit: Time by Person �" if this post was edited.
		if ($settings['show_modify'] && !empty($message['modified']['name']))
			echo '<em>', $txt['last_edit'], ': ', $message['modified']['time'], ' ', $txt['by'], ' ', $message['modified']['name'], '</em>';

		echo '</div>';
							
							echo '<br style="clear:both;" /><div style="float: left;">';
								echo '<div class="flow_hidden">
								<div class="keyinfo">
									<div class="smalltext" style="opacity: 0.5;"><a style="color: #cdcdcd;" opacity: 0.5;" href="', $message['href'], '" rel="nofollow">', $message['time'], '</a></div>
								</div>';
							echo '</div>';
							//Let's do Like posts!
			//This first bit is useful later on for calculating trust ranks better...
			/*if(!empty($modSettings['like_post_enable']) && LP_isAllowedTo(array('can_view_likes', 'can_view_likes_in_posts'))) {
				$total_likes = isset($context['like_posts']['posters_data'][$message['member']['id']]) ? $context['like_posts']['posters_data'][$message['member']['id']] : 0;

				echo '
								<li class="postcount"><a href="', $scripturl ,'?action=profile;area=likeposts;sa=seeotherslikes;u=', $message['member']['id'], '">', $txt['like_post_total_likes'] . ': ' . $total_likes, '</a></li>';
			}*/
			
			if(!empty($modSettings['like_post_enable']) && LP_isAllowedTo(array('can_like_posts', 'can_view_likes', 'can_view_likes_in_posts'))) {
			
			//echo '<br />';
			
			$context['like_posts']['single_msg_data'] = LP_isPostLiked($context['like_posts']['msgs_liked_data'], $message['id']);

			echo '<div class="like_post_box floatleft">';
			if(!$message['is_message_author'] && LP_isAllowedTo('can_like_posts')) {
				echo '
							<a class="like_unlike_link" id="like_',$message['id'],'" href="#', $context['like_posts']['single_msg_data']['already_liked'], '" onclick="lpObj.likeUnlikePosts(event,', $message['id'],', ',$context['current_topic'],', ',$context['current_board'],', ',$message['member']['id'],'); return false;">', $context['like_posts']['single_msg_data']['text'] ,'</a>';
							
							//$context['like_posts']['single_msg_data']['text']
			}

			if(LP_isAllowedTo(array('can_view_likes', 'can_view_likes_in_posts'))) {
				echo '
							<a id="like_post_info_'. $message['id'] .'" href="javascript:void(0)" onclick="lpObj.showMessageLikedInfo(', $message['id'], ')">
								', !empty($context['like_posts']['single_msg_data']['count']) ? '<span id="like_count_'. $message['id'] .'">' . $context['like_posts']['single_msg_data']['count_text'] . '</span>' : '', '
							</a>';
							
							//('. $context['like_posts']['single_msg_data']['count_text'] .')
			}
			echo '</div>';
		}
							echo '</div>';
							echo '<div style="float: right">';
							echo '<section class="bungie_links">';
							
							/*echo '<a class="bungie_buttons" href="#" target="_self">report</a>';
							if ($message['can_modify'])
								echo '<a class="bungie_buttons" href="#" target="_self">edit</a>';
							echo '<a class="bungie_buttons" href="#" target="_self">reply</a>';
							if ($context['can_quote'] && !empty($options['display_quick_reply']) && ($banflag != 'true'))
								echo '<a class="bungie_buttons" href="#" target="_self">quote</a>';*/
								
							if ($banflag2 != 'true') {

							if ($message['can_approve'])

							echo '<a class="bungie_buttons" href="', $scripturl, '?action=moderate;area=postmod;sa=approve;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context

							['session_id'], '">approve</a>';
							
							if ($context['can_moderate_forum'] && $adminflag == false)

							echo '<a class="bungie_buttons" href="', $scripturl, '?action=admin;area=ban;sa=add;u=', $message['member']['id'], '">judge</a>';

							else if (($context['can_report_moderator']) && ($adminflag == false))

							echo '<a class="bungie_buttons" href="', $scripturl, '?action=reporttm;topic=', $context['current_topic'], '.', $message['counter'], ';msg=', $message['id'], '">report</a>';
							
							if ($message['can_modify'])

							echo '<a class="bungie_buttons" href="', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], '">edit</a>';
							
							if ($context['can_quote'] && ($banflag != 'true'))
							echo '<a class="bungie_buttons" href="', $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';last_msg=' . $context['topic_last_message'], 'active">reply</a>';
							

							if ($context['can_quote'] && !empty($options['display_quick_reply']) && ($banflag != 'true'))

							echo '<a class="bungie_buttons" href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], '" onclick="return 

							oQuickReply.quote(', $message['id'], ');">quote</a>';


							elseif ($context['can_quote'] && ($banflag != 'true'))
							echo '<a class="bungie_buttons" href="', $scripturl, '?action=post;quote=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';last_msg=', $context['topic_last_message'], 

							'">quote</a>';

							/*if ($message['can_remove'])

							echo '<a class="bungie_buttons" href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" 

							onclick="return confirm(\'', $txt['remove_message'], '?\');">delete</a>';
							
							if ($context['can_split'] && !empty($context['real_num_replies']))
							echo '<a class="bungie_buttons" href="', $scripturl, '?action=splittopics;topic=', $context['current_topic'], '.0;at=', $message['id'], '">split</a>';*/
							}
								
							echo '<br /></section>';

							echo '<div class="smalltext reportlinks">';
							
		// Maybe they want to report this post to the moderator(s)?
		echo '
								<img src="', $settings['images_url'], '/ip.gif" alt="" />';

		// Show the IP to this user for this post - because you can moderate?
		if (($context['can_moderate_forum'] && !empty($message['member']['ip']) && ($context['current_board'] != 8)) || ($context['user']['is_admin']))
			echo '
								<a href="', $scripturl, '?action=', !empty($message['member']['is_guest']) ? 'trackip' : 'profile;area=tracking;sa=ip;u=' . $message['member']['id'], ';searchip=', $message['member']['ip'], '">', $message['member']['ip'], '</a> <a href="', $scripturl, '?action=helpadmin;help=see_admin_ip" onclick="return reqWin(this.href);" class="help">(?)</a>';
		// Or, should we show it because this is you?
		elseif ($message['can_see_ip'] && ($context['current_board'] != 8))
			echo '
								<a href="', $scripturl, '?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">', $message['member']['ip'], '</a>';
		// Okay, are you at least logged in?  Then we can show something about why IPs are logged...
		elseif (!$context['user']['is_guest'])
			echo '
								<a href="', $scripturl, '?action=helpadmin;help=see_member_ip" onclick="return reqWin(this.href);" class="help">', $txt['logged'], '</a>';
		// Otherwise, you see NOTHING!
		else
			echo '
								', $txt['logged'];

		echo '
							</div>';
							
		if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $message['can_remove'])
			echo '<div class="inline_mod_check" style="float:right; margin: -17px 0 0 2px; display: none;" id="in_topic_mod_check_', $message['id'], '"></div>';
			
			echo '</div><div style="clear:both;"></div>'; //End float right...

		// Are there any custom profile fields for above the signature?
		if (!empty($message['member']['custom_fields']))
		{
			$shown = false;
			foreach ($message['member']['custom_fields'] as $custom)
			{
				if ($custom['placement'] != 2 || empty($custom['value']))
					continue;
				if (empty($shown))
				{
					$shown = true;
					echo '
							<div class="custom_fields_above_signature">
								<ul class="reset nolist">';
				}
				echo '
									<li>', $custom['value'], '</li>';
			}
			if ($shown)
				echo '
								</ul>
							</div>';
		}

		

		// Show the member's signature?
		/*if (!empty($message['member']['signature']) && empty($options['show_no_signatures']) && $context['signature_enabled'])
			echo '<div class="signature" id="msg_', $message['id'], '_signature">', $message['member']['signature'], '</div>';*/

		echo '
						</div>
					</div>
					<span class="botslice"><span></span></span>';
					
					if ($ignoring && $adminflag == false && $modflag == false) {
						echo '</div></div></section>';
					}
					
				echo '</div>
				<hr class="post_separator" />';
	}

	// If the replies are hidden, let the guest know.
	if (isset($modSettings['hideTopicReplies']) && $modSettings['hideTopicReplies'] && $context['user']['is_guest'] && $context['num_replies'] > 0)
		echo '
					<div class="windowbg2">
						<span class="topslice"><span><!-- // --></span></span>
						<div class="content alert" style="text-align: center;">' . sprintf($txt['hideTopicReplies_notify'], $context['num_replies'], $context['num_replies'] != 1 ? $smcFunc['strtolower']($txt['replies']) : $smcFunc['strtolower']($txt['reply'])) . '</div>
						<span class="botslice"><span><!-- // --></span></span>
					</div>';

	echo '
				</form>
			</div>
			<a id="lastPost"></a>';

	// Show the page index... "Pages: [1]".
	echo '
			<div class="pagesection">
				', template_button_strip($normal_buttons, 'right'), '
				<div class="pagelinks floatright">', $txt['pages'], ': ', $context['page_index'], !empty($modSettings['topbottomEnable']) ? $context['menu_separator'] . ' &nbsp;&nbsp;<a href="#top"><strong>' . $txt['go_up'] . '</strong></a>' : '', '</div>
				<div class="nextlinks_bottom">', $context['previous_next'], '</div>
			</div>';

	// Show the lower breadcrumbs.
	theme_linktree();

	$mod_buttons = array(
		'move' => array('test' => 'can_move', 'text' => 'move_topic', 'image' => 'admin_move.gif', 'lang' => true, 'url' => $scripturl . '?action=movetopic;topic=' . $context['current_topic'] . '.0'),
		'delete' => array('test' => 'can_delete', 'text' => 'remove_topic', 'image' => 'admin_rem.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['are_sure_remove_topic'] . '\');"', 'url' => $scripturl . '?action=removetopic2;topic=' . $context['current_topic'] . '.0;' . $context['session_var'] . '=' . $context['session_id']),
		'lock' => array('test' => 'can_lock', 'text' => empty($context['is_locked']) ? 'set_lock' : 'set_unlock', 'image' => 'admin_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lock;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'sticky' => array('test' => 'can_sticky', 'text' => empty($context['is_sticky']) ? 'set_sticky' : 'set_nonsticky', 'image' => 'admin_sticky.gif', 'lang' => true, 'url' => $scripturl . '?action=sticky;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
		'merge' => array('test' => 'can_merge', 'text' => 'merge', 'image' => 'merge.gif', 'lang' => true, 'url' => $scripturl . '?action=mergetopics;board=' . $context['current_board'] . '.0;from=' . $context['current_topic']),
		'calendar' => array('test' => 'calendar_post', 'text' => 'calendar_link', 'image' => 'linktocal.gif', 'lang' => true, 'url' => $scripturl . '?action=post;calendar;msg=' . $context['topic_first_message'] . ';topic=' . $context['current_topic'] . '.0'),
	);

	// Restore topic. eh?  No monkey business.
	if ($context['can_restore_topic'])
		$mod_buttons[] = array('text' => 'restore_topic', 'image' => '', 'lang' => true, 'url' => $scripturl . '?action=restoretopic;topics=' . $context['current_topic'] . ';' . $context['session_var'] . '=' . $context['session_id']);

	// Allow adding new mod buttons easily.
	call_integration_hook('integrate_mod_buttons', array(&$mod_buttons));

	echo '
			<div id="moderationbuttons">', template_button_strip($mod_buttons, 'bottom', array('id' => 'moderationbuttons_strip')), '</div>';

	// Show the jumpto box, or actually...let Javascript do it.
	/*echo '
			<div class="plainbox" id="display_jump_to">&nbsp;</div>';*/

	if ($context['can_reply'] && !empty($options['display_quick_reply']))
	{
		echo '
			<a id="quickreply"></a>
			<div class="tborder" id="quickreplybox">
				<div class="cat_bar">
					<h3 class="catbg">
						<span class="ie6_header floatleft"><a href="javascript:oQuickReply.swap();">
							<img src="', $settings['images_url'], '/', $options['display_quick_reply'] == 2 ? 'collapse' : 'expand', '.gif" alt="+" id="quickReplyExpand" class="icon" />
						</a>
						<a href="javascript:oQuickReply.swap();">', $txt['quick_reply'], '</a>
						</span>
					</h3>
				</div>
				<div id="quickReplyOptions"', $options['display_quick_reply'] == 2 ? '' : ' style="display: none"', '>
					<span class="upperframe"><span></span></span>
					<div class="roundframe">
						<p class="smalltext lefttext">', $txt['quick_reply_desc'], '</p>
						', $context['is_locked'] ? '<p class="alert smalltext">' . $txt['quick_reply_warning'] . '</p>' : '',
						$context['oldTopicError'] ? '<p class="alert smalltext">' . sprintf($txt['error_old_topic'], $modSettings['oldTopicDays']) . '</p>' : '', '
						', $context['can_reply_approved'] ? '' : '<em>' . $txt['wait_for_approval'] . '</em>', '
						', !$context['can_reply_approved'] && $context['require_verification'] ? '<br />' : '', '
						<form action="', $scripturl, '?board=', $context['current_board'], ';action=post2" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" onsubmit="submitonce(this);" style="margin: 0;">
							<input type="hidden" name="topic" value="', $context['current_topic'], '" />
							<input type="hidden" name="subject" value="', $context['response_prefix'], $context['subject'], '" />
							<input type="hidden" name="icon" value="xx" />
							<input type="hidden" name="from_qr" value="1" />
							<input type="hidden" name="notify" value="', $context['is_marked_notify'] || !empty($options['auto_notify']) ? '1' : '0', '" />
							<input type="hidden" name="notify_alerts" value="', $context['is_marked_notify_alerts'] || !empty($options['auto_notify_alerts']) ? '1' : '0', '" />
							<input type="hidden" name="not_approved" value="', !$context['can_reply_approved'], '" />
							<input type="hidden" name="goback" value="', empty($options['return_to_post']) ? '0' : '1', '" />
							<input type="hidden" name="last_msg" value="', $context['topic_last_message'], '" />
							<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
							<input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />';

			// Guests just need more.
			if ($context['user']['is_guest'])
				echo '
							<strong>', $txt['name'], ':</strong> <input type="text" name="guestname" value="', $context['name'], '" size="25" class="input_text" tabindex="', $context['tabindex']++, '" />
							<strong>', $txt['email'], ':</strong> <input type="text" name="email" value="', $context['email'], '" size="25" class="input_text" tabindex="', $context['tabindex']++, '" /><br />';

			// Is visual verification enabled?
			if ($context['require_verification'])
				echo '
							<strong>', $txt['verification'], ':</strong>', template_control_verification($context['visual_verification_id'], 'quick_reply'), '<br />';

			echo '
							<div class="quickReplyContent">
								<textarea cols="600" rows="7" name="message" tabindex="', $context['tabindex']++, '"></textarea>
							</div>
							<div align="center">
					', $txt['read_the_rules_text1'], ' <a href="', $modSettings['read_the_rules_link'], '" target="_blank">', $txt['read_the_rules_text2'], '</a> ', $txt['read_the_rules_text3'], '<br />
								<input type="submit" name="post" value="', $txt['post'], '" onclick="return submitThisOnce(this);" accesskey="s" tabindex="', $context['tabindex']++, '" class="button_submit" />
								<input type="submit" name="preview" value="', $txt['preview'], '" onclick="return submitThisOnce(this);" accesskey="p" tabindex="', $context['tabindex']++, '" class="button_submit" />';

			if ($context['show_spellchecking'])
				echo '
								<input type="button" value="', $txt['spell_check'], '" onclick="spellCheck(\'postmodify\', \'message\');" tabindex="', $context['tabindex']++, '" class="button_submit" />';

			echo '
							</div>
						</form>
					</div>
					<span class="lowerframe"><span></span></span>
				</div>
			</div>';
	}
	else
		echo '
		<br class="clear" />';

	if ($context['show_spellchecking'])
		echo '
			<form action="', $scripturl, '?action=spellcheck" method="post" accept-charset="', $context['character_set'], '" name="spell_form" id="spell_form" target="spellWindow"><input type="hidden" name="spellstring" value="" /></form>
				<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/spellcheck.js"></script>';

	echo '
				<script type="text/javascript" src="' . $settings['default_theme_url'] . '/scripts/topic.js"></script>
				<script type="text/javascript"><!-- // --><![CDATA[';

	if (!empty($options['display_quick_reply']))
		echo '
					var oQuickReply = new QuickReply({
						bDefaultCollapsed: ', !empty($options['display_quick_reply']) && $options['display_quick_reply'] == 2 ? 'false' : 'true', ',
						iTopicId: ', $context['current_topic'], ',
						iStart: ', $context['start'], ',
						sScriptUrl: smf_scripturl,
						sImagesUrl: "', $settings['images_url'], '",
						sContainerId: "quickReplyOptions",
						sImageId: "quickReplyExpand",
						sImageCollapsed: "collapse.gif",
						sImageExpanded: "expand.gif",
						sJumpAnchor: "quickreply"
					});';

	if (!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1 && $context['can_remove_post'])
		echo '
					var oInTopicModeration = new InTopicModeration({
						sSelf: \'oInTopicModeration\',
						sCheckboxContainerMask: \'in_topic_mod_check_\',
						aMessageIds: [\'', implode('\', \'', $removableMessageIDs), '\'],
						sSessionId: \'', $context['session_id'], '\',
						sSessionVar: \'', $context['session_var'], '\',
						sButtonStrip: \'moderationbuttons\',
						sButtonStripDisplay: \'moderationbuttons_strip\',
						bUseImageButton: false,
						bCanRemove: ', $context['can_remove_post'] ? 'true' : 'false', ',
						sRemoveButtonLabel: \'', $txt['quickmod_delete_selected'], '\',
						sRemoveButtonImage: \'delete_selected.gif\',
						sRemoveButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						bCanRestore: ', $context['can_restore_msg'] ? 'true' : 'false', ',
						sRestoreButtonLabel: \'', $txt['quick_mod_restore'], '\',
						sRestoreButtonImage: \'restore_selected.gif\',
						sRestoreButtonConfirm: \'', $txt['quickmod_confirm'], '\',
						sFormId: \'quickModForm\'
					});';

	echo '
					if (\'XMLHttpRequest\' in window)
					{
						var oQuickModify = new QuickModify({
							sScriptUrl: smf_scripturl,
							bShowModify: ', $settings['show_modify'] ? 'true' : 'false', ',
							iTopicId: ', $context['current_topic'], ',
							sTemplateBodyEdit: ', JavaScriptEscape('
								<div id="quick_edit_body_container" style="width: 90%">
									<div id="error_box" style="padding: 4px;" class="error"></div>
									<textarea class="editor" name="message" rows="12" style="' . ($context['browser']['is_ie8'] ? 'width: 635px; max-width: 100%; min-width: 100%' : 'width: 100%') . '; margin-bottom: 10px;" tabindex="' . $context['tabindex']++ . '">%body%</textarea><br />
									<input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
									<input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
									<input type="hidden" name="msg" value="%msg_id%" />
									<div class="righttext">
										<input type="submit" name="post" value="' . $txt['save'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifySave(\'' . $context['session_id'] . '\', \'' . $context['session_var'] . '\');" accesskey="s" class="button_submit" />&nbsp;&nbsp;' . ($context['show_spellchecking'] ? '<input type="button" value="' . $txt['spell_check'] . '" tabindex="' . $context['tabindex']++ . '" onclick="spellCheck(\'quickModForm\', \'message\');" class="button_submit" />&nbsp;&nbsp;' : '') . '<input type="submit" name="cancel" value="' . $txt['modify_cancel'] . '" tabindex="' . $context['tabindex']++ . '" onclick="return oQuickModify.modifyCancel();" class="button_submit" />
									</div>
								</div>'), ',
							sTemplateSubjectEdit: ', JavaScriptEscape('<input type="text" style="width: 90%;" name="subject" value="%subject%" size="80" maxlength="80" tabindex="' . $context['tabindex']++ . '" class="input_text" />'), ',
							sTemplateBodyNormal: ', JavaScriptEscape('%body%'), ',
							sTemplateSubjectNormal: ', JavaScriptEscape('<a href="' . $scripturl . '?topic=' . $context['current_topic'] . '.msg%msg_id%#msg%msg_id%" rel="nofollow">%subject%</a>'), ',
							sTemplateTopSubject: ', JavaScriptEscape($txt['topic'] . ': %subject% &nbsp;(' . $txt['read'] . ' ' . $context['num_views'] . ' ' . $txt['times'] . ')'), ',
							sErrorBorderStyle: ', JavaScriptEscape('1px solid red'), '
						});

						aJumpTo[aJumpTo.length] = new JumpTo({
							sContainerId: "display_jump_to",
							sJumpToTemplate: "<label class=\"smalltext\" for=\"%select_id%\">', $context['jump_to']['label'], ':<" + "/label> %dropdown_list%",
							iCurBoardId: ', $context['current_board'], ',
							iCurBoardChildLevel: ', $context['jump_to']['child_level'], ',
							sCurBoardName: "', $context['jump_to']['board_name'], '",
							sBoardChildLevelIndicator: "==",
							sBoardPrefix: "=> ",
							sCatSeparator: "-----------------------------",
							sCatPrefix: "",
							sGoButtonLabel: "', $txt['go'], '"
						});

						aIconLists[aIconLists.length] = new IconList({
							sBackReference: "aIconLists[" + aIconLists.length + "]",
							sIconIdPrefix: "msg_icon_",
							sScriptUrl: smf_scripturl,
							bShowModify: ', $settings['show_modify'] ? 'true' : 'false', ',
							iBoardId: ', $context['current_board'], ',
							iTopicId: ', $context['current_topic'], ',
							sSessionId: "', $context['session_id'], '",
							sSessionVar: "', $context['session_var'], '",
							sLabelIconList: "', $txt['message_icon'], '",
							sBoxBackground: "transparent",
							sBoxBackgroundHover: "#ffffff",
							iBoxBorderWidthHover: 1,
							sBoxBorderColorHover: "#adadad" ,
							sContainerBackground: "#ffffff",
							sContainerBorder: "1px solid #adadad",
							sItemBorder: "1px solid #ffffff",
							sItemBorderHover: "1px dotted gray",
							sItemBackground: "transparent",
							sItemBackgroundHover: "#e0e0f0"
						});
					}';

	if (!empty($ignoredMsgs))
	{
		echo '
					var aIgnoreToggles = new Array();';

		foreach ($ignoredMsgs as $msgid)
		{
			echo '
					aIgnoreToggles[', $msgid, '] = new smc_Toggle({
						bToggleEnabled: true,
						bCurrentlyCollapsed: true,
						aSwappableContainers: [
							\'msg_', $msgid, '_extra_info\',
							\'msg_', $msgid, '\',
							\'msg_', $msgid, '_footer\',
							\'msg_', $msgid, '_quick_mod\',
							\'modify_button_', $msgid, '\',
							\'msg_', $msgid, '_signature\'

						],
						aSwapLinks: [
							{
								sId: \'msg_', $msgid, '_ignored_link\',
								msgExpanded: \'\',
								msgCollapsed: ', JavaScriptEscape($txt['show_ignore_user_post']), '
							}
						]
					});';
		}
	}

	echo '
				// ]]></script>';
}

?>