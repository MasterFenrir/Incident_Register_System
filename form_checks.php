<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 16-6-14
 * Time: 10:51
 */

function emptyCheck($value) {
    if(!isset($value) || trim($value) == "") {
        return false;
    }
    return true;
}

function numberCheck($value) {
    if(!is_numeric($value)) {
        return false;
    }
    return true;
}

function yearCheck($value) {
    if(is_numeric($value) && $value >= 1900 && $value <= 2100) {
        return true;
    }
    return false;
}