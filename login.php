
<?php
ERROR_REPORTING(E_ALL);

/**
 * This function will display the loginscreen, but also checks if the login credentials have been entered.
 * If they have been entered correctly, it will send you to the page corresponding to the rights that user has.
 */
function displayLogin()
{
    //This will check if you login correctly
    if(isset($_POST['check']))
    {
        $user = $_POST['username'];
        $pass = $_POST['password'];


        // This checks whether the username and password have been entered
        if(empty($pass) && !empty($user))
        {
            $ww = "Verplicht";
        }
        elseif(!empty($pass) && empty($user))
        {
            $name = "Verplicht";
        }
        elseif(empty($pass) && empty($user))
        {
            $empt = "Je moet iets invullen";
        }
        // Als je alles hebt ingevuld word in de DB gekeken of de username en ww overeen komen
        // Zo ja word je ingelogd en naar de gebruikers site gestuurd, no niet krijg je een melding dat inloggegevens niet kloppen
        if(!empty($user) && !empty($pass))
        {
            global $con;

            $sql = "SELECT * FROM users
                    WHERE username = '$user'";

            $query = mysqli_query($con, $sql) or die ("Error: ".mysqli_error($query)."");

            if (($get = mysqli_fetch_assoc($query)) > 0)
            {
                if($pass == $get[password]){
                    $rechten = $get['rechten'];

                    $_SESSION['user'] = $user;
                    $_SESSION['rechten'] = $rechten;

                    header('Location:/index.php');
                }

                /*
                    $existing_hash = $get[password];
                    $hash = password_encrypt($pass, $existing_hash);
                    if($hash === $existing_hash){
                 */
            }

            else
            {
                $error = "Verkeerde inlog gegevens";
            }
        }
    }

    /*
     *Hieronder volgt de inlog formulier
     */

    echo "<form method=\"post\" action=".htmlspecialchars($_SERVER['PHP_SELF'])." >";
    echo        "<table id=\"login\">";
    echo        "<tr>";
    echo            "<td>Username:</td>";
    echo            "<td>";
    echo               "<input type=\"text\" name=\"username\">";
    if(isset($user))
    {
        echo "<span class=\"warning\">".$name."</span>";
    }
    echo            "</td>";
    echo        "</tr>";
    passwordField("password");
    echo            "<td></td>";
    echo            "<td>";
    echo                "<input type=\"Submit\" Value=\"Login\" >";
    if(isset($empt) || isset($error))
    {
        echo "<span class=\"warning\">".$empt.$error."</span>";
    }
    echo            "</td>";
    echo        "</tr>";
    echo                "<input type=\"hidden\" name=\"check\" value=\"1\">";
    echo    "</table>";
    echo "</form>";

}

//showchangepassword
function changePassword(){

    echo"<h3>Om je wachtwoord te veranderen, voer je gebruikersnaam, oude wachtwoord <br/>en je nieuwe wachtwoord in en klik op 'Verander'</h3>";

    echo "<form action=\"/index.php\" method=\"post\">";
    echo        "<table id=\"login\">";
    echo        "<tr>";
    echo            "<td>Gebruikersnaam:</td>";
    echo            "<td>";
    echo               "<input type=\"text\" name=\"username\">";
    echo            "</td>";
    echo        "</tr>";
    echo        "<tr>";
    echo            "<td>Wachtwoord:</td>";
    echo            "<td>";
    echo               "<input type=\"password\" name=\"oldpassword\">";
    echo            "</td>";
    echo        "</tr>";
    echo        "<tr>";
    echo            "<td>Nieuw wachtwoord:</td>";
    echo            "<td>";
    echo               "<input type=\"password\" name=\"newpassword\">";
    echo            "</td>";
    echo        "</tr>";
    echo        "<tr>";
    echo            "<td>Nogmaals het nieuwe wachtwoord:</td>";
    echo            "<td>";
    echo               "<input type=\"password\" name=\"newpasswordtest\">";
    echo            "</td>";
    echo        "</tr>";
    echo        "<tr>";
    echo            "<td></td>";
    echo            "<td>";
    echo                "<input type=\"hidden\" name=\"display\" value=\"bewerkwachtwoord\">";
    echo                "<input type=\"Submit\" Value=\"Verander\" >";
    echo            "</td>";
    echo        "</tr>";
    echo    "</table>";
    echo "</form>";

}

//roep aan met bewerkwachtwoord
function saveChangePassword(){

    global $con;

    $error = "";

    $user = $_POST['username'];
    $oldpassword = $_POST['oldpassword'];
    $newpassword = $_POST['newpassword'];
    $newpasswordtest = $_POST['newpasswordtest'];

    $sql = "SELECT COUNT(*) FROM inloggegevens
                WHERE Gebruikersnaam = '$user'
                AND wachtwoord = '$oldpassword'";

    $result = mysqli_query($con, $sql) or die ("Error: ".mysqli_error($sql)."");
    list($teller) = mysqli_fetch_row($result);

    if(strcmp($newpassword,$newpasswordtest) != 0){

        $error = "Het nieuwe wachtwoord dat je invoert komt niet overeen met de tweede keer dat je het nieuwe wachtwoord invoert.<br/>";

    }

    if(empty($newpassword)){

        $error = "Je moet wel een nieuw wachtwoord invoeren.<br/>";

    }

    if($teller == 0){

        $error = "Deze gebruiker bestaat niet, weet je zeker dat je alles goed hebt ingevoerd?<br/>";

    }

    if(empty($error)){

        $sql = "UPDATE inloggegevens
					SET wachtwoord='$newpassword'
					WHERE Gebruikersnaam = '$user'
					AND wachtwoord = '$oldpassword'";

        mysqli_query($con, "$sql") or die ("Error: ".mysqli_error($sql)."");

        echo"Je wachtwoord is succesvol veranderd.";

    } else {

        echo"Er is iets fout gegaan:<br/>$error";
        echo "<form action=\"/index.php\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"display\" value=\"showchangepassword\">";
        echo "<input class=\"display\" type=\"submit\" value=\"Probeer opnieuw\">";
        echo "</form>";

    }
}

?>