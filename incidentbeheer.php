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
    formHeader();
    dateField(null,null,null);
    textField("Aanvang tijd", null);
    textField("Eind tijd", null);
    dropDown("Hardware", queryToArray("SELECT id_hardware FROM hardware"), null);
    textField("Omschrijving", null);
    textField("Workaround", null);
    textField("Contact", null);
    textField("Prioriteit", null);
    textField("Status", null);
    formFooter("addIncident");
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

}

function addIncident()
{

}

?>