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

function passwordField($name){
    echo "<tr>
            <td>Wachtwoord: </td>
            <td><input type='password' name='{$name}'></td>
          </tr>";
}

function dropDown($name, $array)
{
    echo "<tr>
            <td>".$name."</td>
            <td>
                <select>";
                    foreach($array as $value) {
                        echo "<option value=".$value.">".ucfirst($value)."</option>";
                    }
    echo        "</select>
            </td>
         </tr>";
}

function RadioButtons($name, $array)
{
    echo "<tr>
            <td>".$name."</td>
            <td>";
                    foreach($array as $value) {
                        echo "<input type='radio' name=".$name." value=".$value.">".ucfirst($value)."</input>";
                    }
    echo        "</select>
            </td>
         </tr>";
}

function CheckBoxes($name, $array, $width)
{
    $x = 0;
    echo "<tr>
            <td>".$name."</td>
            <td>";
                echo "<table class='inner'>";
                echo "<tr class='inner'>";
                foreach($array as $value) {
                    if($x == 0) {
                        echo "</tr><tr class ='inner'>";
                    }
                    echo "<td class='inner'><input type='checkbox' name=".$name." value=".$value.">".ucfirst($value)."</input></td>";
                    $x = ($x+1)%$width;
                }
                echo "</tr>";
                echo "</table>";
    echo        "</select>
            </td>
         </tr>";
}

function textField($name)
{
    echo "<tr><td>".$name."</td><td><input type='text' name=".$name."></td></tr>";
}

function formHeader()
{
    echo    "<form action='\index.php' method='post'>";
    echo    "<table>";
}

function formFooter($id)
{
    echo    "<tr>";
    echo        "<td></td>";
    echo        "<input type='hidden' name='id' value=".$id.">";
    echo        "<td><input class='nav' type='submit' value='Submit'></td>";
    echo    "</tr>";
    echo    "</table>";
    echo    "</form>";
}