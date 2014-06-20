<?php
/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 12-6-14
 * Time: 11:49
 */

function displayContentProbleem($postData)
{
    switch($postData) {
        case "displayProblemen" : new HelpdeskTable("Problemen", "SELECT * FROM problemen", "displayProblemen", null, null, null, null, null); break;
        default : displayLandingProbleem();
    }
}

function displayMenuProbleem()
{
    new Button("Problemen", "display", "displayProblemen");
}

function processEventProbleem($eventID)
{
    switch($eventID){

    }
}

function displayLandingProbleem()
{
    $sel = array('hardware.id_hardware', 'hardware.soort', 'hardware.locatie', 'hardware.os',
                 'hardware.merk', 'hardware.leverancier', 'hardware.aanschaf_jaar');
    $from = array('hardware'=>'id_hardware', 'hardware_software'=>'id_software', 'software'=>'id_software');
    $cols = array('hardware.id_hardware', 'hardware.soort', 'hardware.locatie', 'hardware.os', 'hardware.merk',
                  'hardware.leverancier', 'hardware.aanschaf_jaar', 'hardware.status', 'software.naam');
    $type = 'OR';
    $grp = 'id_hardware';
    $search = "werks xp grol 20";

    monsterQueryBuilder($sel, $from, $cols, $type, $grp, $search);
}