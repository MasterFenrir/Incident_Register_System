<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 11-6-14
 * Time: 16:04
 */

$con = mysqli_connect("localhost","helpdesk","Sku53_u3","helpdesk") or die('Unable to connect');

function queryToJSON($query)
{
    global $con;
    $resultset = mysqli_query($con, $query) or die('Error connecting to database');

    $return = array();

    while($row = mysqli_fetch_assoc($resultset)) {
        $return[] = $row;
    }

    return json_encode($return);
}