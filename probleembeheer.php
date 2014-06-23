<?php
/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 12-6-14
 * Time: 11:49
 */


/**
 * Function that handles what to display onscreen
 * @param $postData The function to display
 */
function displayContentProbleem($postData)
{
    switch($postData) {
        case "displayProblemMeldingen"  : displayProblemMeldingen($postData); break;
        case "displayProblems" : displayProblems($postData); break;
        case "displayEditIncidentStatus"    : displayEditIncidentStatus(); break;
        case "displayIncidentProblems"    : displayIncidentProblems($postData); break;
        case "displayProblemDetails"    : displayProblemDetails($postData); break;
        case "displayHardwareProblem" : displayHardwareProblem($postData); break;
        case "displaySoftwareProblem" : displaySoftwareProblem($postData); break;
        case "displayHardwareAndSoftware" : displayHardwareAndSoftware($postData); break;
        case "displayAddProblem"    : displayAddProblem(); break;
        case "displayEditProblem"   : displayEditProblem(); break;
        default : displayLandingProbleem();
    }
}

/**
 * This function displays the buttons on the left
 */
function displayMenuProbleem()
{
    new Button("Meldingen", "display", "displayProblemMeldingen");
    new Button("Problemen", "display", "displayProblems");
    new Button("Probleem toevoegen", "display", "displayAddProblem");
    new Button("Incidenten", "display", "displayIncidentProblems");
    new Button("Hardware","display", "displayHardwareProblem");
    new Button("Software","display", "displaySoftwareProblem");
}

/**
 * This function process functions that do not output anything to the screen
 * @param $eventID The function to execute
 */
function processEventProbleem($eventID)
{
    switch($eventID){
        case "editIncidentStatus"   : editIncidentStatus(); break;
        case "addProblem"   : addProblem(); break;
        case "editProblem"  : editProblem(); break;
    }
}

/**
 * This function displays the incidents that have been upgraded to problems by incidentmanagement
 * @param $postData This is used for creating the sorting functionality in the displayed table
 */
function displayProblemMeldingen($postData){
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten WHERE status = 'Probleem' AND probleem IS NULL", $postData, "displayEditIncidentStatus", null, "nummer", null, null);
}

/**
 * This function displays the table of problems
 * @param $postData This is used for creating the sorting functionality in the displayed table
 */
function displayProblems($postData){
    new HelpdeskTable("Problemen", "SELECT * FROM problemen", $postData, "displayEditProblem", null, "nummer", null, "displayProblemDetails");
}

/**
 * This function displays incidents. The user can upgrade these incidents to problems and connect them to existing problem records
 * @param $postData This is used for creating the sorting functionality in the displayed table
 */
function displayIncidentProblems($postData){
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten", $postData, "displayEditIncidentStatus", null, "nummer", null, null);
}

/**
 * This function displays a problem and the related incidents.
 * @param $postData This is used for creating the sorting functionality in the displayed table
 */
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

/**
 * This function displays the form to edit the status of an incident.
 */
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

/**
 * This function executes the changing of the Incident status.
 */
function editIncidentStatus(){
    global $con;
    mysqli_query($con, "UPDATE incidenten SET status='{$_POST['Status']}', probleem='{$_POST['Probleem']}'
                        WHERE nummer ='".$_POST['key']."' ") or die(mysqli_error($con));

    $_POST['display'] = "displayIncidentProblems";
}

/**
 * This function shows a table of the existing hardware.
 * @param $postData This is used for creating the sorting functionality in the displayed table
 */
function displayHardwareProblem($postData)
{
    new HelpdeskTable("Hardware", "SELECT * FROM hardware", $postData,
        null, null, "id_hardware", null, "displayHardwareAndSoftware");
}

/**
 * This function shows a table of the existing software.
 * @param $postData This is used for creating the sorting functionality in the displayed table
 */
function displaySoftwareProblem($postData)
{
    new HelpdeskTable("Software", "SELECT id_software AS ID, naam, soort,
                                          producent, leverancier, aantal_licenties AS Licenties,
                                          soort_licentie AS Licentiesoort, aantal_gebruikers AS Gebruikers,
                                          status
                                          FROM software", $postData,
        null, null, "id_software", null, null);
}

/**
 * Function to display the form to add problems to the database.
 */
function displayAddProblem(){
    displayErrors();
    date_default_timezone_set("Europe/Amsterdam");

    formHeader();
    dateField($_POST['day'],$_POST['month'],$_POST['year']);
    textField("Aanvangtijd",date('H:i'));
    textField("Omschrijving", $_POST['Omschrijving']);
    dropDown("Prioriteit", queryToArray("SELECT prioriteit FROM prioriteiten"), $_POST['Prioriteit']);
    dropdown("Status", queryToArray("SELECT status From statussen"), "onopgelost");
    hiddenValue("display", "displayProblems");
    formFooter("addProblem");
}

/**
 * This function executes the adding of a problem to the database
 */
function addProblem(){
    global $con;
    global $message;

    $valid = emptyCheck($_POST['Aanvangtijd']); $aanvang = removeMaliciousInput($_POST['Aanvangtijd']);
    if(!emptyCheck($_POST['Aanvangtijd'])){$message = $message."<li>Aanvangtijd mag niet leeg zijn</li>";}

    if($valid){$valid = emptyCheck($_POST['Omschrijving']);} $omschrijving = removeMaliciousInput($_POST['Omschrijving']);
    if(!emptyCheck($_POST['Omschrijving'])){$message = $message."<li>Omschrijving mag niet leeg zijn</li>";}

    if($valid){$valid = validateDate($_POST['day'], $_POST['month'], $_POST['year']);}
    if(!validateDate($_POST['day'], $_POST['month'], $_POST['year'])){$message = $message."<li>Ongeldige datum</li>";}

    if($valid) {
        $day = removeMaliciousInput($_POST['day']);
        $month = removeMaliciousInput($_POST['month']);
        $year = removeMaliciousInput($_POST['year']);
        $prio = removeMaliciousInput($_POST['Prioriteit']);
        $status = removeMaliciousInput($_POST['Status']);

        if(!empty($prio)) {
            $query = "SELECT tijd FROM prioriteiten WHERE prioriteit = {$prio}";
            $result = mysqli_fetch_array(mysqli_query($con, $query));
            $eind = addTimes($day, $month, $year, $aanvang, $result[0]);
            $datum = $eind['day']."-".$eind['month']."-".$eind['year'];
            $eindtijd = $eind['hour'].":".$eind['minutes'];
            mysqli_query($con, "INSERT INTO problemen (datum, aanvang, eindtijd, omschrijving, prioriteit, status)
                                VALUES('".$datum."', '".$aanvang."', '".$eindtijd."',
                                       '".$omschrijving."', '".$prio."', '".$status."')") or die(mysqli_error($con));
        } else {
            $datum = $day."-".$month."-".$year;
            mysqli_query($con, "INSERT INTO problemen (datum, aanvang, eindtijd, omschrijving, status)
                                VALUES('".$datum."', '".$aanvang."', '',
                                       '".$omschrijving."', '".$status."')") or die(mysqli_error($con));
        }
        $_POST['display'] = "displayProblems";
    } else {
        $_POST['display'] = "displayAddProblem";
    }
}

/**
 * This function displays the form to edit an existing problem.
 */
function displayEditProblem() {
    global $con;
    displayErrors();

    $values = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM problemen WHERE nummer='".$_POST['key']."'"));

    $date = explode("-", $values['datum']);
    $day = $date[0];
    $month = $date[1];
    $year = $date[2];

    formHeader();
    dateField($day, $month, $year);
    displayField("Aanvangtijd", $values['aanvang']);
    hiddenValue("Aanvangtijd", $values['aanvang']);
    textField("Omschrijving", $values['omschrijving']);
    dropdown("Prioriteit", queryToArray("SELECT prioriteit FROM prioriteiten"), $values['prioriteit']);
    dropdown("Status", queryToArray("SELECT status From statussen"),$values['status']);
    hiddenValue("display", "displayProblems");
    hiddenValue("key", $values['nummer']);
    formFooter("editProblem");
}

/**
 * This function executes the change of a problem in the database
 */
function editProblem()
{
    global $con;
    global $message;

    $valid = emptyCheck($_POST['Omschrijving']); $omschrijving = removeMaliciousInput($_POST['Omschrijving']);
    if(!emptyCheck($_POST['Omschrijving'])){$message = $message."<li>Omschrijving mag niet leeg zijn</li>";}

    if($valid){$valid = validateDate($_POST['day'], $_POST['month'], $_POST['year']);}
    if(!validateDate($_POST['day'], $_POST['month'], $_POST['year'])){$message = $message."<li>Ongeldige datum</li>";}

    if($valid) {
        $day = removeMaliciousInput($_POST['day']);
        $month = removeMaliciousInput($_POST['month']);
        $year = removeMaliciousInput($_POST['year']);

        $wa = removeMaliciousInput($_POST['Workaround']);
        $cont = removeMaliciousInput($_POST['Contact']);
        $prio = removeMaliciousInput($_POST['Prioriteit']);
        $status = removeMaliciousInput($_POST['Status']);
        $aanvang = $_POST['Aanvangtijd'];

        if(!empty($prio)) {
            $query = "SELECT tijd FROM prioriteiten WHERE prioriteit = {$prio}";
            $result = mysqli_fetch_array(mysqli_query($con, $query));
            $eind = addTimes($day, $month, $year, $aanvang, $result[0]);
            $datum = $eind['day']."-".$eind['month']."-".$eind['year'];
            $eindtijd = $eind['hour'].":".$eind['minutes'];
            mysqli_query($con, "UPDATE problemen SET datum='".$datum."', aanvang='".$aanvang."', eindtijd='".$eindtijd."',
                                       omschrijving='".$omschrijving."', prioriteit='".$prio."', status='".$status."'
                                       WHERE nummer ='".$_POST['key']."'") or die(mysqli_error($con));
        } else {
            $datum = $day."-".$month."-".$year;
            mysqli_query($con, "UPDATE problemen SET datum='".$datum."', aanvang='".$aanvang."', eindtijd='',
                                       omschrijving='".$omschrijving."', prioriteit=NULL, status='".$status."'
                                       WHERE nummer ='".$_POST['key']."'") or die(mysqli_error($con));
        }
        $_POST['display'] = "displayProblems";
    } else {
        $_POST['display'] = "displayEditIncident";
    }
}

/**
 * This is the basic function that displays when the user logs in.
 */
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