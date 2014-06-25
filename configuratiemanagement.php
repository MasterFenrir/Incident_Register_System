<?php
/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 12-6-14
 * Time: 11:52
 */

$message= "";


/**
 * Function that calls the other functions to display all data from the database.
 * @param $postData
 */
function displayContentConfig($postData) {
    switch($postData) {
        case "displayHardware" : displayHardware($postData); break;
        case "displayEditHardware" : displayEditHardware(); break;
        case "displayAddHardware" : displayAddHardware(); break;

        case "displaySoftware" : displaySoftware($postData); break;
        case "displayEditSoftware" : displayEditSoftware(); break;
        case "displayAddSoftware" : displayAddSoftware(); break;

        case "displayUsers" : displayUsers($postData); break;
        case "displayEditUser" : displayEditUser(); break;
        case "displayAddUser" : displayAddUser(); break;
        case "displaySearch" : displaySearchConfig($postData); break;

        case "displayHardwareAndSoftware" : displayHardwareAndSoftware($postData); break;
        default : displayLandingConfig();
    }
}

/**
 * Function that makes all the buttons in the menu to change and to display data.
 */
function displayMenuConfig() {
    new Button("Hardware", "display", "displayHardware");
    new Button("Software", "display", "displaySoftware");
    new Button("Gebruikers", "display", "displayUsers");
    new Button("Hardware toevoegen", "display", "displayAddHardware");
    new Button("Software toevoegen", "display", "displayAddSoftware");
    new Button("Gebruiker toevoegen", "display", "displayAddUser");
}

/**
 * Function that calls all functions that delete, add, edit the hardware, software and user.
 * @param $eventID
 */
function processEventConfig($eventID)
{
    switch($eventID) {
        case "deleteHardware" : deleteHardware(); break;
        case "deleteSoftware" : deleteSoftware(); break;
        case "addSoftware" : addSoftware(); break;
        case "addHardware" : addHardware(); break;
        case "editHardware" : editHardware(); break;
        case "editSoftware" : editSoftware(); break;
        case "addUser"  : addUser(); break;
        case "deleteUser" : deleteUser(); break;
        case "editUser" : editUser(); break;
    }
}

/**
 * Function that preforms the search request and displays those results in a table.
 * @param $postData
 */
function displaySearchConfig($postData)
{
    new HelpdeskTable("Hardware", makeSearchHardware($_POST['search']), null,
                      "displayEditHardware", "deleteHardware", "id_hardware", $_POST['search'], "displayHardwareAndSoftware");

    echo "<br/>";

    new HelpdeskTable("Software", makeSearchSoftware($_POST['search']), null,
        "displayEditSoftware", "deleteSoftware", "id_software", $_POST['search'], null);
}


/*
 * Builds the query to search for the given search String
 * @param $searchString: Value to search for
 */
function makeSearchHardware($searchString)
{
    $sel = array( 'hardware.id_hardware', 'hardware.soort', 'hardware.locatie', 'hardware.os',
                  'hardware.merk', 'hardware.leverancier', 'hardware.aanschaf_jaar');
    $from = array('hardware'=>'id_hardware', 'hardware_software'=>'id_software', 'software'=>'id_software');
    $cols = array('hardware.id_hardware', 'hardware.soort', 'hardware.locatie', 'hardware.os', 'hardware.merk',
                  'hardware.leverancier', 'hardware.aanschaf_jaar', 'hardware.status', 'software.naam');
    $type = 'AND';
    $grp = 'id_hardware';
    $search = $searchString;

    return monsterQueryBuilder($sel, $from, $cols, $type, $grp, $search);
}

/*
 * Builds the query to search for the given search String
 * @param $searchString: Value to search for
 */
function makeSearchSoftware($searchString)
{
    $sel = array( 'id_software AS ID', 'naam', 'soort', 'producent', 'leverancier',
                  'aantal_licenties AS Licenties', 'soort_licentie AS Licentiesoort',
                  'aantal_gebruikers AS Gebruikers', 'status');
    $from = array('software'=>'id_software');
    $cols = array('id_software', 'naam', 'soort', 'producent', 'leverancier', 'aantal_licenties', 'soort_licentie',
                  'aantal_gebruikers', 'status');
    $type = 'OR';
    $grp = 'id_software';
    $search = $searchString;

    return monsterQueryBuilder($sel, $from, $cols, $type, $grp, $search);
}

/**
 * Function that displays the softwaretable.
 * @param $postData
 */
function displaySoftware($postData)
    {
        echo("Hier ziet u de software tabel hier kunt u via de knop edit de gewenste gegevens wijzigen en via de delete knop software verwijderen");
        new HelpdeskTable("Software", "SELECT id_software AS ID, naam, soort,
                                              producent, leverancier, aantal_licenties AS Licenties,
                                              soort_licentie AS Licentiesoort, aantal_gebruikers AS Gebruikers,
                                              status
                                              FROM software", $postData,
                          "displayEditSoftware", "deleteSoftware", "ID", null, null);
    }

/**
 * Form where the user of the site can add software to the database.
 */
function displayAddSoftware()
    {
        echo("Hier kunt u software toevoegen u kunt de ingevulde gegevens doorvoeren door op de submit knop te klikken");
        displayErrors();

        formHeader();
        textField("ID_Software", $_POST['ID_Software']);
        textField("Naam", $_POST['Naam']);
        textField("Soort", $_POST['Soort']);
        textField("Producent", $_POST['Producent']);
        textField("Leverancier", $_POST['Leverancier']);
        textField("Aantal_Licenties", $_POST['Aantal_licenties']);
        textField("Soort_Licentie", $_POST['Soort_Licentie']);
        textField("Aantal_Gebruikers", $_POST['Aantal_Gebruikers']);
        textField("Status", $_POST['Status']);
        hiddenValue("display", "displaySoftware");
        formFooter("addSoftware");
    }

/**
 * Function that creates a form where the values have been filled in. The user can change those values to change them in the softwaretable.
 */
function displayEditSoftware()
    {
        echo("Hier kunt u de software wijzigen de gewijzigde gegevens kunt u bevestigen door op submit te klikken");
        global $con;
        displayErrors();

        $values = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM software WHERE id_software='".$_POST['key']."'"));

        formHeader();
        displayField("ID_Software", $values['id_software'] );
        textField("Naam", $values['naam']);
        textField("Soort", $values['soort']);
        textField("Producent", $values['producent']);
        textField("Leverancier", $values['leverancier']);
        textField("Aantal_Licenties", $values['aantal_licenties']);
        textField("Soort_Licentie", $values['soort_licentie']);
        textField("Aantal_Gebruikers", $values['aantal_gebruikers']);
        textField("Status", $values['status']);
        hiddenValue("display", "displaySoftware");
        formFooter("editSoftware");
    }

/**
 * Function that changes the softwaretable with the values from displayEditSoftware.
 */
function editSoftware()
    {
        echo("Hier kunt u de software wijzigen u kunt de gegevens bevestigen door op submit te klikken");
         global $con;
         global $message;

        $valid = emptyCheck($_POST['ID_Software']);
        if(!emptyCheck($_POST['ID_Software'])){$message = $message."ID mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Naam']); $naam = removeMaliciousInput($_POST['Naam']);
        if(!emptyCheck($_POST['Naam'])){$message = $message."Naam mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Soort']); $soort = removeMaliciousInput($_POST['Soort']);
        if(!emptyCheck($_POST['Soort'])){$message = $message."Soort mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Producent']); $pro = removeMaliciousInput($_POST['Producent']);
        if(!emptyCheck($_POST['Producent'])){$message = $message."Producent mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Leverancier']); $lev = removeMaliciousInput($_POST['Leverancier']);
        if(!emptyCheck($_POST['Leverancier'])){$message = $message."Leverancier mag niet leeg zijn<br/>";}

        if($valid) $valid = numberCheck($_POST['Aantal_Licenties']); $a_lic = removeMaliciousInput($_POST['Aantal_Licenties']);
        if(!numberCheck($_POST['Aantal_Licenties'])){$message = $message."Ongeldig aantal licenties<br/>";}

        $s_lic = removeMaliciousInput($_POST['Soort_Licentie']);
        $a_geb = removeMaliciousInput($_POST['Aantal_Gebruikers']);
        $status = removeMaliciousInput($_POST['Status']);

        if($valid) {
            mysqli_query($con, "UPDATE software SET naam='".$naam."', soort='".$soort."', producent='".$pro."', leverancier='".$lev."', aantal_licenties='".$a_lic."', soort_licentie='".$s_lic."', aantal_gebruikers='".$a_geb."', status='".$status."'
                                WHERE id_software='".$_POST['ID_Software']."'")or die(mysqli_error($con));
        } else {
            $_POST['display'] = 'displayEditSoftware';
        }
    }
    /**
     * This function creates a table that displays the existing users
     * @param $postData
     */
    function displayUsers($postData)
    {
        echo("Hier ziet u de gebruikers tabel hier kunt u via de knop edit de gewenste gegevens wijzigen en via de delete knop gebruikers verwijderen");
        global $message;
        echo $message;
        $messagen = "";
        new HelpdeskTable("Gebruikers", "SELECT username, rechten FROM users", $postData,
            "displayEditUser", "deleteUser", "username", null, null);
    }

/**
 * Function that displays the hardwaretable.
 * @param $postData
 */
function displayHardware($postData)
    {
        echo("Hier ziet u de hardware tabel hier kunt u via de knop edit de gewenste gegevens wijzigen en via de delete knop hardware verwijderen");
        new HelpdeskTable("Hardware", "SELECT * FROM hardware", $postData,
            "displayEditHardware", "deleteHardware", "id_hardware", null, "displayHardwareAndSoftware");
    }

/**
 * Function to display one hardware item and the installed software
 */
function displayHardwareAndSoftware($postData){
    $hardwareID = $_POST['key'];
    $query = "SELECT * FROM hardware WHERE id_hardware = '{$hardwareID}'";
    echo("De volgende tabel toont de details van de hardware:");
    new HelpdeskTable("Hardware item", $query, null, null, null, "id_hardware", null, null);

    $query = "SELECT software.id_software AS ID, software.naam, software.soort,
                     software.producent, software.leverancier, software.aantal_licenties AS Licenties,
                     software.soort_licentie AS Licentiesoort, software.aantal_gebruikers AS Gebruikers,
                     software.status
                     FROM hardware, software
                     WHERE hardware.os = software.id_software
                     AND hardware.id_hardware = '{$hardwareID}'";

    echo("De volgende tabel toont de informatie over het besturingssysteem:");
    new HelpdeskTable("Besturingsysteem", $query, null, null, null, "id_software", null, null);
    $query = "SELECT software.id_software AS ID, software.naam, software.soort,
                     software.producent, software.leverancier, software.aantal_licenties AS Licenties,
                     software.soort_licentie AS Licentiesoort, software.aantal_gebruikers AS Gebruikers,
                     software.status
                     FROM hardware_software, software
                     WHERE software.id_software = hardware_software.id_software
                     AND id_hardware='{$hardwareID}'";
    echo("De volgende tabel toont de software die op dit hardware item staan:");
    new HelpdeskTable("Software items", $query, null, null, null, "ID", null, null);
}

/**
 * Form where the user of the site can add hardware to the database.
 */
function displayAddHardware()
    {
        echo("Hier kunt u hardware toevoegen, u kunt de gegevens bevestigen door op submit te klikken");
        displayErrors();

        formHeader();
        textField("Hardware_ID", $_POST['Hardware_ID']);
        dropDownNoEmptyValue("Soort", queryToArray("SELECT soort FROM hardware GROUP BY soort"), $_POST['Soort']);
        dropDownNoEmptyValue("Locatie", queryToArray("SELECT locatie FROM hardware GROUP BY locatie"), $_POST['Locatie']);
        dropDown("OS", queryToArray("SELECT naam FROM software WHERE soort LIKE '%besturingssysteem%'"), $_POST['OS']);
        CheckBoxes("Software", queryToArray("SELECT naam FROM software WHERE soort NOT LIKE '%besturingssysteem%'"), 3, $_POST['Software']);
        textField("Merk", $_POST['Merk']);
        textField("Leverancier", $_POST['Leverancier']);
        numberField("Aanschaf_jaar", $_POST['Aanschaf_jaar']);
        textField("Status", $_POST['Status']);
        hiddenValue("display", "displayHardware");
        formFooter("addHardware");
    }

/**
* Function that creates a form where the values have been filled in. The user can change those values to change them in the hardwaretable.
 */
    function displayEditHardware()
    {
        echo("Hier kunt u hardware gegevens wijzigen, de gewijzigde gegevens kunt u bevestigen door op submit te klikken");
        global $con;
        displayErrors();

        $values = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM hardware WHERE id_hardware='".$_POST['key']."'"));
        $os = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM software WHERE id_software='".$values['os']."'"));

        formHeader();
        displayField("Hardware_ID", $values['id_hardware']);
        dropDownNoEmptyValue("Soort", queryToArray("SELECT soort FROM hardware GROUP BY soort"), $values['soort']);
        dropDownNoEmptyValue("Locatie", queryToArray("SELECT locatie FROM hardware GROUP BY locatie"), $values['locatie']);
        dropDown("OS", queryToArray("SELECT naam FROM software WHERE soort LIKE '%besturingssysteem%'"), $os['naam']);
        CheckBoxes("Software", queryToArray("SELECT naam FROM software WHERE soort NOT LIKE '%besturingssysteem%'"), 3,
                    queryToArray("SELECT software.naam FROM hardware_software, software WHERE software.id_software = hardware_software.id_software AND id_hardware='".$_POST['key']."'"));
        textField("Merk", $values['merk']);
        textField("Leverancier", $values['leverancier']);
        numberField("Aanschaf_jaar", $values['aanschaf_jaar']);
        textField("Status", $values['status']);
        hiddenValue("display", "displayHardware");
        formFooter("editHardware");
    }

/**
 * Function that changes the hardwaretable with the values from displayEditHardware.
 */
    function editHardware()
    {
        echo("Hier kunt u hardware gegevens wijzigen, de gewijzigde gegevens kunt u bevestigen door op submit te klikken");
        global $con;
        global $message;

        $valid = emptyCheck($_POST['Hardware_ID']);

        if($valid) $valid = emptyCheck($_POST['Soort']); $soort = removeMaliciousInput($_POST['Soort']);
        if(!emptyCheck($_POST['Soort'])){$message = $message."Soort mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Locatie']); $loc = removeMaliciousInput($_POST['Locatie']);
        if(!emptyCheck($_POST['Locatie'])){$message = $message."Locatie mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Leverancier']); $lev = removeMaliciousInput($_POST['Leverancier']);
        if(!emptyCheck($_POST['Leverancier'])){$message = $message."Leverancier mag niet leeg zijn<br/>";}

        if($valid) $valid = yearCheck($_POST['Aanschaf_jaar']); $jaar = removeMaliciousInput($_POST['Aanschaf_jaar']);
        if(!yearCheck($_POST['Aanschaf_jaar'])){$message = $message."Ongeldige aanschaf jaar<br/>";}

        $os = removeMaliciousInput($_POST['OS']);
        $status = removeMaliciousInput($_POST['Status']);
        $merk = removeMaliciousInput($_POST['Merk']);

        if($valid) {
            mysqli_query($con, "UPDATE hardware SET soort='".$soort."', locatie='".$loc."', os='".$os."', leverancier='".$lev."', aanschaf_jaar='".$jaar."', status='".$status.", merk='".$merk."'
                                WHERE id_hardware='".$_POST['Hardware_ID']."'");
        }

        if(!empty($_POST['Software'])) {
            mysqli_query($con, "DELETE FROM hardware_software WHERE id_hardware='".$_POST['Hardware_ID']."'");

            foreach($_POST['Software'] as $box) {
                $key = mysqli_fetch_assoc(mysqli_query($con, "SELECT id_software FROM software WHERE naam='".$box."'"));
                mysqli_query($con, "Insert INTO hardware_software (id_hardware, id_software)
                                    VALUES ('".$_POST['Hardware_ID']."','".$key['id_software']."')") or die(mysqli_error($con));
            }
        } else {
            $_POST['display'] = 'displayEditHardware';
        }
    }

    /**
     * This function will create a form to add a new user
     */
    function displayAddUser(){
        echo("Hier kunt u gebruikers toevoegen u kunt de gegevens bevestigen door op submit te klikken.<br/>");
        global $message;
        if($message != ""){
            echo($message);
            $message = "";
        }
        formHeader();
        textField("Gebruikersnaam", null);
        passwordField("password1");
        passwordField("password2");
        dropDownNoEmptyValue("Rechten", queryToArray("SELECT * FROM rechten"), null);
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

/**
 * This function shows a form to edit an existing user
 */
    function displayEditUser(){
        global $con;
        global $message;
        if($message != ""){
            echo($message);
            $message = "";
        }
        $primeKey = $_POST['key'];
        $query = "SELECT * FROM users WHERE username = '{$primeKey}'";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_assoc($result);
        echo("Je kunt nu deze gebruiker wijzigen. Om het wachtwoord te veranderen, voer een nieuw wachtwoord in. Anders zal het wachtwoord niet veranderen.");
        formHeader();
        echo $result['username'];
        textField("Gebruikersnaam", $result['username']);
        passwordField("password1");
        passwordField("password2");
        dropDownNoEmptyValue("Rechten", queryToArray("SELECT * FROM rechten"), $result['rechten']);
        hiddenValue("display", "displayUsers");
        formFooter("editUser");
    }

/**
 * Function that changes the usertable with the values from displayEditUser.
 */
    function editUser(){
        global $con;
        global $message;
        $message = "";
        $username = removeMaliciousInput($_POST['Gebruikersnaam']);
        $password1 = removeMaliciousInput($_POST['password1']);
        $password2 = removeMaliciousInput($_POST['password2']);
        $rechten = $_POST['Rechten'];

        $result = mysqli_query($con, "SELECT COUNT(*) FROM users WHERE username = '{$username}'") or die("Stuff");
        $result = mysqli_fetch_row($result);

        if($result[0] > 1){
            $message .= "ERROR: Deze gebruikersnaam bestaat al!";
        }
        if($password1 != $password2){
            $message .= "ERORR: De wachtwoorden komen niet overeen!";
        }
        if($message === ""){
            if($password1 != ""){
                $hash = password_encrypt($password1);
                mysqli_query($con, "UPDATE users
                                    SET username='{$username}', password='{$hash}', rechten='{$rechten}'
                                    WHERE username = '{$username}'") or die(mysqli_error($con));

                if (mysqli_connect_errno())
                {
                    $message .= "Gebruiker wijzigen mislukt. Probeer het opnieuw.";
                } else {
                    $message = "Gebruiker succesvol gewijzigd.";
                }
            } else {
                mysqli_query($con, "UPDATE users
                                        SET username='{$username}', rechten='{$rechten}'
                                        WHERE username = '{$username}") or die(mysqli_error($con));

                if (mysqli_connect_errno())
                {
                    $message .= "Gebruiker wijzigen mislukt. Probeer het opnieuw.";
                } else {
                    $message = "Gebruiker succesvol gewijzigd.";
                }
            }
        }
    }

/**
 * Function to add the hardware from the form to the hardwaretable.
 */
function addHardware()
    {
        global $con;
        global $message;

        $valid = emptyCheck($_POST['Hardware_ID']); $id = $_POST['Hardware_ID'];
        if(!emptyCheck($_POST['Hardware_ID'])){$message = $message."ID mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Soort']); $soort = removeMaliciousInput($_POST['Soort']);
        if(!emptyCheck($_POST['Soort'])){$message = $message."Soort mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Locatie']); $loc = removeMaliciousInput($_POST['Locatie']);
        if(!emptyCheck($_POST['Locatie'])){$message = $message."Locatie mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Leverancier']); $lev = removeMaliciousInput($_POST['Leverancier']);
        if(!emptyCheck($_POST['Leverancier'])){$message = $message."Leverancier mag niet leeg zijn<br/>";}

        if($valid) $valid = yearCheck($_POST['Aanschaf_jaar']); $jaar = removeMaliciousInput($_POST['Aanschaf_jaar']);
        if(!yearCheck($_POST['Aanschaf_jaar'])){$message = $message."Ongeldige aanschaf jaar<br/>";}

        $os = removeMaliciousInput($_POST['OS']);
        $status = removeMaliciousInput($_POST['Status']);
        $merk = removeMaliciousInput($_POST['Merk']);

        if($valid) {
            mysqli_query($con, "INSERT INTO hardware (id_hardware, soort, locatie, os, leverancier, aanschaf_jaar, status, merk)
                                VALUES('".$id."', '".$soort."', '".$loc."',
                                       '".$os."', '".$lev."', '".$jaar."',
                                       '".$status."', '".$merk."')") or die(mysqli_error($con));


            if(!empty($_POST['Software'])) {
                foreach($_POST['Software'] as $box) {
                    $key = mysqli_fetch_assoc(mysqli_query($con, "SELECT id_software FROM software WHERE naam='".$box."'"));
                    mysqli_query($con, "Insert INTO hardware_software (id_hardware, id_software)
                                        VALUES ('".$_POST['Hardware_ID']."','".$key['id_software']."')") or die('sw error');
                }
            }
        } else {
            $_POST['display'] = 'displayAddHardware';
        }
    }

/**
 * Function to add the software from the form to the softwaretable.
 */
function addSoftware()
    {
        global $con;

        global $message;

        $valid = emptyCheck($_POST['ID_Software']); $id = removeMaliciousInput($_POST['ID_Software']);
        if(!emptyCheck($_POST['ID_Software'])){$message = $message."ID mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Naam']); $naam = removeMaliciousInput($_POST['Naam']);
        if(!emptyCheck($_POST['Naam'])){$message = $message."Naam mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Soort']); $soort = removeMaliciousInput($_POST['Soort']);
        if(!emptyCheck($_POST['Soort'])){$message = $message."Soort mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Producent']); $pro = removeMaliciousInput($_POST['Producent']);
        if(!emptyCheck($_POST['Producent'])){$message = $message."Producent mag niet leeg zijn<br/>";}

        if($valid) $valid = emptyCheck($_POST['Leverancier']); $lev = removeMaliciousInput($_POST['Leverancier']);
        if(!emptyCheck($_POST['Leverancier'])){$message = $message."Leverancier mag niet leeg zijn<br/>";}

        if($valid) $valid = numberCheck($_POST['Aantal_Licenties']); $a_lic = removeMaliciousInput($_POST['Aantal_Licenties']);
        if(!numberCheck($_POST['Aantal_Licenties'])){$message = $message."Ongeldig aantal licenties<br/>";}

        $s_lic = removeMaliciousInput($_POST['Soort_Licentie']);
        $a_geb = removeMaliciousInput($_POST['Aantal_Gebruikers']);
        $status = removeMaliciousInput($_POST['Status']);

        if($valid) {
            mysqli_query($con, "INSERT INTO software (id_software, naam, soort, producent, leverancier, aantal_licenties, soort_licentie, aantal_gebruikers, status)
                                VALUES('".$id."', '".$naam."', '".$soort."',
                                       '".$pro."', '".$lev."', '".$a_lic."', '".$s_lic."', '".$a_geb."',
                                       '".$status."')") or die(mysqli_error($con));
        } else {
            $_POST['display'] = 'displayAddSoftware';
        }
    }


/**
 * Fucntion that deletes the selected hardware from the hardwaretable.
 */
function deleteHardware()
    {
        global $con;

        $primeKey = $_POST['key'];

        mysqli_query($con, "DELETE FROM hardware_software WHERE id_hardware='".$primeKey."'") or die('swdel error');
        mysqli_query($con, "DELETE FROM hardware WHERE id_hardware='".$primeKey."'") or die('hwdel error');
    }

/**
 * Fucntion that deletes the selected user from the usertable.
 */
    function deleteUser(){
        global $con;
        $primeKey = $_POST['key'];
        mysqli_query($con, "DELETE FROM users WHERE username='".$primeKey."'") or die('hwdel error');
    }

/**
 * Displays hello and the function of the user on the first page of the webpage.
 */
function displayLandingConfig()
    {
        echo "Hello ".ucfirst($_SESSION['user']);
    }

/**
 * Fucntion that deletes the selected software from the softwaretable.
 */
    function deleteSoftware()
    {
        global $con;

        $primeKey = $_POST['key'];
        mysqli_query($con, "DELETE FROM software WHERE id_software ='".$primeKey."'") or die(mysqli_error($con));
    }


?>