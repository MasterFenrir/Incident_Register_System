<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl">

<head><title>Helpdesk</title></head>
<?php
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
    if(isset($_SESSION['user'])){
        if(isset($_POST['id'])){
            switch($_SESSION['rechten']){
                case 'incident' : processEventIncident($_POST['id']); break;
                case 'probleem' : processEventProbleem($_POST['id']); break;
                case 'config' : processEventConfig($_POST['id']); break;
            }
        }
    }
?>

<head>
    <link type="text/css" rel="stylesheet" href="stijlblad.css">
</head>

<body>
<div id="container">

<!-- Dit toont de banner aan de bovenkant van de pagina -->
<div id="header">
    <span class="foto"><img src="helpdesk.png" /></span>
</div>


<!-- Dit toont de logout knop en geeft het een div id wanneer iemand ingelogd is -->
<div id="topmenu">
    <?php
        if(isset($_SESSION['user'])) {
            logoutKnop();
        }
    ?>
</div>


<!--
     De onderstaande code toont de navigatie gedeelte van de website en geeft het een positie op het scherm
     Alleen de functies die voor de verschillende actoren de navigatie
     menu tonen moeten hierin gezet worden.
     De waarde van $_SESSION['user'] word bepaald als je inlogd en is de Username.
-->

<div id="left_sidebar">
    <div id="menu">
        <?php
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

<!--
     De onderstaande code toont de content gedeelte van de website en geeft het een positie op het scherm
     Alle functies die betrekking hebben op iets wat je ziet wat niet tot het navigatie
     menu hoort moet hierin gezet worden.
     Ook worden $_POST waarden vanuit functies overgezet naar $_SESSION variabelen, en ook $_POST variabelen
     'verlengt' zodat ze meer dan 1 click verstuurt kunnen worden.
     De navigatie knoppen sturen de waarde van $_POST['display'] hier naartoe.
-->

<div id="content">
    <?php
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

<!-- Dit toont de footer -->
<div id="footer">
    <?php
        echo "Copyright Soepmonsters";
    ?>
</div>



</div>
</body>
</html>