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
        case "displayProblemen" : new HelpdeskTable("Problemen", "SELECT * FROM problemen", "displayProblemen"); break;
        default : echo "Hello ".ucfirst($_SESSION['user']);
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