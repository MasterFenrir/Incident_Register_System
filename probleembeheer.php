<?php
/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 12-6-14
 * Time: 11:49
 */

function displayContentProbleem($postData)
{
    switch($postData) {
        case "displayProblemen" : new HelpdeskTable("Problemen", "SELECT * FROM problemen", "displayProblemen", null, null, null, null, null); break;
        case "displayEditIncidentStatus"    : displayEditIncidentStatus(); break;
        case "displayIncidentenProblems"    : displayIncidentProblems($postData); break;
        default : displayLandingProbleem();
    }
}

function displayMenuProbleem()
{
    new Button("Problemen", "display", "displayProblemen");
    new Button("Incidenten", "display", "displayIncidentenProblems");
}

function processEventProbleem($eventID)
{
    switch($eventID){
        case "editIncidentStatus"   : editIncidentStatus(); break;
    }
}

function displayIncidentProblems($postData){
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten", $postData, "displayEditIncidentStatus", null, "nummer", null, null);
}

function displayEditIncidentStatus(){
    global $con;
    $values = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM incidenten WHERE nummer='".$_POST['key']."'"));

    $date = explode("-", $values['datum']);
    $day = $date[0];
    $month = $date[1];
    $year = $date[2];

    formHeader();
    displayValue("Dag", $day);
    displayValue("Maand", $month);
    displayValue("Jaar", $year);
    displayValue("Aanvangtijd", $values['aanvang']);
    displayValue("Eindtijd", $values['eindtijd']);
    displayValue("Hardware",$values['id_hardware']);
    displayValue("Omschrijving", $values['omschrijving']);
    displayValue("Workaround", $values['workaround']);
    displayValue("Contact", $values['contact']);
    displayValue("Prioriteit", $values['prioriteit']);
    dropdown("Status", queryToArray("SELECT status From statussen"),$values['status']);
    hiddenValue("display", "displayIncidenten");
    hiddenValue("key", $values['nummer']);
    formFooter("changeStateIncident");
}

function editIncidentStatus(){
    global $con;
    mysqli_query($con, "UPDATE incidenten SET status='".$_POST['Status']."'
                        WHERE nummer ='".$_POST['key']."' ") or die(mysqli_error($con));

    $_POST['display'] = "displaytIncidentProblems";
}

function displayLandingProbleem()
{
    $sel = array('hardware.id_hardware', 'hardware.soort', 'hardware.locatie', 'hardware.os',
                 'hardware.merk', 'hardware.leverancier', 'hardware.aanschaf_jaar');
    $from = array('hardware'=>'id_hardware', 'hardware_software'=>'id_software', 'software'=>'id_software');
    $cols = array('hardware.id_hardware', 'hardware.soort', 'hardware.locatie', 'hardware.os', 'hardware.merk',
                  'hardware.leverancier', 'hardware.aanschaf_jaar', 'hardware.status', 'software.naam');
    $type = 'OR';
    $grp = 'id_hardware';
    $search = "werks xp grol 08 off";

    echo monsterQueryBuilder($sel, $from, $cols, $type, $grp, $search);
}