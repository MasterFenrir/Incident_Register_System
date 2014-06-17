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
}

/**
 * This function manages actions to be taken, like adding records or editing them.
 * @param $eventID
 */
function processEventIncident($eventID)
{
    switch($eventID) {
        case "editIncident" : editIncident();
        case "deleteIncident" : deleteIncident();
        case "addIncident" : addIncident();
    }
}

/**
 * This function manages the display of all the incidents known
 */
function displayIncidenten($postData){
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten", $postData, "displayEditIncident", "deleteIncident", "nummer");
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
    textField("Prioriteit", null);
    textField("Status", null);
    hiddenValue("display", "displayIncidenten");
    formFooter("addIncident");
}

function displayEditIncident() {
    global $con;
    $values = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM incidenten WHERE nummer='".$_POST['key']."'"));

    formHeader();
    displayField("Datum", $values['datum']);
    displayField("Aanvangtijd", $values['aanvang']);
    textField("Eindtijd", $values['eindtijd']);
    dropDown("Hardware", queryToArray("SELECT id_hardware FROM hardware"), $values['id_hardware']);
    textField("Omschrijving", $values['omschrijving']);
    textField("Workaround", $values['workaround']);
    textField("Contact", $values['contact']);
    textField("Prioriteit", $values['prioriteit']);
    textField("Status", $values['status']);
    hiddenValue("display", "displayIncidenten");
    hiddenValue("key", $values['nummer']);
    formFooter("editIncident");
}

/**
 * This functions displays all the incidents that have no priority yet.
 */
function displayMeldingen($postData)
{
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten WHERE prioriteit = NULL OR prioriteit = ''", $postData, "displayEditIncident", "deleteIncident", "nummer");
}

function deleteIncident()
{
    global $con;
    mysqli_query($con, "DELETE FROM incidenten WHERE nummer ='".$_POST['key']."'");
}

function editIncident()
{
    global $con;

    $valid = emptyCheck($_POST['Aanvangtijd']); $aanvang = removeMaliciousInput($_POST['Aanvangtijd']);
    $valid = emptyCheck($_POST['Hardware']); $hw = removeMaliciousInput($_POST['Hardware']);
    $valid = emptyCheck($_POST['Omschrijving']); $omschrijving = removeMaliciousInput($_POST['Omschrijving']);

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

        mysqli_query($con, "UPDATE incidenten SET datum='".$datum."', aanvang='".$aanvang."', eindtijd='".$eind."',
                            id_hardware='".$hw."', omschrijving='".$omschrijving."', workaround='".$wa."',
                            contact='".$cont."', prioriteit='".$prio."', status='".$status."'
                            WHERE nummer ='".$_POST['key']."'") or die(mysqli_error($con));
    }
}

function addIncident()
{
    global $con;

    $valid = emptyCheck($_POST['Aanvangtijd']); $aanvang = removeMaliciousInput($_POST['Aanvangtijd']);
    $valid = emptyCheck($_POST['Hardware']); $hw = removeMaliciousInput($_POST['Hardware']);
    $valid = emptyCheck($_POST['Omschrijving']); $omschrijving = removeMaliciousInput($_POST['Omschrijving']);
    $valid = validateDate($_POST['day'], $_POST['month'], $_POST['year']);

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

        mysqli_query($con, "INSERT INTO incidenten (datum, aanvang, eindtijd, id_hardware, omschrijving, workaround, contact, prioriteit, status)
                                VALUES('".$datum."', '".$aanvang."', '".$eind."',
                                       '".$hw."', '".$omschrijving."', '".$wa."',
                                       '".$cont."', '".$prio."', '".$status."')") or die(mysqli_error($con));
    }
}

?>