<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 16-6-14
 * Time: 10:51
 */

/**
 * Checks to see if a value is empty
 * @param $value The value to check
 * @return bool Return false if empty, true if not.
 */
function emptyCheck($value) {
    if(empty($value) || trim($value) == "") {
        return false;
    }
    return true;
}

/**
 * This function checks if a value is numeric
 * @param $value The value to check
 * @return bool Returns true if the value is numeric, false if not.
 */
function numberCheck($value) {
    if($value=='nvt'||$value=='onbekent'||is_numeric($value)){
        return true;
    }
    return false;
}

/**
 * This function checks for a valid year.
 * @param $value The year to check
 * @return bool Returns true if it is valid (between 1900 and 2100), and false if not.
 */
function yearCheck($value) {
    if($value >= 1900 && $value <= 2100) {
        return true;
    }
    return false;
}