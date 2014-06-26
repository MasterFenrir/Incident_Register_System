<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 23-6-14
 * Time: 9:11
 */
session_start();

$con = mysqli_connect("localhost","helpdesk","Sku53_u3","helpdesk") or die('Unable to connect');

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';

echo '<response>';
    $values = json_decode($_POST['values']);

    switch($_POST['id']) {
        case 'locatie' :
            $query = mysqli_query($con, "SELECT soort FROM hardware WHERE locatie='".$values->locatie."' GROUP BY soort") or die(mysqli_error($con));
            processArray($query);
            break;
        case 'soort' :
            $query = mysqli_query($con, "SELECT id_hardware FROM hardware WHERE soort='".$values->soort."' AND locatie='".$values->locatie."'") or die(mysqli_error($con));
            processArray($query);
            break;
    }

echo '</response>';

function processArray($query) {
    $array = array();
    while($row = mysqli_fetch_array($query)) {
        $array[] = $row[0];
    }
    echo json_encode($array);
}