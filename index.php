<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl">

<?php
/**
 * Include all the necessary files
 */
    include "configuratiemanagement.php";
    include "probleembeheer.php";
    include "incidentbeheer.php";
    include "form_functions.php";
    include "general.php";
    include "HelpdeskTable.php";
    include "Button.php";
    include "login.php";
    include "form_checks.php";

    checkLogin();
?>

<?php
/*
 * Check ifsomeone is logged in, and if they have taken any action
 */
    if(isset($_SESSION['user'])){
        // Time out functionality
        if(isset($_SESSION['timeout'])){
            if ($_SESSION['timeout'] + 10 * 60 < time()) {
                logOut();
            } else {
                $_SESSION['timeout'] = time();
            }
        } else {
            $_SESSION['timeout'] = time();
        }

        if(isset($_POST['id'])){
            switch($_SESSION['rechten']){
                case 'incident' : processEventIncident($_POST['id']); break;
                case 'probleem' : processEventProbleem($_POST['id']); break;
                case 'config' : processEventConfig($_POST['id']); break;
            }
        }
    }
?>

<!-- This includes the stylesheet -->
<head>
    <link type="text/css" rel="stylesheet" href="stijlblad.css">
</head>

<body>
<div id="container">

<!-- This shows the header -->
<div id="header">
    <span class="foto"><img src="helpdesk.png" /></span>
</div>


<!-- This shows the logout button if a user is logged in. -->
<div id="topmenu">
    <?php
        if(isset($_SESSION['user'])) {
            searchField();
            logoutKnop();
        }
    ?>
</div>


<!-- The following code shows the navigation of the website, based on the rights of the current user. -->

<div id="left_sidebar">
    <div id="menu">
        <?php
            //Check if the user is logged in.
            if(isset($_SESSION['user'])) {
                switch($_SESSION['rechten']) {
                    case 'incident' : displayMenuIncident(); break;
                    case 'probleem' : displayMenuProbleem(); break;
                    case 'config' : displayMenuConfig(); break;
                }
            }
        ?>
    </div>
</div>

<!-- The following code takes care of showing the content. This is based on the users' rights. -->

<div id="content">
    <?php
    //Check if the user is logged in.
        if(!isset($_SESSION['user'])) {
            displayLogin();
        } else {
            switch($_SESSION['rechten']) {
                case 'incident' : displayContentIncident($_POST['display']); break;
                case 'probleem' : displayContentProbleem($_POST['display']); break;
                case 'config' : displayContentConfig($_POST['display']); break;
            }
        }
    ?>
</div>

<!-- This shows the footer -->
<div id="footer">
    <?php
        echo "Copyright Tapir Inc.";
    ?>
</div>



</div>
</body>
</html>