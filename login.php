
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
                /*if($pass == $get[password]){*/

                if(password_check($pass, $get['password'])){
                    $_SESSION['user'] = $user;
                    $_SESSION['rechten'] = $get['rechten'];

                    header('Location:/index.php');
                } else {
                    $error = "Verkeerde inlog gegevens. Wachtwoord";
                }
            }

            else
            {
                $error = "Verkeerde inlog gegevens.";
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
?>