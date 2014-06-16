<?php
/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 12-6-14
 * Time: 11:52
 */

//dummycode for configuratiemagement
// to do change name and remove some code

function displayContentConfig($postData) {
    switch($postData) {
        case "displayHardware" : displayHardware($postData); break;
        case "displayEditHardware" : displayEditHardware(); break;
        case "displayAddHardware" : displayAddHardware(); break;

        case "displaySoftware" : displaySoftware($postData); break;
        case "displayEditSoftware" : displayEditSoftware(); break;
        //case "displayAddSoftware" : displayAddSoftware(); break;

        case "displayUsers" : displayUsers($postData); break;
       // case "displayEditUser" : displayEditUser(); break;
        case "displayAddUser" : displayAddUser(); break;

        default : displayLandingConfig();
    }
}

function displayMenuConfig() {
    new Button("Hardware", "display", "displayHardware");
    new Button("Software", "display", "displaySoftware");
    new Button("Gebruikers", "display", "displayUsers");
    new Button("Hardware toevoegen", "display", "displayAddHardware");
    new Button("Software toevoegen", "display", "displayAddSoftware");
    new Button("Gebruiker toevoegen", "display", "displayAddUser");
}

function processEventConfig($eventID)
{
    switch($eventID) {
        case "deleteHardware" : deleteHardware(); break;
        case "deleteSoftware" : deleteSoftware(); break;
        case "addHardware" : addHardware(); break;
        case "addUser"  : addUser(); break;
    }
}

    function displayHardware($postData)
    {
        new HelpdeskTable("Hardware", "SELECT * FROM hardware", $postData,
                          "displayEditHardware", "deleteHardware", "id_hardware");
    }

    function displaySoftware($postData)
    {
        new HelpdeskTable("Software", "SELECT * FROM software", $postData,
                          "displayEditSoftware", "deleteSoftware", "id_software");
    }

    /**
     * This function creates a table that displays the existing users
     * @param $postData
     */
    function displayUsers($postData)
    {
        new HelpdeskTable("Gebruikers", "SELECT username, rechten FROM users", $postData,
            "displayEditUser", "deleteUser", "username");
    }

    function displayAddHardware()
    {
        formHeader();
        textField("Hardware_ID");
        dropDown("Soort", queryToArray("SELECT soort FROM hardware GROUP BY soort"));
        dropDown("Locatie", queryToArray("SELECT locatie FROM hardware GROUP BY locatie"));
        dropDown("OS", queryToArray("SELECT naam FROM software WHERE soort LIKE '%besturingssysteem%'"));
        CheckBoxes("Software", queryToArray("SELECT naam FROM software WHERE soort NOT LIKE '%besturingssysteem%'"), 3);
        textField("Leverancier");
        textField("Aanschaf_jaar");
        textField("Status");
        formFooter("addHardware");
    }


    /**
     * This function will create a form to add a new user
     */
    function displayAddUser(){
        formHeader();
        textField("Gebruikersnaam");
        passwordField("password1");
        passwordField("password2");
        dropDown("Rechten", queryToArray("SELECT * FROM rechten"));
        formFooter("addUser");
    }

    /**
     * This function adds a user and encrypts his password
     */
    function addUser(){
        global $con;
        $username = removeMaliciousInput($_POST['Gebruikersnaam']);
        $password1 = removeMaliciousInput($_POST['password1']);
        $password2 = removeMaliciousInput($_POST['password2']);
        $rechten = $_POST['rechten'];
        $error = "";

        $result = mysqli_query($con, "SELECT COUNT(*) FROM users WHERE username = '{$username}'") or die("Stuff");
        $result = mysqli_fetch_row($result);

        if($result[0] > 0){
            $error .= "ERROR: Deze gebruikersnaam bestaat al!";
        }
        if($password1 != $password2){
            $error .= "ERORR: De wachtwoorden komen niet overeen!";
        }
        if($error === ""){
            $hash = password_encrypt($password1);
            mysqli_query($con, "INSERT INTO users
                                VALUES('{$username}', '{$hash}', '{$rechten}')") or die(mysqli_error($con));

            if (mysqli_connect_errno())
            {
                echo("Gebruiker toevoegen mislukt. Probeer het opnieuw.");
            } else {
                echo("Gebruiker succesvol toegevoegd.");
            }
        } else {
            echo($error);
            displayAddUser();
        }
    }

function addHardware()
    {
        global $con;

        $valid = emptyCheck($_POST['Hardware_ID']);
        $valid = emptyCheck($_POST['Soort']);
        $valid = emptyCheck($_POST['Locatie']);
        $valid = emptyCheck($_POST['Leverancier']);
        $valid = yearCheck($_POST['Aanschaf_jaar']);

        if($valid) {
            mysqli_query($con, "INSERT INTO hardware (id_hardware, soort, locatie, os, leverancier, aanschaf_jaar, status)
                                VALUES('".$_POST['Hardware_ID']."', '".$_POST['Soort']."', '".$_POST['Locatie']."',
                                       '".$_POST['OS']."', '".$_POST['Leverancier']."', '".$_POST['Aanschaf_jaar']."',
                                       '".$_POST['Status']."')") or die('hw error');
        }

        if(!empty($_POST['boxes'])) {
            foreach($_POST['boxes'] as $box) {
                mysqli_query($con, "Insert INTO hardware_software (id_hardware, id_software)
                                    VALUES ('".$_POST['Hardware_ID']."','".$box."')") or die('sw error');
            }
        }

        displayHardware("displayHardware");
    }

    function deleteHardware()
    {
        global $con;

        $primeKey = $_POST['key'];
        mysqli_query($con, "DELETE FROM hardware WHERE id_hardware='".$primeKey."'") or die('hwdel error');
    }

    function displayLandingConfig()
    {
        echo "Hello ".ucfirst($_SESSION['user']);
    }

    function deleteSoftware()
    {
        global $con;

        $primeKey = $_POST['key'];
        mysqli_query($con, "DELETE FROM software WHERE id_software = $primeKey");
    }

    function displayEditHardware()
    {
        $primeKey = $_POST['key'];
    }

    function displayEditSoftware()
    {
        $primeKey = $_POST['key'];
    }

    function InkoopInkooporderToevoegen()
    {
        global $con;

        $sql = "SELECT * FROM hardware
		GROUP BY id_hardware";

        $row = mysqli_query($con, "$sql");

        echo "  <form method=\"post\" action=\"index.php\">";
        echo " 	<table>";
        echo "	<tr>";
        echo "	       <th>id_hardware</th>";
        echo "	      	<th>soort</th>";
        echo "          <th>locatie</th>";
        echo "	       <th>os</th>";
        echo "	      	<th>merk</th>";
        echo "          <th>leverancier</th>";
        echo "          <th>aanschafjaar</th>";
        echo "	 </tr>";

        while($row2 = mysqli_fetch_array($row))
        {
            echo "<tr>";
            echo " <td><input type='checkbox' name='Artikelnr[]' value='".$row2['Artikelnr']."'>".ucfirst($row2['Naam'])."</td>";
            echo "	<td><input type=\"text\" name=\"Hoeveelheid[]\" /></td>";
            echo "</tr>";
        }

        echo"		<tr>";
        echo"			<td></td>";
        echo"			<td><input type=\"submit\" value=\"Bestellen\" /></td>";
        echo"			    <input type=\"hidden\" name='display' value=\"inkoopbestelbevestig\" />";
        echo"		</tr>";
        echo" 	</table>";

        echo" </form>";
    }

	//Bugged, geeft hoeveelheid niet mee bij 1 artikel
	function InkoopInkooporderBevestig()
    {
        global $_POST;
        global $con;

        $id = $_POST['Artikelnr'];
        $id2 = $_POST['Hoeveelheid'];

        $sql2 = "SELECT COUNT(Artikelnr) AS aantal FROM artikel";
        $query2 = mysqli_query($con, "$sql2");

        list($aantal) = mysqli_fetch_array($query2);

        echo "<form method=\"post\" action=\"index.php\">";
        echo "<table>";
        echo 	"<tr>";
        echo 		"<th>Artikel</th>";
        echo 		"<th>Aantal</th>";
        echo 	"</tr>";

        for($x=0; $x<$aantal; $x++)
        {
            if(!empty($id[$x]))
            {
                $artnr = $id[$x];
                //$hoeveel = $id2[$x];


                //echo $hoeveel;

                $sql = "SELECT Naam FROM artikel
			WHERE Artikelnr = $artnr";
                $query = mysqli_query($con, "$sql");

                list($naam) = mysqli_fetch_array($query);

                echo 	"<tr>";
                echo 		"<td>$naam</td>";
                echo 	"</tr>";
            }
        }

        echo    "<th colspan=\"2\" class=\"klaar\">";
        echo    "<form action=\"/index.php\" method=\"post\">";
        echo    "<input type=\"hidden\" name=\"Artikelnr[]\" value=\"$id\">";
        echo    "<input type=\"hidden\" name=\"Hoeveelheid[]\" value=\"$id2\">";
        echo    "<input type=\"hidden\" name=\"bewerk\" value=\"inkoopbewerk\">";
        echo    "<input class=\"logout\" type=\"submit\" value=\"Bevestigen\">";
        echo    "</form>";

        echo    "<form action=\"/index.php\" method=\"post\">";
        echo    "<input type=\"hidden\" name=\"bewerk\" value=\"inkoopbewerk\">";
        echo    "<input type=\"hidden\" name=\"change\" value=\"x\">";
        echo    "<input class=\"logout\" type=\"submit\" value=\"Anuleren\">";
        echo    "</form>";
        echo    "</th>";
        echo "</table>";
        echo "</form>";
    }

	function InkoopInkooporderBewerk()
    {
        global $_POST;
        global $con;

        $id = $_POST['Artikelnr'];
        $id2 = $_POST['Hoeveelheid'];
        $check = $_POST['change'];
        $aantal=count($id);

        if($check !== 'x')
        {
            for($x=0; $x<=$aantal; $x++)
            {
                $artnr = $id[$x];
                $hoeveel = $id2[$x];

                echo $artnr;
                echo $hoeveel;

                $sql = "INSERT INTO inkooporder (Artikelnr, Hoeveelheid)
	            VALUES ($artnr, $hoeveel)";

                mysqli_query($con, "$sql");

                $sql2 = "UPDATE artikel
		    SET Besteld = (Besteld + $hoeveel)
		    WHERE Artikelnr = $artnr";
                mysqli_query($con, "$sql2");
            }
        }

        $_POST['display'] = 'inkoopartikelen';
    }
?>