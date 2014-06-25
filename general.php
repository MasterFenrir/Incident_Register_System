<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 11-6-14
 * Time: 16:04
 */

    $con = mysqli_connect("localhost","helpdesk","Sku53_u3","helpdesk") or die('Unable to connect');

/**
 * This function displays a logout button
 */
function logoutKnop(){
    echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method=\"post\">";
echo "<input type=\"hidden\" name=\"logout\" value=\"1\">";
echo "<input class=\"nav\" type=\"submit\" value=\"Logout\">";
echo "</form>";
}

/**
 * This function displays a searchfield
 */
function searchField(){
    echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method=\"post\">";
    echo "<input type='search' name='search'>";
    echo "<input type='hidden' name='display' value='displaySearch'>";
    echo "<input class='nav' type='submit' value='Search'>";
    echo "</form>";
}

/*
 *  This function checks if someone pressed the logout button. If yes, the user will be logged out.
 */
function checkLogin(){
    session_start();
    if(isset($_POST['logout']))
    {
        logOut();
    }
}

/*
 * This function ends the session and destroys the session variables.
 */
function logOut(){
    session_unset();
    session_destroy();
}

/*
 * This function removes possible malicious input
 */
function removeMaliciousInput($input){
    global $con;
    $input = strip_tags($input);
    $input = mysqli_real_escape_string($con, $input);
    return $input;
}

/*
 * Function to validate the entered date
 */
function validateDate($day, $month, $year){
    if(($day < 1) || $day > 31 || $month < 1 || $month > 12 || $year < 2000 || $year > 2100){
        return false;
    }
    //Check february
    if($month == 2){
        if((($year % 4 == 0) && ($year % 100 > 0) || ($year % 400 == 0)) && $day < 30){
            return true;
        } else if( $day < 29 ){
            return true;
        } else {
            return false;
        }
    }
    // Check the months with 30 days
    if($month == 4 || $month == 6 || $month == 9 || $month == 11){
        if($day > 30){
            return false;
        }
    }
    // Since we've gotten here, it must be true.
    return true;
}

/**
 * Function to turn query results into an array
 * @param $query The query you want the array from
 * @return array The array with the results
 */
function queryToArray($query)
{
    global $con;
    $sql = mysqli_query($con, $query);
    while($row = mysqli_fetch_array($sql)) {$array[] = $row[0];}

        return $array;
}

/**
 * This function encrypts a password and returns the result
 * @param $password
 * @return string
 */
function password_encrypt($password) {
    $hash_format = "$2y$10$";   // Tells PHP to use Blowfish with a "cost" of 10
    $salt_length = 22; 					// Blowfish salts should be 22-characters or more
    $salt = generate_salt($salt_length);
    $format_and_salt = $hash_format . $salt;
    $hash = crypt($password, $format_and_salt);
    return $hash;
}

/**
 * This function generates a random salt, in order to use this with passwords, making them safer
 * @param $length
 * @return string
 */
function generate_salt($length) {
    // Not 100% unique, not 100% random, but good enough for a salt
    // MD5 returns 32 characters
    $unique_random_string = md5(uniqid(mt_rand(), true));

    // Valid characters for a salt are [a-zA-Z0-9./]
    $base64_string = base64_encode($unique_random_string);

    // But not '+' which is valid in base64 encoding
    $modified_base64_string = str_replace('+', '.', $base64_string);

    // Truncate string to the correct length
    $salt = substr($modified_base64_string, 0, $length);

    return $salt;
}

/**
 * This function compares the entered password with the password from the database.
 * @param $password
 * @param $existing_hash
 * @return bool
 */
function password_check($password, $existing_hash) {
    // existing hash contains format and salt at start
    $hash = crypt($password, $existing_hash);
    echo $existing_hash."<br/>".$hash;
    if ($hash === $existing_hash) {
        return true;
    } else {
        return false;
    }
}

/*
 * Function that builds a search query based on it's input
 *
 * @param $select: Array of colom names to select, best given as 'table.column'
 * @param $from: Associative array of tables to search in given as 'table=>primary_key'
 * @param $cols: Array of columns to search in, best given as 'table.column'
 * @param $type: Either AND or OR, determines whether all or atleast one word of the searchstring must be found
 * @param $group: Optional, determines what column to group by, null to not group
 * @param $searchString:
 */
function monsterQueryBuilder($select, $from, $cols, $type, $group, $searchString)
{
    //Seperates $searchString on spaces in order to search for each word
    $search = explode(" ", $searchString);

    /*
     * All elements from the $select array are added to the SELECT part of the statement
     */
    $query = "SELECT";
    for($x=0; $x<count($select); $x++) {
        if($x != (count($select)-1)) {
            $query = $query. " ".$select[$x].", ";
        } else {
            $query = $query. " ".$select[$x];
        }
    }

    /*
     * All elements from the $from associative array are added to the FROM/LEFT OUTER JOIN part of statement
     */
    $tabs = array_keys($from);
    for($x=0; $x<count($from); $x++) {
        $table = $tabs[$x];
        $lastTable = $tabs[$x-1];
        $lastCol = $from[$lastTable];

        if($x==0) {
            $query = $query." FROM ".$table;
        } else {
            $query = $query." LEFT OUTER JOIN ".$table." ON ".$table.".".$lastCol."=".$lastTable.".".$lastCol;
        }
    }

    /*
     * Adds a WHERE/AND/OR column LIKE search OR column LIKE search etc. statement for each
     * word in $searchString for each column in $cols.
     *
     * Outer loop iterates over the words in $searchString adding a new WHERE/AND/OR
     */
    for($x=0; $x<count($search); $x++) {
        if($x==0) {
            $query = $query." WHERE(";
        } else {
            $query = $query." ".$type."(";
        }

        //Inner loop iterates over each column in $cols with the current $search word
        for($y=0; $y<count($cols); $y++) {
            if($y != (count($cols)-1)) {
                $query = $query.$cols[$y]." LIKE '%".$search[$x]."%' OR ";
            } else {
                $query = $query.$cols[$y]." LIKE '%".$search[$x]."%'";
            }
        }
        $query = $query.")";
    }

    //Groups results by $group if $group isn't null
    if($group != null) {
        $query = $query." GROUP BY ".$group;
    }
    return $query;
}

/*
 * Function that builds a search query based on it's input
 *
 * @param $select: Array of colom names to select, best given as 'table.column'
 * @param $from: Associative array of tables to search in given as 'table=>primary_key'
 * @param $cols: Array of columns to search in, best given as 'table.column'
 * @param $type: Either AND or OR, determines whether all or atleast one word of the searchstring must be found
 * @param $group: Optional, determines what column to group by, null to not group
 * @param $searchString:
 */
function superMonsterQueryBuilder($select, $from, $cols, $type, $group, $searchStringArray)
{
    //Seperates $searchString on spaces in order to search for each word

    /*
     * All elements from the $select array are added to the SELECT part of the statement
     */
    $query = "SELECT";
    for($x=0; $x<count($select); $x++) {
        if($x != (count($select)-1)) {
            $query = $query. " ".$select[$x].", ";
        } else {
            $query = $query. " ".$select[$x];
        }
    }

    /*
     * All elements from the $from associative array are added to the FROM/LEFT OUTER JOIN part of statement
     */
    $tabs = array_keys($from);
    for($x=0; $x<count($from); $x++) {
        $table = $tabs[$x];
        $lastTable = $tabs[$x-1];
        $lastCol = $from[$lastTable];

        if($x==0) {
            $query = $query." FROM ".$table;
        } else {
            $query = $query." LEFT OUTER JOIN ".$table." ON ".$table.".".$lastCol."=".$lastTable.".".$lastCol;
        }
    }

    /*
     * Adds a WHERE/AND/OR column LIKE search OR column LIKE search etc. statement for each
     * word in $searchString for each column in $cols.
     *
     * Outer loop iterates over the words in $searchString adding a new WHERE/AND/OR
     */
    $query = $query." WHERE(".true.")";

    for($z=0; $z<count($searchStringArray); $z++) {
        $search = explode(" ", $searchStringArray[$z]);

            $query = $query." AND(";

        for($x=0; $x<count($search); $x++) {
            //Inner loop iterates over each column in $cols with the current $search word
            for($y=0; $y<count($cols); $y++) {
                if($y == (count($cols)-1) && $x == (count($search)-1)) {
                    $query = $query.$cols[$y]." LIKE '%".$search[$x]."%'";
                } else {
                    $query = $query.$cols[$y]." LIKE '%".$search[$x]."%' OR ";
                }
            }
        }
        $query = $query.")";
    }

    //Groups results by $group if $group isn't null
    if($group != null) {
        $query = $query." GROUP BY ".$group;
    }

    return $query;
}

/**
 * A function to add two timestrings together.
 * @param $time1 The time
 * @param $time2 The time to add to the previous one
 */
function addTimes($day, $month, $year, $time1, $time2){
    $time1 = explode(":", $time1);
    $time2 = explode(":", $time2);
    $time[0] = $time1[0] + $time2[0];
    $time[1] = $time1[1] + $time2[1];

    $time[0] %= 24;
    $time[1] %= 60;
    $time[0] = str_pad($time[0], 2, '0', STR_PAD_LEFT);
    $time[1] = str_pad($time[1], 2, '0', STR_PAD_LEFT);

    if(intval($time[0]) < intval($time1[0])){
        $date = incrementDate($day, $month, $year);
        $finalTime['day'] = $date['day'];
        $finalTime['month'] = $date['month'];
        $finalTime['year'] = $date['year'];
    } else {
        $finalTime['day'] = $day;
        $finalTime['month'] = $month;
        $finalTime['year'] = $year;
    }
    $finalTime['hour'] = $time[0];
    $finalTime['minutes'] = $time[1];

    return $finalTime;
}

/**
 * This function increments the date by one day
 * @param $day The day
 * @param $month The
 * @param $year The year
 * @return mixed Returns the results in an array
 */
function incrementDate($day, $month, $year){
    $day++;
    if($day > 31 && ($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12)){
        $day = 1;
        $month++;
    } else if($day > 30 && ($month == 4 || $month == 6 || $month == 8 || $month == 9 || $month == 11)){
        $day = 1;
        $month++;
    } else if($month == 2){
        if((($year % 4 == 0) && ($year % 100 > 0) || ($year % 400 == 0)) && $day > 29){
            $day = 1;
            $month++;
        } else if($day > 28){
            $day = 1;
            $month++;
        }
    }

    if($month > 12){
        $month = 1;
        $year++;
    }

    $date['day'] = $day;
    $date['month'] = $month;
    $date['year'] = $year;

    return $date;
}

/**
 * Function to display errors.
 */
function displayErrors()
{
    global $message;

    if(!empty($message)) {
        echo "<p class=error>".$message."</p>";
        $message = '';
    }
}

/**
* Function to determine whether the end time has been reached in time
* @param $day The original day
* @param $month The original month
* @param $year The original year
* @param $start The original time
* @param $prio The priority
*/
function checkOnTime($day, $month, $year, $time, $prio){
    date_default_timezone_set("Europe/Amsterdam");
    global $con;
    $query = "SELECT tijd FROM prioriteiten WHERE prioriteit = {$prio}";
    $result = mysqli_query($con, $query);
    $result = mysqli_fetch_array($result);

    $date1 = $year."-".$month."-".$day." ".$time.":00";
    $date2 = date('Y-m-d H:i:s');
    $hourdiff = round((strtotime($date2) - strtotime($date1))/3600, 1);
    $prio = explode(":", $result[0]);
    $prio = $prio[0];

    if($prio < $hourdiff) {
        return false;
    } else {
        return true;
    }
}

?>