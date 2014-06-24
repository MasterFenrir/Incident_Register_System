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
        case "displayTrends" : displayTrends($postData); break;
        case "displayAddProblem"    : displayAddProblem(); break;
        case "displayEditProblem"   : displayEditProblem(); break;
        case "displayStatisticsSettingsProblems"    : displayStatisticsSettingsProblems(); break;
        case "displayStatisticsProblems"    :   displayStatisticsProblems();    break;
        case "Trends" : Trends($postData); break;
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
    new Button("Statistieken","display", "displayStatisticsSettingsProblems");
    new Button("Trends", "display", "displayTrends");
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
    $day = $date[2];
    $month = $date[1];
    $year = $date[1];

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
            $datum = $eind['year']."-".$eind['month']."-".$eind['day'];
            $eindtijd = $eind['hour'].":".$eind['minutes'];
            if($status === "opgelost"){
                if(checkOnTime($day, $month, $year, $aanvang, $prio)){
                    $optijd = "ja";
                } else {
                    $optijd = "nee";
                }
            } else {
                $optijd = null;
            }
            mysqli_query($con, "INSERT INTO problemen (datum, aanvang, eindtijd, op_tijd_opgelost, omschrijving, prioriteit, status)
                                VALUES('".$datum."', '".$aanvang."', '".$eindtijd."', {$optijd},
                                       '".$omschrijving."', '".$prio."', '".$status."')") or die(mysqli_error($con));
        } else {
            $datum = $year."-".$month."-".$day;
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
    $day = $date[2];
    $month = $date[1];
    $year = $date[0];

    formHeader();
    dateField($day, $month, $year);
    displayField("Aanvangtijd", $values['aanvang']);
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

        $prio = removeMaliciousInput($_POST['Prioriteit']);
        $status = removeMaliciousInput($_POST['Status']);
        $aanvang = $_POST['Aanvangtijd'];

        if(!empty($prio)) {
            $query = "SELECT tijd FROM prioriteiten WHERE prioriteit = {$prio}";
            $result = mysqli_fetch_array(mysqli_query($con, $query));
            $eind = addTimes($day, $month, $year, $aanvang, $result[0]);
            $datum = $eind['year']."-".$eind['month']."-".$eind['day'];
            $eindtijd = $eind['hour'].":".$eind['minutes'];
            if($status === "opgelost"){
                if(checkOnTime($day, $month, $year, $aanvang, $prio)){
                    $optijd = "ja";
                } else {
                    $optijd = "nee";
                }
            } else {
                $optijd = null;
            }
            mysqli_query($con, "UPDATE problemen SET datum='".$datum."', aanvang='".$aanvang."', eindtijd='".$eindtijd."',
                                       op_tijd_opgelost={$optijd},
                                       omschrijving='".$omschrijving."', prioriteit='".$prio."', status='".$status."'
                                       WHERE nummer ='".$_POST['key']."'") or die(mysqli_error($con));
        } else {
            $datum = $year."-".$month."-".$day;
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
 * Function to display a screen where you can select the period to show statistics about.
 */
function displayStatisticsSettingsProblems(){
    echo("Hier kunt u van een bepaalde periode bekijken hoeveel problemen op tijd zijn opgelost en hoeveel niet op tijd zijn opgelost.<br/>
        Ook kunt u de checkbox voor alles selecteren. Dan ziet u de statistieken voor alle inicidenten ooit.<br/><br/>");
    formHeader();
    echo "De eerste datum: <br/>";
    dateField(null, null, null, "day1", "month1", "year1");

    dateField(null, null, null, "day2", "month2", "year2");
    $array[0] = "alles";
    CheckBoxes("Alles", $array, 1, null);
    hiddenValue("display", "displayStatistics");
    formFooter("Submit");
}

/**
 * Function to display statistics about the problems.
 * @param $postData
 */
function displayStatisticsProblems(){
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

            //Total amount of problems in this periode
            $query = "SELECT COUNT(*) FROM problemen
                        WHERE datum BETWEEN '{$datum1}' AND '{$datum2}'";
            $result = mysqli_query($con, $query);
            $result = mysqli_fetch_array($result);
            $totalproblems = $result[0];

            //Amount of problems solved on time
            $query = "SELECT COUNT(*) FROM problemen
                      WHERE datum BETWEEN '{$datum1}' AND '{$datum2}'
                      AND op_tijd_opgelost = 'ja'";
            $result = mysqli_query($con, $query);
            $result = mysqli_fetch_array($result);
            $onTime = $result[0];

            //Amount of problems solved.
            $query = "SELECT COUNT(*) FROM problemen
                      WHERE datum BETWEEN '{$datum1}' AND '{$datum2}'
                      AND op_tijd_opgelost IS NOT NULL";
            $result = mysqli_query($con, $query);
            $result = mysqli_fetch_array($result);
            $totalSolved = $result[0];

            //Amount of problems not solved on time
            $query = "SELECT COUNT(*) FROM problemen
                      WHERE datum BETWEEN '{$datum1}' AND '{$datum2}'
                      AND op_tijd_opgelost = 'nee'";
            $result = mysqli_query($con, $query);
            $result = mysqli_fetch_array($result);
            $notOnTime = $result[0];

            $notSolved = $totalproblems - $totalSolved;

            //Percentages
            $percSolved = ($totalSolved/$totalproblems)*100;
            $percNotSolved = 100 - $percSolved;
            $percSolvedOnTime = ($onTime/$totalSolved)*100;
            $percNotSolvedOnTime = 100 - $percSolvedOnTime;
            echo "Tussen {$datum1} en {$datum2} zijn {$totalproblems} problemen gemeld. Hiervan zijn {$totalSolved} opgelost.<br/>
                    Dat betekent dat {$notSolved} problemen nog niet zijn opgelost of niet relevant zijn. <br/>
                    Dit is {$percNotSolved}% van alle problemen in deze periode. {$percSolved}% van de problemen zijn wel opgelost.<br/>
                    Van alle problemen in deze periode zijn {$totalSolved} opgelost. {$onTime} hiervan zijn op tijd opgelost.<br/>
                    Dus {$notOnTime} zijn niet op tijd opgelost. <br/>
                    {$percSolvedOnTime}% van de problemen zijn op tijd opgelost. <br/>
                    {$percNotSolvedOnTime}% van de problemen zijn niet op tijd opgelost.";
        }
    } else {
        //Total amount of problems in this periode
        $query = "SELECT COUNT(*) FROM problemen";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        $totalproblems = $result[0];

        //Amount of problems solved on time
        $query = "SELECT COUNT(*) FROM problemen
                      WHERE op_tijd_opgelost = 'ja'";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        $onTime = $result[0];

        //Amount of problems solved.
        $query = "SELECT COUNT(*) FROM problemen
                      WHERE op_tijd_opgelost IS NOT NULL";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        $totalSolved = $result[0];

        //Amount of problems not solved on time
        $query = "SELECT COUNT(*) FROM problemen
                      WHERE op_tijd_opgelost = 'nee'";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        $notOnTime = $result[0];

        $notSolved = $totalproblems - $totalSolved;

        //Percentages
        $percSolved = ($totalSolved/$totalproblems)*100;
        $percNotSolved = 100 - $percSolved;
        $percSolvedOnTime = ($onTime/$totalSolved)*100;
        $percNotSolvedOnTime = 100 - $percSolvedOnTime;
        echo "In totaal zijn {$totalproblems} problemen gemeld. Hiervan zijn {$totalSolved} opgelost.<br/>
                    Dat betekent dat {$notSolved} problemen nog niet zijn opgelost of niet relevant zijn. <br/>
                    Dit is {$percNotSolved}% van alle problemen in deze periode. {$percSolved}% van de problemen zijn wel opgelost.<br/>
                    Van alle problemen in deze periode zijn {$totalSolved} opgelost. {$onTime} hiervan zijn op tijd opgelost.<br/>
                    Dus {$notOnTime} zijn niet op tijd opgelost. <br/>
                    {$percSolvedOnTime}% van de problemen zijn op tijd opgelost. <br/>
                    {$percNotSolvedOnTime}% van de problemen zijn niet op tijd opgelost.";
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

/**
 * Function that creates a form where you can choose different objects to see if there is a relation.
 */
function displayTrends(){

    formHeader();
    CheckBoxes("Soort", queryToArray("SELECT soort FROM hardware GROUP BY soort"),3, $_POST['Soort']);
    CheckBoxes("Locatie", queryToArray("SELECT locatie FROM hardware GROUP BY locatie"),3, $_POST['Locatie']);

    CheckBoxes("Merk", queryToArray("SELECT merk FROM hardware GROUP BY merk"),3, $_POST['Merk']);
    CheckBoxes("Leverancier",queryToArray("SELECT leverancier FROM hardware GROUP BY leverancier"),3, $_POST['Leverancier']);
    CheckBoxes("Aanschaf_jaar",queryToArray("SELECT aanschaf_jaar FROM hardware GROUP BY aanschaf_jaar"),3, $_POST['Aanschaf_jaar']);

    CheckBoxes("OS", queryToArray("SELECT naam FROM software WHERE soort LIKE '%besturingssysteem%'"),3, $_POST['OS']);
    CheckBoxes("Software", queryToArray("SELECT naam FROM software GROUP BY naam"), 3, $_POST['Software']);

    hiddenValue("display", "Trends");
    formFooter("Trends");


}

function stringbuilder($building){
    $ret="";
    foreach($building as $temple){
        $ret= $ret." ".$temple;

    }
    return ret;
}

function Trends($postData){
    global $con;
    global $message;

    $soort = ($_POST['Soort']);
    $locatie = ($_POST['Locatie']);
    $merk = ($_POST['Merk']);

    $leverancier = ($_POST['Leverancier']);
    $aanschafjaar = ($_POST['Aanschaf_jaar']);
    $os = ($_POST['OS']);
    $software = ($_POST['Software']);




    $query= "SELECT * /*nummer, datum, incidenten.id_hardware, omschrijving, probleem*/ FROM incidenten, hardware, software, hardware_software
     WHERE incidenten.id_hardware=hardware.id_hardware
     AND hardware.id_hardware=hardware_software.id_hardware
     AND software.id_software=hardware_software.id_software
     group by nummer";


    $select = array('*');
    $from = array('incidenten'=>'id_hardware', 'hardware'=>'id_hardware','hardware_software'=>'id_software', 'software'=>'id_software');
    $cols = array('hardware.soort','hardware.locatie','hardware.merk', 'hardware.leverancier','hardware.aanschafjaar','hardware.os','hardware.software');
    $grp = 'incidenten.nummer';

    $teveelwerk= monsterQueryBuilder($select, $from, $cols, 'OR', $grp, StringBuilder($_POST['Soort']));
   $nogmeerwerk1= mysqli_query($con, $teveelwerk);

    $teveelwerk= monsterQueryBuilder($select, $from, $cols, 'OR', $grp, StringBuilder($_POST['Locatie']));
    $nogmeerwerk2= mysqli_query($con, $teveelwerk);

    $teveelwerk= monsterQueryBuilder($select, $from, $cols, 'OR', $grp, StringBuilder($_POST['Merk']));
    $nogmeerwerk3= mysqli_query($con, $teveelwerk);

     $teveelwerk= monsterQueryBuilder($select, $from, $cols, 'OR', $grp, StringBuilder($_POST['Leverancier']));
   $nogmeerwerk4= mysqli_query($con, $teveelwerk);

     $teveelwerk= monsterQueryBuilder($select, $from, $cols, 'OR', $grp, StringBuilder($_POST['Aanschafjaar']));
   $nogmeerwerk5= mysqli_query($con, $teveelwerk);

    $teveelwerk= monsterQueryBuilder($select, $from, $cols, 'OR', $grp, StringBuilder($_POST['OS']));
    $nogmeerwerk6= mysqli_query($con, $teveelwerk);

    $teveelwerk= monsterQueryBuilder($select, $from, $cols, 'OR', $grp, StringBuilder($_POST['Software']));
    $nogmeerwerk7= mysqli_query($con, $teveelwerk);

    }
/*
AND (OR (foreach $soort.hardware))
     AND (OR (foreach $locatie.hardware))
     AND (OR (foreach $merk.hardware))"


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
        }



*/
?>