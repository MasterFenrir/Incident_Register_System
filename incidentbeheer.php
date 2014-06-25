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
        case "displayStatisticsSettings"    : displayStatisticsSettings(); break;
        case "displayStatistics"    :   displayStatistics();    break;

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
    new Button("Statistieken", "display", "displayStatisticsSettings");
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

/**
 * This displays the table with the incidents from the search.
 * @param $postData
 */
function displaySearchIncidenten($postData)
{
    new HelpdeskTable("Incidenten", makeSearchIncidenten($_POST['search']), null,
                      "displayEditIncident", "deleteIncident", "nummer", $_POST['search'], null);

    echo "<br/>";

    displaySearchConfig($postData);
}

/**
 * This function searches for incidents in the incidenttable.
 * @param $search
 * @return string
 */
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
 * This function manages the display of all the incidents known.
 */
function displayIncidenten($postData){
    echo("Hier ziet u de incidenten tabel, U kunt gegevens wijzigen door op edit te klikken.");
    echo(" Gegevens kunnen verwijdert worden door op delete te klikken.");
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten WHERE status != 'melding'", $postData, "displayEditIncident", "deleteIncident", "nummer", null, null);
}

/*
 * This functions displays the form to add an incident.
 */
function displayAddIncident() {
    echo("Hier kunt u incidenten toevoegen de gegevens kunnen worden bevestigd door op submit te klikken");
    displayErrors();
    date_default_timezone_set("Europe/Amsterdam");

    formHeader();
    dateField($_POST['year'],$_POST['month'],$_POST['day']);
    textField("Aanvangtijd",date('H:i'));
    dropDownNoEmptyValue("Hardware", queryToArray("SELECT id_hardware FROM hardware"), $_POST['Hardware']);
    textField("Omschrijving", $_POST['Omschrijving']);
    textField("Workaround", $_POST['Workaround']);
    textField("Contact", $_POST['Contact']);
    dropDown("Prioriteit", queryToArray("SELECT prioriteit FROM prioriteiten"), $_POST['Prioriteit']);
    dropDownNoEmptyValue("Status", queryToArray("SELECT status From statussen"), "onopgelost");
    hiddenValue("display", "displayIncidenten");
    formFooter("addIncident");
}

/**
 * This function displays the form to edit an incident.
 */
function displayEditIncident() {
    echo("Hier kunt u de gegevens van het incident wijzigen, de gegevens kunnen bevestigd worden door op submit te klikken");
    global $con;
    displayErrors();

    $values = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM incidenten WHERE nummer='".$_POST['key']."'"));

    $date = explode("-", $values['datum']);
    $day = $date[2];
    $month = $date[1];
    $year = $date[0];

    formHeader();
    dateField($day, $month, $year);
    displayField("Aanvangtijd", $values['aanvang']);
    dropDownNoEmptyValue("Hardware", queryToArray("SELECT id_hardware FROM hardware"), $values['id_hardware']);
    textField("Omschrijving", $values['omschrijving']);
    textField("Workaround", $values['workaround']);
    textField("Contact", $values['contact']);
    dropdown("Prioriteit", queryToArray("SELECT prioriteit FROM prioriteiten"), $values['prioriteit']);
    dropDownNoEmptyValue("Status", queryToArray("SELECT status From statussen"),$values['status']);
    hiddenValue("display", "displayIncidenten");
    hiddenValue("key", $values['nummer']);
    formFooter("editIncident");
}

/**
 * This function displays all the incidents that have no priority yet.
 */
function displayMeldingen($postData)
{
    echo("Hier ziet u de meldingen die zijn doorgegeven");
    new HelpdeskTable("Incidenten", "SELECT * FROM incidenten WHERE prioriteit IS NULL", $postData, "displayEditIncident", "deleteIncident", "nummer", null, null);
}

/**
 * This function removes an incident.
 */
function deleteIncident()
{
    global $con;
    mysqli_query($con, "DELETE FROM incidenten WHERE nummer ='".$_POST['key']."'");
}

/**
 * This function uses the form from displayEditIncident to change an incident.
 */
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
            $datum = $eind['year']."-".$eind['month']."-".$eind['day'];
            $eindtijd = $eind['hour'].":".$eind['minutes'];
            $query = "SELECT op_tijd_opgelost FROM incidenten WHERE nummer = '{$_POST['key']}'";
            $optijd = mysqli_query($con, $query);
            $optijd = mysqli_fetch_array($optijd);
            if($status === "opgelost" && !emptyCheck($optijd[0])){
                if(checkOnTime($day, $month, $year, $aanvang, $prio)){
                    $optijd = "ja";
                } else {
                    $optijd = "nee";
                }
            } else {
                $optijd = $optijd[0];
            }
            mysqli_query($con, "UPDATE incidenten SET datum='".$datum."', aanvang='".$aanvang."', eindtijd='".$eindtijd."',
                                op_tijd_opgelost = '{$optijd}', id_hardware='".$hw."', omschrijving='".$omschrijving."', workaround='".$wa."',
                                contact='".$cont."', status='".$status."', prioriteit='".$prio."'
                                WHERE nummer ='".$_POST['key']."' ") or die(mysqli_error($con));
        } else {
            $datum = $year."-".$month."-".$day;
            mysqli_query($con, "UPDATE incidenten SET datum='".$datum."', aanvang='".$aanvang."',
                                id_hardware='".$hw."', omschrijving='".$omschrijving."', workaround='".$wa."',
                                contact='".$cont."', prioriteit=NULL, status='".$status."'
                                WHERE nummer ='".$_POST['key']."'") or die(mysqli_error($con));
        }
    } else {
        $_POST['display'] = "displayEditIncident";
    }
}

/**
 * This function adds the incident from the form displayAddIncident into the incidenttable.
 */
function addIncident()
{
    echo("Hier kunt u een incident toevoegen, de gegevens worden bevestigd door op submit te klikken");
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
            $datum = $eind['year']."-".$eind['month']."-".$eind['day'];
            $eindtijd = $eind['hour'].":".$eind['minutes'];
            $query = "SELECT op_tijd_opgelost FROM incidenten WHERE nummer = '{$_POST['key']}'";
            $optijd = mysqli_query($con, $query);
            $optijd = mysqli_fetch_array($optijd);
            if($status === "opgelost" && !emptyCheck($optijd[0])){
                if(checkOnTime($day, $month, $year, $aanvang, $prio)){
                    $optijd = "ja";
                } else {
                    $optijd = "nee";
                }
            } else {
                $optijd = $optijd[0];
            }
            mysqli_query($con, "INSERT INTO incidenten (datum, aanvang, eindtijd,op_tijd_opgelost, id_hardware, omschrijving, workaround, contact, prioriteit, status)
                                VALUES('".$datum."', '".$aanvang."', '".$eindtijd."','{$optijd}',
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

/**
 * This function displays all hardware incidents.
 * @param $postData
 */
function displayHardwareIncident($postData)
{
    echo("Hier ziet u alle hardware die aanwezig is");
    new HelpdeskTable("Hardware", "SELECT * FROM hardware", $postData,
        null, null, "id_hardware", null, "displayHardwareAndSoftware");
}

/**
 * This function displays all software incidents.
 * @param $postData
 */
function displaySoftwareIncident($postData)
{
    echo("Hier ziet u alle software die aanwezig is");
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
    echo("Hier ziet u alle opgeloste problemen");
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

/**
 * Function to display a screen where you can select the period to show statistics about.
 */
function displayStatisticsSettings(){
    echo("Hier kunt u van een bepaalde periode bekijken hoeveel incidenten op tijd zijn opgelost en hoeveel niet op tijd zijn opgelost.<br/>
        Ook kunt u de checkbox voor alles selecteren. Dan ziet u de statistieken voor alle incidenten ooit.<br/><br/>");
    formHeader();
    displayField(null, "De datum waarna de incidenten komen, inclusief zichzelf.");
    dateField(null, null, null, "day1", "month1", "year1");
    displayField(null, "De datum waarvoor de incidenten komen, inclusief zichzelf.");
    dateField(null, null, null, "day2", "month2", "year2");
    $array[0] = "alles";
    CheckBoxes("Alles", $array, 1, null);
    hiddenValue("display", "displayStatistics");
    formFooter("Submit");
}

/**
 * Function to display statistics about the incidents.
 */
function displayStatistics(){
    displayErrors();
    global $message;
    global $con;
    if(!isset($_POST['Alles'])){
        $day1 = $_POST['day1'];
        $month1 = $_POST['month1'];
        $year1 = $_POST['year1'];

        $day2 = $_POST['day2'];
        $month2 = $_POST['month2'];
        $year2 = $_POST['year2'];

        if(!validateDate($day1, $month1, $year1)){
            $message .= "De eerste datum is geen correct datum.<br/>";
        }

        if(!validateDate($day2, $month2, $year2)){
            $message .= "De tweede datum is geen correct datum.<br/>";
        }

        if(!emptyCheck($message)){
            $datum1 = $year1."-".$month1."-".$day1;
            $datum2 = $year2."-".$month2."-".$day2;

            //Total amount of incidents in this periode
            $query = "SELECT COUNT(*) FROM incidenten
                        WHERE datum BETWEEN '{$datum1}' AND '{$datum2}'";
            $result = mysqli_query($con, $query);
            $result = mysqli_fetch_array($result);
            $totalIncidents = $result[0];
            if($totalIncidents != 0){
                //Amount of incidents solved on time
                $query = "SELECT COUNT(*) FROM incidenten
                      WHERE datum BETWEEN '{$datum1}' AND '{$datum2}'
                      AND op_tijd_opgelost = 'ja'";
                $result = mysqli_query($con, $query);
                $result = mysqli_fetch_array($result);
                $onTime = $result[0];

                //Amount of incidents solved.
                $query = "SELECT COUNT(*) FROM incidenten
                      WHERE datum BETWEEN '{$datum1}' AND '{$datum2}'
                      AND op_tijd_opgelost IS NOT NULL";
                $result = mysqli_query($con, $query);
                $result = mysqli_fetch_array($result);
                $totalSolved = $result[0];

                //Amount of incidents not solved on time
                $query = "SELECT COUNT(*) FROM incidenten
                      WHERE datum BETWEEN '{$datum1}' AND '{$datum2}'
                      AND op_tijd_opgelost = 'nee'";
                $result = mysqli_query($con, $query);
                $result = mysqli_fetch_array($result);
                $notOnTime = $result[0];

                $notSolved = $totalIncidents - $totalSolved;

                //Percentages
                $percSolved = ($totalSolved/$totalIncidents)*100;
                $percNotSolved = 100 - $percSolved;
                if($totalSolved == 0){
                    $percSolvedOnTime = 0;
                } else {
                    $percSolvedOnTime = ($onTime/$totalSolved)*100;
                }
                $percNotSolvedOnTime = 100 - $percSolvedOnTime;
                echo "Tussen {$datum1} en {$datum2} zijn {$totalIncidents} incidenten gemeld. Hiervan zijn {$totalSolved} opgelost.<br/>
                    Dat betekent dat {$notSolved} incidenten nog niet zijn opgelost of niet relevant zijn. <br/>
                    Dit is {$percNotSolved}% van alle incidenten in deze periode. {$percSolved}% van de incidenten zijn wel opgelost.<br/>
                    Van alle incidenten in deze periode zijn {$totalSolved} opgelost. {$onTime} hiervan zijn op tijd opgelost.<br/>
                    Dus {$notOnTime} zijn niet op tijd opgelost. <br/>
                    {$percSolvedOnTime}% van de incidenten zijn op tijd opgelost. <br/>
                    {$percNotSolvedOnTime}% van de incidenten zijn niet op tijd opgelost.";
            } else {
                echo "Er zijn geen incidenten in deze periode. Weet je zeker dat je de data goed hebt ingevoerd?";
            }

        }
    } else {
        //Total amount of incidents in this periode
        $query = "SELECT COUNT(*) FROM incidenten";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        $totalIncidents = $result[0];
        if($totalIncidents != 0){
            //Amount of incidents solved on time
            $query = "SELECT COUNT(*) FROM incidenten
                      WHERE op_tijd_opgelost = 'ja'";
            $result = mysqli_query($con, $query);
            $result = mysqli_fetch_array($result);
            $onTime = $result[0];

            //Amount of incidents solved.
            $query = "SELECT COUNT(*) FROM incidenten
                      WHERE op_tijd_opgelost IS NOT NULL";
            $result = mysqli_query($con, $query);
            $result = mysqli_fetch_array($result);
            $totalSolved = $result[0];

            //Amount of incidents not solved on time
            $query = "SELECT COUNT(*) FROM incidenten
                      WHERE op_tijd_opgelost = 'nee'";
            $result = mysqli_query($con, $query);
            $result = mysqli_fetch_array($result);
            $notOnTime = $result[0];

            $notSolved = $totalIncidents - $totalSolved;

            //Percentages
            $percSolved = ($totalSolved/$totalIncidents)*100;
            $percNotSolved = 100 - $percSolved;
            if($totalSolved == 0){
                $percSolvedOnTime = 0;
            } else {
                $percSolvedOnTime = ($onTime/$totalSolved)*100;
            }
            $percNotSolvedOnTime = 100 - $percSolvedOnTime;
            echo "In totaal zijn {$totalIncidents} incidenten gemeld. Hiervan zijn {$totalSolved} opgelost.<br/>
                    Dat betekent dat {$notSolved} incidenten nog niet zijn opgelost of niet relevant zijn. <br/>
                    Dit is {$percNotSolved}% van alle incidenten in deze periode. {$percSolved}% van de incidenten zijn wel opgelost.<br/>
                    Van alle incidenten in deze periode zijn {$totalSolved} opgelost. {$onTime} hiervan zijn op tijd opgelost.<br/>
                    Dus {$notOnTime} zijn niet op tijd opgelost. <br/>
                    {$percSolvedOnTime}% van de incidenten zijn op tijd opgelost. <br/>
                    {$percNotSolvedOnTime}% van de incidenten zijn niet op tijd opgelost.";
        } else {
            echo "Er zijn geen incidenten gevonden.";
        }
    }
}

?>