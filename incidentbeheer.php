<?php
/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 12-6-14
 * Time: 11:51
 */

function displayContentIncident($postData)
{
    switch($postData) {
        case "displayIncidenten" : new HelpdeskTable("Incidenten", "SELECT * FROM incidenten"); break;
        default : echo "Hello ".ucfirst($_SESSION['user']);
    }
}

function displayMenuIncident()
{
    new Button("Incidenten", "displayIncidenten");
}