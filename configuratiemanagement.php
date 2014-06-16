<?php
/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 12-6-14
 * Time: 11:52
 */

$message= "";

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
        case "confirmDeleteUser" : confirmDeleteUser; break;

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
        case "deleteUser" : deleteUser(); break;
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
        textField("Hardware_ID", null);
        dropDown("Soort", queryToArray("SELECT soort FROM hardware GROUP BY soort"), null);
        dropDown("Locatie", queryToArray("SELECT locatie FROM hardware GROUP BY locatie"), null);
        dropDown("OS", queryToArray("SELECT naam FROM software WHERE soort LIKE '%besturingssysteem%'"), null);
        CheckBoxes("Software", queryToArray("SELECT naam FROM software WHERE soort NOT LIKE '%besturingssysteem%'"), 3, null);
        textField("Leverancier", null);
        textField("Aanschaf_jaar", null);
        textField("Status", null);
        hiddenValue("display", "displayHardware");
        formFooter("addHardware");
    }

    function displayEditHardware()
    {
        global $con;

        formHeader();
        $value = mysqli_query($con, "SELECT id_hardware FROM hardware WHERE id_hardware='".$_POST['key']."'");
        textField("Hardware_ID", mysqli_fetch_row($value));
        $value = mysqli_query($con, "SELECT soort FROM hardware WHERE id_hardware='".$_POST['key']."'");
        dropDown("Soort", queryToArray("SELECT soort FROM hardware GROUP BY soort"), mysqli_fetch_row($value));
        $value = mysqli_query($con, "SELECT locatie FROM hardware WHERE id_hardware='".$_POST['key']."'");
        dropDown("Locatie", queryToArray("SELECT locatie FROM hardware GROUP BY locatie"), mysqli_fetch_row($value));
        $value = mysqli_query($con, "SELECT software.naam FROM software, hardware WHERE hardware.os = software.id_software AND hardware.id_hardware='".$_POST['key']."'");
        dropDown("OS", queryToArray("SELECT naam FROM software WHERE soort LIKE '%besturingssysteem%'"), mysqli_fetch_row($value));
        //$value = mysqli_query($con, "SELECT id_software FROM hardware_software WHERE id_hardware='".$_POST['key']."'") or die('error');
        CheckBoxes("Software", queryToArray("SELECT naam FROM software WHERE soort NOT LIKE '%besturingssysteem%'"), 3,
                    queryToArray("SELECT id_software FROM hardware_software WHERE id_hardware='".$_POST['key']."'"));
        $value = mysqli_query($con, "SELECT leverancier FROM hardware WHERE id_hardware='".$_POST['key']."'");
        textField("Leverancier", mysqli_fetch_row($value));
        $value = mysqli_query($con, "SELECT aanschaf_jaar FROM hardware WHERE id_hardware='".$_POST['key']."'");
        textField("Aanschaf_jaar", mysqli_fetch_row($value));
        $value = mysqli_query($con, "SELECT status FROM hardware WHERE id_hardware='".$_POST['key']."'");
        textField("Status", mysqli_fetch_row($value));
        hiddenValue("display", "displayHardware");
        formFooter("editHardware");
    }


    /**
     * This function will create a form to add a new user
     */
    function displayAddUser(){
        global $message;
        if($message != ""){
            echo($message);
            $message = "";
        }
        formHeader();
        textField("Gebruikersnaam", null);
        passwordField("password1");
        passwordField("password2");
        dropDown("Rechten", queryToArray("SELECT * FROM rechten"), null);
        hiddenValue("display", "displayAddUser");
        formFooter("addUser");
    }

    /**
     * This function adds a user and encrypts his password
     */
    function addUser(){
        global $con;
        global $message;
        $message = "";
        $username = removeMaliciousInput($_POST['Gebruikersnaam']);
        $password1 = removeMaliciousInput($_POST['password1']);
        $password2 = removeMaliciousInput($_POST['password2']);
        $rechten = $_POST['Rechten'];

        $result = mysqli_query($con, "SELECT COUNT(*) FROM users WHERE username = '{$username}'") or die("Stuff");
        $result = mysqli_fetch_row($result);

        if($result[0] > 0){
            $message .= "ERROR: Deze gebruikersnaam bestaat al!";
        }
        if($password1 != $password2){
            $message .= "ERORR: De wachtwoorden komen niet overeen!";
        }
        if($message === ""){
            $hash = password_encrypt($password1);
            mysqli_query($con, "INSERT INTO users
                                VALUES('{$username}', '{$hash}', '{$rechten}')") or die(mysqli_error($con));

            if (mysqli_connect_errno())
            {
                $message .= "Gebruiker toevoegen mislukt. Probeer het opnieuw.";
            } else {
                $message .= "Gebruiker succesvol toegevoegd.";
            }
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
    }

    function deleteHardware()
    {
        global $con;

        $primeKey = $_POST['key'];

        mysqli_query($con, "DELETE FROM hardware_software WHERE id_hardware='".$primeKey."'") or die('swdel error');
        mysqli_query($con, "DELETE FROM hardware WHERE id_hardware='".$primeKey."'") or die('hwdel error');
    }

    function deleteUser(){
        global $con;
        $primeKey = $_POST['key'];
        mysqli_query($con, "DELETE FROM users WHERE username='".$primeKey."'") or die('hwdel error');
    }

    function displayLandingConfig()
    {
        echo "Hello ".ucfirst($_SESSION['user']);
    }

    function deleteSoftware()
    {
        global $con;

        $primeKey = $_POST['key'];
        echo $primeKey;
        mysqli_query($con, "DELETE FROM software WHERE id_software = $primeKey");
    }

    function displayEditSoftware()
    {
        $primeKey = $_POST['key'];
    }

    function confirmDeleteUser(){
        $primeKey = $_POST['key'];
        if($primeKey === $_SESSION['user']){
            echo("Je kunt jezelf niet verwijderen.");
        } else {
            echo("Weet je het zeker dat je de gebruiker {$primeKey} wil verwijderen?");
            new Button("Nee, ga terug", "display", "displayUsers");
            formHeader();
            hiddenValue("display", "displayUsers");
            hiddenValue("key", $primeKey);
            formFooter("deleteUser", "Ja, verwijder deze gebruiker");
        }
    }
?>