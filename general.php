<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 11-6-14
 * Time: 16:04
 */

    $con = mysqli_connect("localhost","helpdesk","Sku53_u3","helpdesk") or die('Unable to connect');

    function logoutKnop(){
        global $perm;
        echo $perm;

        echo "<form action='".htmlspecialchars($_SERVER['PHP_SELF'])."' method=\"post\">";
        echo "<input type=\"hidden\" name=\"logout\" value=\"1\">";
        echo "<input class=\"nav\" type=\"submit\" value=\"Logout\">";
        echo "</form>";
    }

    /*
     *  Deze functie controleerd of iemand op uitloggen heeft geklikt, zo ja worden sessie variabelen verwijderd en de sessie beeindigd
     */
    function checkLogin(){
        session_start();
        if(isset($_POST['logout']))
        {
            logOut();
        }
    }

    /*
     *Deze functie eindigd de sessie en vernietigd de sessie variabelen
     */
    function logOut(){
        session_unset();
        session_destroy();
    }

    /*
     * This function removes possible malicious input
     */
    function removeMaliciousInput($input){
        $input = strip_tags($input);
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

    function queryToArray($query)
    {
        global $con;
        $sql = mysqli_query($con, $query);
        while($row = mysqli_fetch_array($sql)) {$array[] = $row[0];}

        return $array;
    }

    function password_encrypt($password) {
        $hash_format = "$2y$10$";   // Tells PHP to use Blowfish with a "cost" of 10
        $salt_length = 22; 					// Blowfish salts should be 22-characters or more
        $salt = generate_salt($salt_length);
        $format_and_salt = $hash_format . $salt;
        $hash = crypt($password, $format_and_salt);
        return $hash;
    }

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

    function password_check($password, $existing_hash) {
        // existing hash contains format and salt at start
        $hash = crypt($password, $existing_hash);
        if ($hash === $existing_hash) {
            return true;
        } else {
            return false;
        }
    }

?>