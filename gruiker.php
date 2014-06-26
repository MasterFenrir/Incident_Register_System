<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 22-6-14
 * Time: 17:05
 */

function displayContentGebruiker($postData)
{
    switch($postData) {
        default: displayScript(); break;
    }
}

function processEventGebruiker($postData)
{
    switch($postData) {
        default: sendData(); break;
    }
}

function displayScript()
{
    $select = array('*');
    $from = array('hardware'=>'id_hardware', 'incidenten'=>'nummer');
    $cols = array('incidenten.nummer', 'incidenten.datum', 'incidenten.aanvang', 'incidenten.eindtijd', 'incidenten.id_hardware',
        'incidenten.omschrijving', 'incidenten.workaround', 'incidenten.probleem', 'incidenten.prioriteit', 'incidenten.status',
        'hardware.soort', 'incidenten.contact');
    $grp = 'incidenten.nummer';
    $search = array(arrayToString(array('test', 'awsome','woot')), arrayToString(array('next', 'second', 'woot')), arrayToString(array('third', 'last', 'awsome')));

    superMonsterQueryBuilder($select, $from, $cols, 'AND', $grp, $search);
    //scriptDropDown("Locatie", queryToArray("SELECT locatie FROM hardware GROUP BY locatie ORDER BY locatie"), 'locatie');

    echo "<div id='loc'></div>";
    echo "<div id='srt'></div>";
    echo "<div id='it'></div>";
    echo "<div id='test'></div>";

    $array = queryToArray("SELECT locatie FROM hardware GROUP BY locatie ORDER BY locatie");
    echo "<script> jscriptDropDown('Locatie',".json_encode($array).",'locatie', 'loc'); </script>";

    /*
    $array = array('woot', 'awsome');
    echo "<div id='test'></div>";
    echo "<script> jscriptDropDown('Soort',".$array.",'test'); </script>";
    */
}

function arrayToString($array) {
    $ret = '';
    for($x=0;$x<count($array);$x++) {
        if($x==0) {
            $ret = $array[$x];
        } else {
            $ret = $ret." ".$array[$x];
        }
    }
    return $ret;
}

function sendData()
{

}