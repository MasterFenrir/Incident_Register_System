<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 11-6-14
 * Time: 16:04
 */

$con = mysqli_connect("localhost","helpdesk","Sku53_u3","helpdesk") or die('Unable to connect');

function logoutKnop()
{
    global $perm;
    echo $perm;

    echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method=\"post\">";
    echo "<input type=\"hidden\" name=\"logout\" value=\"1\">";
    echo "<input class=\"logout\" type=\"submit\" value=\"Logout\">";
    echo "</form>";
}

/*
 *  Deze functie controleerd of iemand op uitloggen heeft geklikt, zo ja worden sessie variabelen verwijderd en de sessie beeindigd
 */
function checkLogin()
{
    session_start();

    if(isset($_POST['logout']))
    {
        logOut();
    }
}

/*
 *Deze functie eindigd de sessie en vernietigd de sessie variabelen
 */
function logOut()
{
    session_unset();
    session_destroy();
}