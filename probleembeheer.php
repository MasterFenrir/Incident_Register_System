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
        case "displayProblemMeldingen"  : displayProblemMeldingen($postData); break;
        case "displayProblemen" : displayProblems($postData); break;
        case "displayEditIncidentStatus"    : displayEditIncidentStatus(); break;
        case "displayIncidentProblems"    : displayIncidentProblems($postData); break;
        case "displayProblemDetails"    : displayProblemDetails($postData); break;
        case "displayHardwareProblem" : displayHardwareProblem($postData); break;
        case "displaySoftwareProblem" : displaySoftwareProblem($postData); break;
        case "displayHardwareAndSoftware" : displayHardwareAndSoftware($postData); break;
        default : displayLandingProbleem();
    }
}

function displayMenuProbleem()
{
    new Button("Meldingen", "display", "displayProblemMeldingen");
    new Button("Problemen", "display", "displayProblemen");
    new Button("Incidenten", "display", "displayIncidentProblems");
    new Button("Hardware","display", "displayHardwareProblem");
    new Button("Software","display", "displaySoftwareProblem");
}

function processEventProbleem($eventID)
{
    switch($eventID){
        case "editIncidentStatus"   : editIncidentStatus(); break;
    }
}

function displayProblemMeldingen($postData){
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten WHERE status = 'Probleem' AND probleem IS NULL", $postData, "displayEditIncidentStatus", null, "nummer", null, null);
}

function displayProblems($postData){
    new HelpdeskTable("Problemen", "SELECT * FROM problemen", $postData, null, null, "nummer", null, "displayProblemDetails");
}

function displayIncidentProblems($postData){
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten", $postData, "displayEditIncidentStatus", null, "nummer", null, null);
}

function displayProblemDetails($postData){
    $query = "SELECT * FROM problemen
              WHERE nummer = '{$_POST['key']}'";
    echo("Hieronder staan de details van het probleem:");
    new HelpdeskTable("Probleem", $query, $postData, null, null, "nummer", null, null);
    $query = "SELECT * FROM incidenten
              WHERE probleem = '{$_POST['key']}'";
    echo("Hieronder staan de gerelateerde incidenten:");
    new HelpdeskTable("Incidenten", $query, $postData, null, null, "nummer", null, null);
}

function displayEditIncidentStatus(){
    global $con;
    $values = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM incidenten WHERE nummer='".$_POST['key']."'"));

    $date = explode("-", $values['datum']);
    $day = $date[0];
    $month = $date[1];
    $year = $date[2];

    formHeader();
    displayField("Dag", $day);
    displayField("Maand", $month);
    displayField("Jaar", $year);
    displayField("Aanvangtijd", $values['aanvang']);
    displayField("Eindtijd", $values['eindtijd']);
    displayField("Hardware",$values['id_hardware']);
    displayField("Omschrijving", $values['omschrijving']);
    displayField("Workaround", $values['workaround']);
    dropdown("Probleem", queryToArray("SELECT nummer FROM problemen"), null);
    displayField("Contact", $values['contact']);
    displayField("Prioriteit", $values['prioriteit']);
    dropdown("Status", queryToArray("SELECT status From statussen"),$values['status']);
    hiddenValue("display", "displayIncidenten");
    hiddenValue("key", $values['nummer']);
    formFooter("editIncidentStatus");
}

function editIncidentStatus(){
    global $con;
    mysqli_query($con, "UPDATE incidenten SET status='{$_POST['Status']}', probleem='{$_POST['Probleem']}'
                        WHERE nummer ='".$_POST['key']."' ") or die(mysqli_error($con));

    $_POST['display'] = "displayIncidentProblems";
}

function displayHardwareProblem($postData)
{
    new HelpdeskTable("Hardware", "SELECT * FROM hardware", $postData,
        null, null, "id_hardware", null, "displayHardwareAndSoftware");
}

function displaySoftwareProblem($postData)
{
    new HelpdeskTable("Software", "SELECT id_software AS ID, naam, soort,
                                          producent, leverancier, aantal_licenties AS Licenties,
                                          soort_licentie AS Licentiesoort, aantal_gebruikers AS Gebruikers,
                                          status
                                          FROM software", $postData,
        null, null, "id_software", null, null);
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