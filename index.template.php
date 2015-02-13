<?php

// Current version
global $forumVersion;
$forumVersion = "2.2.0a";

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = false;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $boardurl, $forumVersion;

	// Show right to left and the character set for ease of translating.
	echo '<!doctype html>
<!--
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-=[January, 2015]-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
      __
   __/  \__		  ___          ____
  /  \__/  \	 / __| ___ _ _|__  |_ _ __ _ ___ _ _
  \__/  \__/	 \__ \/ -_) \'_ \/ / _` / _` / _ \ \'\
  /  \__/  \	 |___/\___| .__/_/\__,_\__, \___/_||_|
  \__/  \__/	          |_|          |___/
     \__/

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-[V '.$forumVersion.']=-=-=-=-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-->
<html lang="en"', $context['right_to_left'] ? ' dir="rtl"' : '', '>';
echo '<head>';
	echo '
		<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />';
	echo '
		<style type="text/css">
		/* Media settings START
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
		
		@media only screen and (min-width : 600px) {
			.spacemaker {
				clear:both; height: 100px;
			}

			table.table_list tbody.content td.stats {
				display: none !important;
			}

			table.table_list tbody.content td.lastpost {
				display: none !important;
			}
		}
		
		@media only screen and (max-width : 600px) {
			.spacemaker {
				clear:both; height: 100px;
			}
			
			nav {
				background: #111111;
				border-bottom: 1px solid #424242;
			}

			table.table_list tbody.content td.stats {
				display: none !important;
			}

			table.table_list tbody.content td.lastpost {
				display: none !important;
			}
		}
		
		/* 	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- 
			-=-=-=-=- Media Settings END -=-=-=-=--=-=-=-=--=-=-=-=--=-=-=-=--=-=-=-=- */
		
		/* Board settings START
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- */
		
		.b_index {
			background: url("',$settings['theme_url'],'/data/img/boards/b_index.png") no-repeat left top;
		}

		.b_flood {
			background: url("',$settings['theme_url'],'/data/img/boards/b_flood.png") no-repeat left top;
		}

		.b_serious {
			background: url("',$settings['theme_url'],'/data/img/boards/b_serious.png") no-repeat left top;
		}

		.b_gaming {
			background: url("',$settings['theme_url'],'/data/img/boards/b_gaming.png") no-repeat left top;
		}

		.b_support {
			background: url("',$settings['theme_url'],'/data/img/boards/b_support.png") no-repeat left top;
		}

		.b_updates {
			background: url("',$settings['theme_url'],'/data/img/boards/b_updates.png") no-repeat left top;
		}

		.b_art {
			background: url("',$settings['theme_url'],'/data/img/boards/b_art.png") no-repeat left top;
		}

		.b_anarchy {
			background: url("',$settings['theme_url'],'/data/img/boards/b_rapture.png") no-repeat left top;
		}

		.b_hq {
			background: url("',$settings['theme_url'],'/data/img/boards/b_hq.png") no-repeat left top;
		}

		.banner {
			text-decoration: none;
			width: 100%;
			height: 100px;
			margin: 0 auto;
			padding: auto;
			float: left;
		}

		.banner:hover {
			text-decoration: none;
		}

		#alerts_image.alerts_full {
			background-image: url("'.$settings['theme_url'].'/images/alerts/alerts.png");
		}

		#alerts_image.alerts_hide {
			background-image: url("'.$settings['theme_url'].'/images/alerts/alerts-inactive.png");
		}

		/* 	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			-=-=-=-=- Board Settings END -=-=-=-=--=-=-=-=--=-=-=-=--=-=-=-=--=-=-=-=- */
	</style>';

	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/data/css/index', $context['theme_variant'], '.css?',date('ddmmyyyytt'),'" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css?',date('ddmmyyyytt'),'" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/data/css/rtl.css?',date('ddmmyyyytt'),'" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/data/js/theme.js"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	include('catchphrase.php');

	if ($context['page_title_html_safe'] == 'Sep7agon')
		$pagetitle = 'Sep7agon | ' . $outputphrase;
	else
		$pagetitle = $context['page_title_html_safe'];

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $pagetitle, '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	echo '
	<!-- Automatic HTML headers start here -->
	';
	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo'
	<!-- Automatic HTML headers end here -->
	';
	// Sep7agon v2.0.0 style changes
	echo '
	<link rel="icon" type="image/ico" href="',$settings['theme_url'],'/favicon.ico" />';
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/data/css/dropit.css?',date('ddmmyyyytt'),'" />';
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/data/css/master.css?',date('ddmmyyyytt'),'" />';
	echo '
	<script type="text/javascript" src="',$settings['theme_url'],'/data/js/jquery-1.7.2.min.js"></script>';
	echo '
	<script type="text/javascript" src="', $settings['theme_url'], '/data/js/dropit.js"></script>';
	echo '
	<script type="text/javascript" src="', $settings['theme_url'], '/data/js/jquery.hoverIntent.minified.js"></script>';
	echo '
</head>
<body>';
include_once("analyticstracking.php");
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $boardurl, $user_info, $forumVersion;
	//echo $context['tapatalk_body_hook'];

if ($context['current_topic'] >= 1)
	$isthread = 'true';
else
	$isthread = 'false';

$anarchy = $context['theme_settings']['anarchy'];
$forumDev =  $context['theme_settings']['devmode'];
    
if ($_GET['action'] || $_GET['board'] || $_GET['topic'] || $_GET['page'])
    $frontpage = false;
else
    $frontpage = true;

// Header, and logo
echo '
	<div id="container">
	<header>
		<div id="headerContainer">


			<div id="branding">
				<a href="'.$boardurl.'" onclick="$(\'.small-userct\').show();">sep7agon</a>
			</div>';

			// Navigation
			echo '
		<nav>
			<ul id="navMenu">';
			if($context['current_board'] == null) {
				$banner='b_index';
				$b_href= $boardurl;
			}
    
            // Index
			echo '<li><a';
			if ($_GET['action'] == 'forum') {
				echo ' class="current_b"';
				$b_href= '?action=forum';
				$banner='b_index';
			}
			echo ' href="',$boardurl,'/index.php?action=forum">List</a></li>';

			// News
			echo '<li><a';
			if ($context['current_board'] == 5) {
				echo ' class="current_b"';
				$b_href= '?board=5.0';
				$banner='b_updates';
			}
			echo ' href="',$boardurl,'/index.php?board=5.0">News</a></li>';

			// The Flood
			echo '<li><a';
			if ($context['current_board'] == 1) {
				echo ' class="current_b"';
				$b_href= '?board=1.0';
				$banner='b_flood';
			}
			echo ' href="',$boardurl,'/index.php?board=1.0">Flood</a></li>';

			// Serious
			echo '<li><a';
			if ($context['current_board'] == 6) {
				echo ' class="current_b"';
				$b_href= '?board=6.0';
				$banner='b_serious';
			}
			echo ' href="',$boardurl,'/index.php?board=6.0">Serious</a></li>';

			// Gaming
			echo '<li><a';
			if ($context['current_board'] == 4) {
				echo ' class="current_b"';
				$b_href= '?board=4.0';
				$banner='b_gaming';
			}
			echo ' href="',$boardurl,'/index.php?board=4.0">Gaming</a></li>';

			// Anarchy

			if ($context['allow_admin'] && $anarchy && (in_array(1,$user_info['groups']) ||
					in_array(2,$user_info['groups']) || in_array(36,$user_info['groups']) ||
						in_array(63,$user_info['groups']) || in_array(69,$user_info['groups']) || in_array(74,$user_info['groups']))) {
				echo '<li><a';
				if ($context['current_board'] == 8) {
					echo ' class="current_b"';
					$b_href= '?board=8.0';
					$banner='b_anarchy';
				}
				echo ' href="',$boardurl,'/index.php?board=8.0">Anarchy</a></li>';
			}

			// Septagon
			echo '<li><a';
			if ($context['current_board'] == 3) {
				echo ' class="current_b"';
				$b_href= '?board=3.0';
				$banner='b_support';
			}
			echo ' href="',$boardurl,'/index.php?board=3.0">Septagon</a></li>';

			// HQ
			if ($context['allow_admin'] || in_array(2,$user_info['groups']) || in_array(74,$user_info['groups']) ||  in_array(36,$user_info['groups']) || in_array(63,$user_info['groups'])) {
				echo '<li><a';
				if ($context['current_board'] == 2) {
					echo ' class="current_b"';
					$b_href= '?board=2.0';
					$banner='b_hq';
				}
				echo ' href="',$boardurl,'/index.php?board=2.0">HQ</a></li>';
			}
			echo '
			</ul>';
			echo '
			<ul id="smallNavMenu">';
				echo '<li class="menuBtn"><a class="menuBtnLink" href="#">Menu</a>';
				echo '<ul id="mobileMenu">';
                echo '<li><a href="'.$boardurl.'/index.php?action=forum">List</a></li>';
				echo '<li><a href="'.$boardurl.'/index.php?board=5.0">News</a></li>';
				echo '<li><a href="'.$boardurl.'/index.php?board=1.0">The Flood</a></li>';
				echo '<li><a href="'.$boardurl.'/index.php?board=6.0">Serious</a></li>';
				echo '<li><a href="'.$boardurl.'/index.php?board=4.0">Gaming</a></li>';
				if ($context['user']['is_logged'] && $anarchy && (in_array(2,$user_info['groups']) || in_array(36,$user_info['groups']) || in_array(63,$user_info['groups']) || in_array(69,$user_info['groups'])))
					echo '<li><a href="'.$boardurl.'/index.php?board=8.0">Anarchy</a></li>';

				echo '<li><a href="'.$boardurl.'/index.php?board=3.0">Septagon</a></li>';
				if ($context['allow_admin'] || in_array(2,$user_info['groups']) || in_array(74,$user_info['groups']) || in_array(36,$user_info['groups']) || in_array(63,$user_info['groups']))
					echo '<li><a href="'.$boardurl.'/index.php?board=2.0">HQ</a></li>';
				if (!$context['user']['is_logged']) {
					echo '<li><a href="'.$boardurl.'/index.php?action=login">Login</a></li>';
					echo '<li><a href="'.$boardurl.'/index.php?action=register">Sign up</a></li>';
				}
				echo '</ul></li>';
			echo '
			</ul>';
		echo '
		</nav>';
		// Search
		echo '
		<div id="searchTool">
			<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
				<div id="searchTextfield">
					<input id="searchSelField" type="text" name="search" placeholder="Search…" />
					<a href="?action=search">Advanced Search</a>
					<a href="?action=mlist;sa=search">Search Members</a>
				</div>
				<input id="searchMag" type="submit" value="" />';
				// Search within current topic?
				if (!empty($context['current_topic']))
				echo '
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />';

				// If we're on a certain board, limit it to this board ;).
				elseif (!empty($context['current_board']))
				echo '
					<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';
				echo '
			</form>
		</div>';
		// User profile
		// Find whether the user is logged in or not.
		if($context['user']['is_logged']) {
			$showLogInSuggestion = false;
			$showNotifications = true;
			$showAvatar = true;
		} else {
			$showLogInSuggestion = true;
			$showNotifications = false;
			$showAvatar = false;
		}

		echo '
		<div id="userInfoPane">';
		if($showNotifications) {
		echo '
			<div id="notification">',function_exists("show_alerts") && show_alerts(),'</div>';
		}

		if($showAvatar) {
			// Avatar toolbar
			echo "
			<script type=\"text/javascript\">
			$(document).ready(function () {
				var timeOutAvatar;

				$(\"#avatarControls\").mouseenter(function () {
					if ($(\"#avatarToolbar\").css(\"display\") == \"none\") {
						showAvatarMenu();
						// Hide alerts menu
						$('#alerts').hide();
						// Hide other menus
						$('.dropit-submenu').hide();
						hideSearchTooltip();
					}
				});

				$(\"#avatarControls\").mouseleave(function () {
					if ($(\"#avatarToolbar\").hasClass(\"menu-open\")) {
						timeOutAvatar = window.setTimeout(fadeAvatarMenu, 50);
						$(\"#avatarToolbar\").mouseenter(function () { window.clearTimeout(timeOutAvatar) });
						$(\"#avatarToolbar\").mouseleave(function () { fadeAvatarMenu() });
					}
				});

				function showAvatarMenu() {
					$(\"#avatarToolbar\").show();
					$(\"#avatarToolbar\").addClass(\"menu-open\");
				}

				function fadeAvatarMenu() {
					$(\"#avatarToolbar\").hide();
					$(\"#avatarToolbar\").removeClass(\"menu-open\");
				}
			});
			</script>
			<div id=\"avatarToolbar\">";
			echo '<div id="avatarContainer">';
			if ($context['user']['avatar']['image']!=null) {
				echo '<div id="avatarBig" style="background-image: url(\''.$context['user']['avatar']['href'].'\')"> </div>';
			} else {
				echo '<div id="avatarBig" style="background-image: url(\''.$settings['theme_url'].'/data/img/MonkeyAvatar.png\')"> </div>';
			}
			echo '<h4 class="username"><a class="userProfileLink" href="'.$boardurl.'?action=profile">'.$context['user']['name'].'</a></h4>';
			echo '<p class=usermotto"><a class="editProfile" href="'.$boardurl.'?action=profile;area=forumprofile">Edit profile…</a></p>';
			echo '</div>';
			template_menu();
			echo "</div>";
			if ($context['user']['avatar']['image']!=null) {
				echo '<div id="avatar" style="background-image: url(\'',$context['user']['avatar']['href'],'\');">';
			} else {
				echo '<div id="avatar" style="background-image: url(\'',$settings['theme_url'],'/data/img/MonkeyAvatar.png\');">';
			}

			echo '<a id="avatarControls" href="'.$boardurl.'?action=profile">'.$context['user']['name'].'</a>';
			echo ' </div>';
		}
			echo '
		</div>';



			echo '
		<div id="userControls">';

		if($showLogInSuggestion) {
			echo '
                <div id="NotLoggedIn">
                    Welcome, guest! Please <a href="index.php?action=login">login</a> or
                    <a href="index.php?action=register">register</a>!
                </div>
			';
		} else {
			//template_menu();
			echo '
			<ul id="usrCommandMenu">';

				// Display PM icon
				pmIcon();

				// Display admin icon
				if ($context['user']['is_admin']) {
					adminIcon();
				}

				// Display mod icon
				if ($context['allow_admin'] || in_array(2,$user_info['groups']) || in_array(36,$user_info['groups']) || in_array(63,$user_info['groups'])) {
					modIcon();
				} echo '
			</ul>';
		}
		echo '</div>
		</div>';
		if ($forumDev) {
			echo '
						<div id="forumTestVersion">
							<div id="forumTestVersionWrap">
								<p>REL. '.$forumVersion.'</p>
							</div>
						</div>
					';
		}
		echo '
	</header>';

	echo '
    <script type="text/javascript">
        $(document).ready(function () {
        	$(\'#NotLoggedIn\').show();

            if ($("#userControls ul li.menuOption").length == 1 && $("#navMenu li").length > 5) {
            	$("#navMenu li").css("padding", "0px 26px"); // Normal member with anarchy
            } else if ($("#userControls ul li.menuOption").length == 1 && $("#navMenu li").length == 5) {
            	$("#navMenu li").css("padding", "0px 36px"); // Normal member, no extra forums
            } else if ($("#userControls ul li.menuOption").length > 2 && $("#navMenu li").length > 6) {
            	$("#navMenu li").css("padding", "0px 14px"); // Admin with anarchy board
            } else if ($("#userControls ul li.menuOption").length > 2 && $("#navMenu li").length == 6) {
            	$("#navMenu li").css("padding", "0px 20px"); // Admin without Anarchy board
            } else if ($("#userControls ul li.menuOption").length == 2 && $("#navMenu li").length == 6) {
            	$("#navMenu li").css("padding", "0px 18px"); // Moderator with HQ
            } else if ($("#userControls ul li.menuOption").length == 2 && $("#navMenu li").length > 6) {
				$("#navMenu li").css("padding", "0px 18px"); // Moderator with HQ and Anarchy
            } else if ($("#userControls ul li.menuOption").length == 0) {
            	$("#navMenu li").css("padding", "0px 20px"); // Guest
            }


            $(\'#usrCommandMenu\').dropit({action: \'hover\'});
            $(\'#smallNavMenu\').dropit({action: \'click\'});

            $("\'[placeholder]\'").focus(function() {
                var input = $(this);
                if (input.val() == input.attr("\'placeholder\'")) {
                    input.val("\'\'");
                    input.removeClass("\'placeholder\'");
                }
            }).blur(function() {
                var input = $(this);
                if (input.val() == "\'\'" || input.val() == input.attr("\'placeholder\'")) {
                    input.addClass("\'placeholder\'");
                    input.val(input.attr("\'placeholder\'"));
                }
            }).blur();

            // Check size of the username under avatar
            if ($("#avatarToolbar h4 a.userProfileLink").text().length > 12 && $("#avatarToolbar h4 a.userProfileLink").text().length <= 14) {
				$("#avatarToolbar h4 ").css("font-size", $("#avatarToolbar h4 a.userProfileLink").text().length*0.9+"px");
            } else if ($("#avatarToolbar h4 a.userProfileLink").text().length > 14 && $("#avatarToolbar h4 a.userProfileLink").text().length <=16) {
				$("#avatarToolbar h4 ").css("font-size", $("#avatarToolbar h4 a.userProfileLink").text().length*0.7+"px");
            } else if ($("#avatarToolbar h4 a.userProfileLink").text().length > 16) {
            	$("#avatarToolbar").css("width", "300px");
            	$("#avatarToolbar h4 ").css("font-size", "12px");
            	$("#avatarToolbar p").css("width", "160px");
            }

            // Alerts image override
            $("#alerts_image").attr("src", "'.$settings['theme_url'].'/images/alerts/TheNotificationsSystemIsPoorlyWritten.png");

            // Hide search dropdown on click
            $(document).mousedown(function(e) {
            	if(!$("#searchTool *").is(e.target)) {
            		searchTimeOut = window.setTimeout(hideSearchTooltip, 50);
            	}
            });

            // Have quick reply ready by default
            try {
            	oQuickReply.swap();
            } catch (e) {
            	console.log("> implying quick reply should be here");
            }
    });

    // Search drop down
	var searchTimeOut;

	// The searchbar...
	$("input#searchMag").mouseenter(function () {
    	$("#searchTextfield").show();
    });

    $("input#searchMag").mouseleave(function () {
    	searchTimeOut = window.setTimeout(hideSearchTooltip, 50);
    });


    // window.clearTimeout(timeOut)
    $("div#searchTextfield").mouseenter(function () {
    	window.clearTimeout(searchTimeOut)
    	$("input#searchMag").addClass("checking-search");
    	// Hide alerts and avatar menu
		$("#avatarToolbar").hide();
		$("#alerts").hide();
    });

    $("div#searchTextfield").mouseleave(function () {
    	if ($("#searchSelField").val().length == 0) {
    		hideSearchTooltip();
    	}
    });


    $("div#searchTextfield").bind("keyup", function () {
    	if ($("#searchSelField").val().length == 0) {
    		hideSearchTooltip();
    	}
	});

    function hideSearchTooltip () {
		$("#searchTextfield").hide();
		$("input#searchMag").removeClass("checking-search");
		window.clearTimeout(searchTimeOut);
    }

    </script>';

	echo '
<div class="spacemaker">&nbsp;</div>';
	// Script for mobile
	echo '<script type="text/javascript" src="'.$settings['theme_url'].'/data/js/mobile.js"></script>';
		echo '
		<div id="wrapper">';
            if (!$frontpage){
			echo '<a class="';
			echo $banner;
			echo ' banner" href="';
			echo $b_href;
			echo '" target="_self">&nbsp;</a>';
            }
            elseif ($frontpage) {
                include("fader-test.php");
            }
			echo '<div style="clear: both;"></div>
			<div id="topbar">';
	
			// Is the forum in maintenance mode?
			if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
				<span>', $txt['maintain_mode_on'], '</span>';
	
			// Are there any members waiting for approval?
			if (!empty($context['unapproved_members']))
			echo '
				<span>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</span>';
	
			if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
				<span><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></span>';		
									
		echo '							
			</div>';

		// Removed unused toolbar <div id="toolbar">

			echo '<div style="clear:both;"></div><div style="clear:both;"></div>';
				if ($context['current_board'] > 99) {
					echo '<div style="float: left; display: block; width: 99%; max-height: 120px; overflow: hidden;"><center>
						<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
						<!-- Sep7agon Responsive -->
						<ins class="adsbygoogle"
							 style="display:block"
							 data-ad-client="ca-pub-9422718038809839"
							 data-ad-slot="5402863603"
							 data-ad-format="auto"></ins>
						<script>
						(adsbygoogle = window.adsbygoogle || []).push({});
						</script>
					</div><div style="clear:both;"></div><div style="clear:both;"></center></div>';
				}
			if ($context['current_topic'] >= 1)
				echo '<div class="mainad" id="mainarea">';
			else
				echo '<div id="mainarea">';

			// Show board description
			if (!empty($options['show_board_desc']) && $context['description'] != '')
				echo '<p class="description_board">', $context['description'], '</p>';
			// Show the navigation tree.
            if (!$frontpage)
                theme_linktree();

}

function hasMessages() {
	global $context;

	if ($context['user']['unread_messages'] > 0) {
		return true;
	}

	return false;
}

function pmIcon() {
	global $settings, $boardurl;

	// Link to PM page
	$pmLink = $boardurl.'?action=pm';

	// Display PM icon
	echo '
	<li id="pmIcon" class="menuOption">
		<a class="';
	if (hasMessages()) {
		echo 'new-message" href="'.$pmLink.'" style="background-image: url(\''.$settings['theme_url'].'/data/img/newmail.png\')"> </a>';
	} else {
		echo 'no-message" href="'.$pmLink.'" style="background-image: url(\''.$settings['theme_url'].'/data/img/mail.png\')"> </a>';
	}
	echo '
		<ul>';
		// Generate the dropdown menu options
		displaySpecificMenu("pm");
		echo '
		</ul>';
	echo '
	</li>';
}

function adminIcon() {
	global $settings, $boardurl;

	// Display admin icon
	echo '<li id="adminIcon" class="menuOption">';

	// Display the icon as a link
	echo '<a class="showAdmin" href="'.$boardurl.'?action=admin" style="background-image: url(\''.$settings['theme_url'].'/data/img/tadmin.png\')"> </a>';

	// Get the admin submenu
	echo '<ul>';
	displaySpecificMenu("admin");
	echo '</ul>';
	echo '</li>';
}

function modIcon() {
	global $settings, $boardurl;

	// Display mod icon
	echo '<li id="modIcon" class="menuOption">';

	// Display the icon as a link
	echo '<a class="showMod" href="'.$boardurl.'?action=moderate" style="background-image: url(\''.$settings['theme_url'].'/data/img/tblox.png\')"> </a>';

	// Get the mod submenu
	echo '<ul>';
	displaySpecificMenu("moderate");
	echo '</ul>';
	echo '</li>';
}

function displaySpecificMenu($menu) {
	global $context, $settings, $options, $scripturl, $txt;
	foreach ($context['menu_buttons'] as $act => $button)
	{
		if ($act==$menu) {
			if (!empty($button['sub_buttons'])) {
				foreach ($button['sub_buttons'] as $childbutton) {
					echo '<li>
					<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' .
						$childbutton['target'] . '"' : '', '><span>', $childbutton['title'],
					!empty($childbutton['sub_buttons']) ? '...' : '', '</span></a>';
					echo '</li>';
				}
			}
		}
	}
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $boardurl, $forumVersion;

		echo '
			</div>';
		echo '
		</div>
			<div id="footer">
				<span class="copyrighttext">', theme_copyright(), '</span>'; /* Some retards force down their
 				copyright messages if we hide this. */
				// Our actual footer
				echo '
				<p class="copyNote"><a href="http://sep7agon.net/index.php?topic=6.0" target="_self">Check the Rules</a> <a href="http://sep7agon.net/index.php?topic=21782.0" target="_self">Patch notes</a>
				<a href="http://sep7agon.net/index.php?topic=8396.0" target="_self">About moderation</a><br/>
				&copy; Sep7agon.net, all rights reserved. [Version: '.$forumVersion.'] </p>
				';

				// Show the load time?
				/* Deprecated -- we never showed it
				if ($context['show_load_time'])
					echo '<span class="smalltext">'. $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</span>';
				*/
		echo '
			</div>';
		
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	</div><!--
--></body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' <img src="'.$settings['theme_url'].'/data/img/quote.png" />';

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}

// Small tool list for the footer
function footerSmall_menu() {
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<ul id="usrSettingMenu">
	';

	foreach ($context['menu_buttons'] as $act => $button) {
		echo '
				<li id="footerBtn_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', '" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '><span>', $button['title'], '</span></a>';

		if (!empty($button['sub_buttons'])) {
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton) {
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</a>';

				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons'])) {
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>', $grandchildbutton['title'], '</a>
								</li>';

					echo '
						</ul>';
				}

				echo '
						</li>';
			}
			echo '
					</ul>';
		}
		echo '
				</li>';
	}
	echo '
	</ul>';
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<ul id="usrAvatarMenu">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		if ($act=="profile") {
			if (!empty($button['sub_buttons'])) {
				foreach ($button['sub_buttons'] as $childbutton) {
					if ($childbutton['title']!=$context['user']['name']) {
						echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '><span>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span></a>';
						echo '</li>';
					}
				}
			}
		}
	}
	echo '
			</ul>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();
		
	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);
	
	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . 'left' : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>