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
        case "displayIncidenten" : new HelpdeskTable("Incidenten", "SELECT * FROM incidenten", "displayIncidenten", null, null, null); break;
        default : echo "Hello ".ucfirst($_SESSION['user']);
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
    switch($eventID){

    }
}

/**
 * This function manages the display of all the incidents known
 */
function displayIncidenten(){

}

/*
 * This functions displays the form to add an incident
 */
function displayAddIncident(){

}

/**
 * This functions displays all the incidents that have no priority yet.
 */
function displayMeldingen(){

}

?>