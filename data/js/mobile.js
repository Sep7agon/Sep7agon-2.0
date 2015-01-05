/*
 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 -=[January, 2015]=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
      __
   __/  \__		  ___          ____
  /  \__/  \	 / __| ___ _ _|__  |_ _ __ _ ___ _ _
  \__/  \__/	 \__ \/ -_) '_ \/ / _` / _` / _ \ ' \
  /  \__/  \	 |___/\___| .__/_/\__,_\__, \___/_||_|
  \__/  \__/	          |_|          |___/
     \__/

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-[V 2.2.0]-=-=-=-=-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 */

// Get the window size

// Used to prevent cached duplication

// Hides or shows login message depending
// on the given resolution.
function displayLoginMsg() {
    if ($(window).width() <= 650) {
        $("#NotLoggedIn").hide();
    } else {
        $("#NotLoggedIn").show();
    }
}

// Display the small navigation menu?
function displaySmallNav($bool) {
    if ($bool) {
        $("#smallNavMenu").show();
        return true;
    } else {
        $("#smallNavMenu").hide();
    }
}

// Resize the branding
function resizeBranding($bool) {
    if ($bool) {
        $("#branding").addClass("small-brand");
    } else {
        $("#branding").removeClass("small-brand");
    }
}

// Resize the elements of the website
function resizeElements($bool) {
    if ($bool) {
        $("#headerContainer").width($(window).width()-20);
        $("#footer").width($(window).width()-10);
        $("nav").addClass("small-screen");
        $("#avatarToolbar").css("margin-left", "10px !important");
        resizeBranding(true);
    } else {
        $("#headerContainer").width($("#mainarea").width()+10);
        $("#footer").width($("#mainarea").width());
        $("nav").removeClass("small-screen");
        resizeBranding(false);
    }
}

// This is for smaller resolutions, and basically runs
// at the start.

$(document).ready(function () {
    // Or actual window size, which could imply
    // the resolution itself
    if ($(window).width() <= 960) {
        resizeElements(true);
        displaySmallNav(true);
    } else {
        resizeElements(false);
        displaySmallNav(false);
    }

    // On window resize adjust things
    $(window).resize(function () {
        if($(window).width() <= 960) {
            resizeElements(true);
            displaySmallNav(true);
        } else {
            resizeElements(false);
            displaySmallNav(false);
        }

        // Check and hide login message as needed.
        displayLoginMsg();
    });

    // Check and hide login message as needed.
    displayLoginMsg();
});