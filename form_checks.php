<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 16-6-14
 * Time: 10:51
 */

function emptyCheck($value) {
    if(empty($value) || trim($value) == "") {
        return false;
    }
    return true;
}

function numberCheck($value) {
    if($value=='nvt'||$value=='onbekent'||is_numeric($value)){
        return true;
    }
    return false;
}

function yearCheck($value) {
    if($value >= 1900 && $value <= 2100) {
        return true;
    }
    return false;
}