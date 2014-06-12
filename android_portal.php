<?php
$databasehost = "localhost";
$databasename = "helpdesk";
$databaseusername ="helpdesk";
$databasepassword = "Sku53_u3";

$con = mysqli_connect($databasehost,$databaseusername,$databasepassword,$databasename) or die('Unable to connect');

$user = $_POST['user'];
$pw = $_POST['pw'];

$sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pw'";
$login = mysqli_query($con, $sql) or die('Unable to connect');

if(($get = mysqli_fetch_assoc($login)) > 0) {

    switch($_POST['id']) {
        case "hardware_list" : echo queryToJSON("SELECT * FROM hardware"); break;
        case "software_list" : echo queryToJSON("SELECT * FROM software"); break;
        case "login" : echo 1; break;
    }
}
else {
    echo 0;
    die();
}




/*
 * Performs given query and returns the response in JSON format.
 */
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
?>