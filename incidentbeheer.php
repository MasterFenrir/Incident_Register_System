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
        case "displayHardwareIncident" : displayHardwareIncident($postData); break;
        case "displaySoftwareIncident" : displaySoftwareIncident($postData); break;
        case "displayHardwareAndSoftware" : displayHardwareAndSoftware($postData); break;
        case "displaySolvedProblems"    : displaySolvedProblems($postData); break;
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
    new Button("Hardware","display", "displayHardwareIncident");
    new Button("Software","display", "displaySoftwareIncident");
    new Button("Opgeloste problemen", "display", "displaySolvedProblems");
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
    new HelpdeskTable("Incidenten", makeSearchIncidenten($_POST['search']), null,
                      "displayEditIncident", "deleteIncident", "nummer", $_POST['search'], null);

    echo "<br/>";

    displaySearchConfig($postData);
}

function makeSearchIncidenten($search)
{
    $select = array('incidenten.nummer', 'incidenten.datum', 'incidenten.aanvang', 'incidenten.id_hardware', 'incidenten.omschrijving',
                    'incidenten.workaround', 'incidenten.probleem', 'incidenten.contact, incidenten.prioriteit, incidenten.status');
    $from = array('hardware'=>'id_hardware', 'incidenten'=>'nummer');
    $cols = array('incidenten.nummer', 'incidenten.datum', 'incidenten.aanvang', 'incidenten.eindtijd', 'incidenten.id_hardware',
                  'incidenten.omschrijving', 'incidenten.workaround', 'incidenten.probleem', 'incidenten.prioriteit', 'incidenten.status',
                  'hardware.soort', 'incidenten.contact');
    $grp = 'incidenten.nummer';

    return monsterQueryBuilder($select, $from, $cols, 'AND', $grp, $search);
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
function displayAddIncident() {
    displayErrors();
    date_default_timezone_set("Europe/Amsterdam");

    formHeader();
    dateField($_POST['day'],$_POST['month'],$_POST['year']);
    textField("Aanvangtijd",date('H:i'));
    dropDown("Hardware", queryToArray("SELECT id_hardware FROM hardware"), $_POST['Hardware']);
    textField("Omschrijving", $_POST['Omschrijving']);
    textField("Workaround", $_POST['Workaround']);
    textField("Contact", $_POST['Contact']);
    dropDown("Prioriteit", queryToArray("SELECT prioriteit FROM prioriteiten"), $_POST['Prioriteit']);
    dropdown("Status", queryToArray("SELECT status From statussen"), "onopgelost");
    hiddenValue("display", "displayIncidenten");
    formFooter("addIncident");
}

function displayEditIncident() {
    global $con;
    displayErrors();

    $values = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM incidenten WHERE nummer='".$_POST['key']."'"));

    $date = explode("-", $values['datum']);
    $day = $date[0];
    $month = $date[1];
    $year = $date[2];

    formHeader();
    dateField($day, $month, $year);
    displayField("Aanvangtijd", $values['aanvang']);
    hiddenValue("Aanvangtijd", $values['aanvang']);
    dropDown("Hardware", queryToArray("SELECT id_hardware FROM hardware"), $values['id_hardware']);
    textField("Omschrijving", $values['omschrijving']);
    textField("Workaround", $values['workaround']);
    textField("Contact", $values['contact']);
    dropdown("Prioriteit", queryToArray("SELECT prioriteit FROM prioriteiten"), $values['prioriteit']);
    dropdown("Status", queryToArray("SELECT status From statussen"),$values['status']);
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

    $valid = emptyCheck($_POST['Hardware']); $hw = removeMaliciousInput($_POST['Hardware']);
    if(!emptyCheck($_POST['Hardware'])){$message = $message."<li>Hardware mag niet leeg zijn</li>";}

    if($valid){$valid = emptyCheck($_POST['Omschrijving']);} $omschrijving = removeMaliciousInput($_POST['Omschrijving']);
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
            mysqli_query($con, "UPDATE incidenten SET datum='".$datum."', aanvang='".$aanvang."', eindtijd='".$eindtijd."',
                                id_hardware='".$hw."', omschrijving='".$omschrijving."', workaround='".$wa."',
                                contact='".$cont."', status='".$status."', prioriteit='".$prio."'
                                WHERE nummer ='".$_POST['key']."' ") or die(mysqli_error($con));
        } else {
            $datum = $day."-".$month."-".$year;
            mysqli_query($con, "UPDATE incidenten SET datum='".$datum."', aanvang='".$aanvang."',
                                id_hardware='".$hw."', omschrijving='".$omschrijving."', workaround='".$wa."',
                                contact='".$cont."', prioriteit=NULL, status='".$status."'
                                WHERE nummer ='".$_POST['key']."'") or die(mysqli_error($con));
        }
    } else {
        $_POST['display'] = "displayEditIncident";
    }
}

function addIncident()
{
    global $con;
    global $message;

    $valid = emptyCheck($_POST['Aanvangtijd']); $aanvang = removeMaliciousInput($_POST['Aanvangtijd']);
    if(!emptyCheck($_POST['Aanvangtijd'])){$message = $message."<li>Aanvangtijd mag niet leeg zijn</li>";}

    if($valid){$valid = emptyCheck($_POST['Hardware']);} $hw = removeMaliciousInput($_POST['Hardware']);
    if(!emptyCheck($_POST['Hardware'])){$message = $message."<li>Hardware mag niet leeg zijn</li>";}

    if($valid){$valid = emptyCheck($_POST['Omschrijving']);} $omschrijving = removeMaliciousInput($_POST['Omschrijving']);
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

        if(!empty($prio)) {
            $query = "SELECT tijd FROM prioriteiten WHERE prioriteit = {$prio}";
            $result = mysqli_fetch_array(mysqli_query($con, $query));
            $eind = addTimes($day, $month, $year, $aanvang, $result[0]);
            $datum = $eind['day']."-".$eind['month']."-".$eind['year'];
            $eindtijd = $eind['hour'].":".$eind['minutes'];
            mysqli_query($con, "INSERT INTO incidenten (datum, aanvang, eindtijd, id_hardware, omschrijving, workaround, contact, prioriteit, status)
                                VALUES('".$datum."', '".$aanvang."', '".$eindtijd."',
                                       '".$hw."', '".$omschrijving."', '".$wa."',
                                       '".$cont."', '".$prio."', '".$status."')") or die(mysqli_error($con));
        } else {
            $datum = $day."-".$month."-".$year;
            mysqli_query($con, "INSERT INTO incidenten (datum, aanvang, eindtijd, id_hardware, omschrijving, workaround, contact, status)
                                VALUES('".$datum."', '".$aanvang."', '',
                                       '".$hw."', '".$omschrijving."', '".$wa."',
                                       '".$cont."', '".$status."')") or die(mysqli_error($con));
        }
    } else {
        $_POST['display'] = "displayAddIncident";
    }
}

function displayHardwareIncident($postData)
{
    new HelpdeskTable("Hardware", "SELECT * FROM hardware", $postData,
        null, null, "id_hardware", null, "displayHardwareAndSoftware");
}

function displaySoftwareIncident($postData)
{
    new HelpdeskTable("Software", "SELECT id_software AS ID, naam, soort,
                                          producent, leverancier, aantal_licenties AS Licenties,
                                          soort_licentie AS Licentiesoort, aantal_gebruikers AS Gebruikers,
                                          status
                                          FROM software", $postData,
                      null, null, "id_software", null, null);
}

/**
 * Function to show the problems with the status 'solved', with their related incidents.
 * @param $postData
 */
function displaySolvedProblems($postData){
    $query = "SELECT incidenten.nummer, incidenten.datum, incidenten.aanvang,
                    incidenten.eindtijd, incidenten.id_hardware, incidenten.omschrijving,
                    incidenten.workaround, incidenten.probleem, incidenten.contact,
                    incidenten.prioriteit, incidenten.status
            FROM incidenten, problemen
            WHERE incidenten.probleem = problemen.nummer
            AND problemen.status = 'opgelost'
            AND incidenten.status = 'probleem'";
    new HelpdeskTable("Incidenten gerelateerd aan opgeloste problemen", $query, $postData, "displayEditIncident", null, "nummer", null, null);
}

?>