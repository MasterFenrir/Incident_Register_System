<?php
/**
 * Date: 13-6-14
 * Time: 11:38
 */

/**
 * This functions generates three fields to fill in a date (day, month, year)
 */
function dateField($d, $m, $y){
    $day = date('d');
    $month = date('m');
    $year = date('Y');
    echo "<tr>
            <td>Dag </td>
            <td><input type='number' name='day' size='2' value='";
                if($d != null){echo $d;} else{echo $day;}
                echo "'></input>
            </td>
         </tr>
         <tr>
            <td>Maand </td>
            <td><input type='number' name='month' size='2' value='";
                if($m != null){echo $m;} else{echo $month;}
                echo"'></input>
            </td>
        </tr>
        <tr>
            <td>Jaar </td>
            <td><input type='number' name='year' size='4' value='";
                if($y != null){echo $y;} else{echo $year;}
                echo"'></input>
            </td>
        </tr>";
}

/**
 * This field creates a field to fill in a password.
 * @param $name The name the password field should have
 */
function passwordField($name){
    echo "<tr>
            <td>Wachtwoord: </td>
            <td><input type='password' name='{$name}'></td>
          </tr>";
}

/**
 * Function to create a dropdown out of an array
 *
 * @param $name  name of the dropdown
 * @param $array the array with options
 * @param $sel selected value
 */
function dropDown($name, $array, $sel){
    echo "<tr>
            <td>".$name."</td>
            <td>
                <select name='".$name."'>";
                    echo "<option></option>";
                    foreach($array as $value) {
                        echo "<option value='".$value."' "; if($value==$sel){echo "selected='selected'";} echo ">".ucfirst($value)."</option>";
                    }
    echo        "</select>
            </td>
         </tr>";
}

/**
 * Functions to create radio buttons.
 * @param $name The name of the collection of radio buttons
 * @param $array The array with options
 */
function RadioButtons($name, $array, $sel){
    echo "<tr>
            <td>".$name."</td>
            <td>";
                    foreach($array as $value) {
                        echo "<input type='radio' name='".$name."' value='".$value."' "; if($value==$sel){echo "selected='selected'";} echo ">".ucfirst($value)."</input>";
                    }
    echo        "</select>
            </td>
         </tr>";
}

/**
 * Function to create checkboxes
 * @param $name Name of the collection of checkboxes
 * @param $array Array with options
 * @param $width How many checkboxes there should be in one row
 * @param $sel Optional array of values to check
 */
function CheckBoxes($name, $array, $width, $sel){
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
                    echo "<td class='inner'><input type='checkbox' name='".$name."[]' value='".$value."'";
                    if($sel != null && in_array($value, $sel)){echo " checked";}
                    echo ">".ucfirst($value)."</input></td>";
                    $x = ($x+1)%$width;
                }
                echo "</tr>";
                echo "</table>";
    echo        "</select>
            </td>
         </tr>";
}

/**
 * Function to create a simple textfield
 *
 * @param $name The name for the textfield
 * @param $value Default value
 */
function textField($name, $sel){
    echo "<tr><td>".$name."</td><td><input type='text' name=".$name." value='".$sel."'></text></td></tr>";
}

function displayField($name, $sel){
    echo "<tr><td>".$name."</td><td>$sel</td></tr>";
}

function displayValue($name, $value){
    echo "<tr><td>".$name."</td><td>{$value}</td></tr>";
}

/**
 * Function to add a hidden value to a form
 * @param $name The name of the value
 * @param $value The actual value
 */
function hiddenValue($name, $value){
    echo    "<input type=\"hidden\" name=".$name." value=".$value.">";
}

/**
 * Function to create a quick start for a form
 */
function formHeader() {
    echo    "<form action='\index.php' method='post'>";
    echo    "<table>";
}

/**
 * Function to create
 * @param $id
 */
function formFooter($id, $name="Submit"){
    echo    "<tr>";
    echo        "<td></td>";
    echo        "<input type='hidden' name='id' value='".$id."'>";
    echo        "<td><input class='nav' type='submit' value='{$name}'></td>";
    echo    "</tr>";
    echo    "</table>";
    echo    "</form>";
}