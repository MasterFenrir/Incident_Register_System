<?php
/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 12-6-14
 * Time: 11:51
 */

/**
 * This function manages the content to be displayed for the people that have acces to Incident Management
 * @param $postData
 */
function displayContentIncident($postData)
{
    switch($postData) {
        case "displayIncidenten" : displayIncidenten($postData); break;
        case "displayAddIncident" : displayAddIncident(); break;
        case "displayEditIncident" : displayEditIncident(); break;
        case "displayMeldingen" : displayMeldingen($postData); break;
        case "displaySearch" : displaySearchIncidenten($postData); break;
        case "displayHardware" : displayHardware($postData); break;
        case "displaySoftware" : displaySoftware($postData); break;
        default : echo "Hello ".ucfirst($_SESSION['user']); break;
    }
}

/**
 * This functions manages the sidebar to be displayed for the people that have access to Incident Management
 */
function displayMenuIncident()
{
    new Button("Incidenten","display", "displayIncidenten");
    new Button("Incident toevoegen","display", "displayAddIncident");
    new Button("Meldingen","display", "displayMeldingen");
    new Button("Hardware","display", "displayHardware");
    new Button("Software","display", "displaySoftware");
}

/**
 * This function manages actions to be taken, like adding records or editing them.
 * @param $eventID
 */
function processEventIncident($eventID)
{
    switch($eventID) {
        case "editIncident" : editIncident(); break;
        case "deleteIncident" : deleteIncident(); break;
        case "addIncident" : addIncident(); break;
    }
}

function displaySearchIncidenten($postData)
{
    $search = $_POST['search'];
    new HelpdeskTable("Incidenten", "SELECT nummer, datum, aanvang, eindtijd, incidenten.id_hardware, incidenten.omschrijving, workaround, probleem, contact, prioriteit, incidenten.status
                                     FROM incidenten, hardware WHERE incidenten.id_hardware = hardware.id_hardware AND nummer LIKE '%".$search."%'
                                     OR datum LIKE '%".$search."%' OR aanvang LIKE '%".$search."%' OR eindtijd LIKE '%".$search."%' OR incidenten.id_hardware LIKE '%".$search."%'
                                     OR incidenten.omschrijving LIKE '%".$search."%' OR workaround LIKE '%".$search."%' OR probleem LIKE '%".$search."%'
                                     OR contact LIKE '%".$search."%' OR prioriteit LIKE '%".$search."%' OR incidenten.status LIKE '%".$search."%' OR hardware.soort LIKE '%".$search."%'
                                     GROUP BY incidenten.nummer",
                                     $postData, "displayEditIncident", "deleteIncident", "nummer", $search, null);
}



/**
 * This function manages the display of all the incidents known
 */
function displayIncidenten($postData){
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten", $postData, "displayEditIncident", "deleteIncident", "nummer", null, null);
}

/*
 * This functions displays the form to add an incident
 */
function displayAddIncident(){
    date_default_timezone_set("Europe/Amsterdam");

    formHeader();
    dateField(null,null,null);
    textField("Aanvangtijd",date('H:i'));
    textField("Eindtijd", null);
    dropDown("Hardware", queryToArray("SELECT id_hardware FROM hardware"), null);
    textField("Omschrijving", null);
    textField("Workaround", null);
    textField("Contact", null);
    dropDown("Prioriteit", queryToArray("SELECT prioriteit FROM prioriteiten"), null);
    textField("Status", null);
    hiddenValue("display", "displayIncidenten");
    formFooter("addIncident");
}

function displayEditIncident() {
    global $con;
    global $message;

    if(!empty($message)) {
        echo $message;
        $message = '';
    }
    $values = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM incidenten WHERE nummer='".$_POST['key']."'"));

    $date = explode("-", $values['datum']);
    $day = $date[0];
    $month = $date[1];
    $year = $date[2];

    formHeader();
    dateField($day, $month, $year);
    displayField("Aanvangtijd", $values['aanvang']);
    textField("Eindtijd", $values['eindtijd']);
    dropDown("Hardware", queryToArray("SELECT id_hardware FROM hardware"), $values['id_hardware']);
    textField("Omschrijving", $values['omschrijving']);
    textField("Workaround", $values['workaround']);
    textField("Contact", $values['contact']);
    dropdown("Prioriteit", queryToArray("SELECT prioriteit FROM prioriteiten"), $values['prioriteit']);
    dropdown("Status", queryToArray("SELECT status From "),$values['status']);
    hiddenValue("display", "displayIncidenten");
    hiddenValue("key", $values['nummer']);
    formFooter("editIncident");
}

/**
 * This functions displays all the incidents that have no priority yet.
 */
function displayMeldingen($postData)
{
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten WHERE prioriteit IS NULL", $postData, "displayEditIncident", "deleteIncident", "nummer", null, null);
}

function deleteIncident()
{
    global $con;
    mysqli_query($con, "DELETE FROM incidenten WHERE nummer ='".$_POST['key']."'");
}

function editIncident()
{
    global $con;
    global $message;

    $valid = emptyCheck($_POST['Aanvangtijd']); $aanvang = removeMaliciousInput($_POST['Aanvangtijd']);
    if(!emptyCheck($_POST['Aanvangtijd'])){$message = $message."Aanvangtijd mag niet leeg zijn<br>";}

    if($valid){$valid = emptyCheck($_POST['Hardware']);} $hw = removeMaliciousInput($_POST['Hardware']);
    if(!emptyCheck($_POST['Hardware'])){$message = $message."Hardware mag niet leeg zijn<br>";}

    if($valid){$valid = emptyCheck($_POST['Omschrijving']);} $omschrijving = removeMaliciousInput($_POST['Omschrijving']);
    if(!emptyCheck($_POST['Omschrijving'])){$message = $message."Omschrijving mag niet leeg zijn<br>";}

    if($valid){$valid = validateDate($_POST['day'], $_POST['month'], $_POST['year']);}
    if(!validateDate($_POST['day'], $_POST['month'], $_POST['year'])){$message = $message."Ongeldige datum<br>";}

    if($valid) {
        $day = removeMaliciousInput($_POST['day']);
        $month = removeMaliciousInput($_POST['month']);
        $year = removeMaliciousInput($_POST['year']);

        $wa = removeMaliciousInput($_POST['Workaround']);
        $cont = removeMaliciousInput($_POST['Contact']);
        $prio = removeMaliciousInput($_POST['Prioriteit']);
        $status = removeMaliciousInput($_POST['Status']);

        $query = "SELECT tijd FROM prioriteiten WHERE prioriteit = {$prio}";
        $result = mysqli_fetch_array(mysqli_query($con, $query));
        $eind = addTimes($day, $month, $year, $aanvang, $result[0]);
        echo("editIncident <br/>");
        var_dump($eind);
        $datum = $eind['day']."-".$eind['month']."-".$eind['year'];
        $eindtijd = $eind['hour'].":".$eind['minutes'];

        if(!empty($prio)) {
            mysqli_query($con, "UPDATE incidenten SET datum='".$datum."', aanvang='".$aanvang."', eindtijd='".$eindtijd."',
                                id_hardware='".$hw."', omschrijving='".$omschrijving."', workaround='".$wa."',
                                contact='".$cont."', status='".$status."', prioriteit='".$prio."'
                                WHERE nummer ='".$_POST['key']."' ") or die(mysqli_error($con));
        } else {
            mysqli_query($con, "UPDATE incidenten SET datum='".$datum."', aanvang='".$aanvang."', eindtijd='".$eind."',
                                id_hardware='".$hw."', omschrijving='".$omschrijving."', workaround='".$wa."',
                                contact='".$cont."', status='".$status."'
                                WHERE nummer ='".$_POST['key']."'") or die(mysqli_error($con));
        }
    } else {
        $_POST['display'] = "displayEditIncident";
    }
}

function addIncident()
{
    global $con;

    $valid = emptyCheck($_POST['Aanvangtijd']); $aanvang = removeMaliciousInput($_POST['Aanvangtijd']);
    if($valid) $valid = emptyCheck($_POST['Hardware']); $hw = removeMaliciousInput($_POST['Hardware']);
    if($valid) $valid = emptyCheck($_POST['Omschrijving']); $omschrijving = removeMaliciousInput($_POST['Omschrijving']);
    if($valid) $valid = validateDate($_POST['day'], $_POST['month'], $_POST['year']);

    if($valid) {
        $day = removeMaliciousInput($_POST['day']);
        $month = removeMaliciousInput($_POST['month']);
        $year = removeMaliciousInput($_POST['year']);
        $datum = $day."-".$month."-".$year;

        $wa = removeMaliciousInput($_POST['Workaround']);
        $cont = removeMaliciousInput($_POST['Contact']);
        $prio = removeMaliciousInput($_POST['Prioriteit']);
        $status = removeMaliciousInput($_POST['Status']);
        $eind = removeMaliciousInput($_POST['Eindtijd']);

        if(!empty($prio)) {
        mysqli_query($con, "INSERT INTO incidenten (datum, aanvang, eindtijd, id_hardware, omschrijving, workaround, contact, prioriteit, status)
                                VALUES('".$datum."', '".$aanvang."', '".$eind."',
                                       '".$hw."', '".$omschrijving."', '".$wa."',
                                       '".$cont."', '".$prio."', '".$status."')") or die(mysqli_error($con));
        } else {
            mysqli_query($con, "INSERT INTO incidenten (datum, aanvang, eindtijd, id_hardware, omschrijving, workaround, contact, status)
                                VALUES('".$datum."', '".$aanvang."', '".$eind."',
                                       '".$hw."', '".$omschrijving."', '".$wa."',
                                       '".$cont."', '".$status."')") or die(mysqli_error($con));
        }
    }







}

?>