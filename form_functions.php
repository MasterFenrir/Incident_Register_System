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

function dropDown($valueArray)
{
    echo "<tr>
            <td></td>
            <td>
                <select>";
                    foreach($valueArray as $value) {
                        echo "<option value='$value'>".ucfirst($value)."</option>";
                    }
    echo        "</select>
            </td>
         </tr>";
}

function RadioButtons($valueArray, $name)
{
    echo "<tr>
            <td></td>
            <td>";
                    foreach($valueArray as $value) {
                        echo "<input type='radio' name='$name' value='$value'>$value</input>";
                    }
    echo        "</select>
            </td>
         </tr>";
}

function CheckBoxes($valueArray, $name)
{
    echo "<tr>
            <td></td>
            <td>";
                foreach($valueArray as $value) {
                    echo "<input type='checkbox' name='$name' value='$value'>$value</input>";
                }
    echo        "</select>
            </td>
         </tr>";
}

function textField($name)
{
    echo "<tr><td>".$name."</td><td><input type='text' value='$name'></td></tr>";
}