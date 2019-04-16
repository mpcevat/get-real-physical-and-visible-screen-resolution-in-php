<?php
/*  Get the physical and visible screen width

    When ready, these variables are set:
            $PhysicalWidth      = the hardware's maximum width
            $PhysicalHeight     = the hardware's maximum height
            $VisibleScreenWidth = the usable width of the window

*/

// set this stepsize to any value between 1 and 100; 10 works best
        $StepSize = 10;

// setthis to the max that you expect (up to 16640 for 16k UHD)
        $MaxWidthExpected = 3000;

// set up a php session
        session_start();

// determine the hardware (physical) screen size
        if(isset($_SESSION['screen_width']) AND isset($_SESSION['screen_height'])){
            $PhysicalWidth = $_SESSION['screen_width'];
            $PhysicalHeight = $_SESSION['screen_height'];
            echo 'Physical screen size: ' . $PhysicalWidth . 'x' . $PhysicalHeight;
        } else if(isset($_REQUEST['width']) AND isset($_REQUEST['height'])) {
            $_SESSION['screen_width'] = $_REQUEST['width'];
            $_SESSION['screen_height'] = $_REQUEST['height'];
            header('Location: ' . $_SERVER['PHP_SELF']);
        } else {
            echo '<script type="text/javascript">window.location = "' . $_SERVER['PHP_SELF'] . '?width="+screen.width+"&height="+screen.height;</script>';
        }

// create a HTML document and CSS style
        echo "<!DOCTYPE html><html><head><style>";

// create styles for ever stepsize
        $ScreenWidthIndex = 0;
        do {
            echo "@media (max-width:" . ($ScreenWidthIndex+$StepSize) . "px) {.div" . $ScreenWidthIndex . "{color:black;}}\n";
            echo "@media (min-width:" . $ScreenWidthIndex . "px) {.div" . $ScreenWidthIndex . "{color:red!important;}}\n";
            $ScreenWidthIndex=$ScreenWidthIndex+$StepSize;
        } while ($ScreenWidthIndex<$MaxWidthExpected);
        echo "</style>";

// create the scripts to get the real color value for the actual visible screen width
?>
<script>
    function GetColorOfDiv(MinWidthVal) {

        // have we already found a value, then exit
        var HasAValueBeenFound = document.getElementById("ReturnValue").innerHTML;
        if (HasAValueBeenFound != "No") {return;}

        // get the computer color
        var DivId = "xyz" + MinWidthVal;
        var elem = document.getElementById(DivId);
        var theCSSprop = window.getComputedStyle(elem, null).getPropertyValue("color");
        document.getElementById(DivId).innerHTML = theCSSprop;

        // if this is the first black section, then save the value
        if (theCSSprop == "rgb(0, 0, 0)") {
            document.getElementById("ReturnValue").innerHTML = MinWidthVal;

            // and store it in a cookie
            var expires = "";
            document.cookie = "Screenwidth=" + MinWidthVal + ";" + expires + ";path=/";
        }
    }

    // the style values are not updated unless reloaded, so at resize we reload the page
    function ReloadOnResize() {
            location.reload();
    }
</script>
<?php

        echo "</head>";

// make sure the colors are updated when the user resizes the screen
        echo "<body onresize='ReloadOnResize()'>";

// here we display the value as an indication to the script that we succeeded
        echo "<br>Screensize=<div id='ReturnValue'>No</div>";

// now create all individual divs that are default black but the style makes them red when outsize the screen width
        $ScreenWidthIndex = 0;
        do {
            echo "<div style='color:black;' id=xyz" . $ScreenWidthIndex . " class=div" . $ScreenWidthIndex . ">" . $ScreenWidthIndex . "</div>\n";
            $ScreenWidthIndex=$ScreenWidthIndex+$StepSize;
        } while ($ScreenWidthIndex<$MaxWidthExpected);

        // now everything is on the screen, get the computed color for each div
        $ScreenWidthIndex = 0;
        do {
            echo "<script type='text/javascript'> GetColorOfDiv('" . $ScreenWidthIndex . "')</script>\n";
            $ScreenWidthIndex = $ScreenWidthIndex + $StepSize;
        } while ($ScreenWidthIndex<$MaxWidthExpected);

// This is what we need: the script puts the result in this cookie
        $VisibleScreenWidth = $_COOKIE["Screenwidth"];
        echo "Value from cookie " . $VisibleScreenWidth;

// and wrap up the screen
        echo "</body></html>";
