<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl">


<?php
    include "functions.php";
    include "HelpdeskTable.php";
    include "login.php";

    checkLogin();
?>

<head>
    <link type="text/css" rel="stylesheet" href="stijlblad.css">
</head>

<body>
<div id="container">

<!-- Dit toont de banner aan de bovenkant van de pagina -->
<div id="header">
    <?php
        echo "Header";
    ?>
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
                switch($_SESSION['user']) {
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
            switch($_SESSION['user']) {
                case 'incident' : displayContentIncident($_POST['nav']); break;
                case 'probleem' : displayContentProbleem($_POST['nav']); break;
                case 'config' : displayContentConfig($_POST['nav']); break;
                default : new HelpdeskTable("Hardware", "SELECT * FROM hardware");
            }
        }
    ?>

    crypt(passwordjsdjsdoifjsdflkjsdflkjsdljsdflkjsdfj)

    if $dbpw == crypt(pw, $dbpw)
</div>

<!-- Dit toont de footer -->
<div id="footer">
    <?php
        echo "Footer";
    ?>
</div>



</div>
</body>
</html>