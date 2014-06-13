<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 13-6-14
 * Time: 11:38
 */

function dateField(){
    $day = date('d');
    $month = date('m');
    $year = date('Y');
    echo "<tr>
            <td>Dag: </td>
            <td><input type='number' name='day' size='2' value='".$day."'></td>
         </tr>
         <tr>
            <td>Maand: </td>
                <td><input type='number' name='month' size='2' value='".$month."'></td>
        </tr>
        <tr>
            <td>Jaar: </td>
            <td><input type='number' name='month' size='4' value='".$year."'></td>
        </tr>";
}

function passwordField(){
    echo "<tr>
            <td>Wachtwoord: </td>
            <td><input type='password' name='day'></td>
          </tr>";
}