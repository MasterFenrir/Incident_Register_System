
<?php
/**
 * Date: 13-6-14
 * Time: 11:38
 */

/**
 * Function to create a dropdown out of an array
 *
 * @param $name  name of the dropdown
 * @param $array the array with options
 * @param $sel selected value
 */
function scriptDropDown($name, $array, $id){
    echo "<tr>
            <td>".$name."</td>
            <td>
                <select name='".$name."' id='".$id."' onchange='process();'>";
    echo "<option></option>";
    foreach($array as $value) {
        echo "<option value='".$value."' >".ucfirst($value)."</option>";
    }
    echo    "</select>
            </td>
         </tr>";
}

/**
 * Functions to create radio buttons.
 * @param $name The name of the collection of radio buttons
 * @param $array The array with options
 */
function scriptRadioButtons($name, $array, $sel){
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
function scriptCheckBoxes($name, $array, $width, $sel){
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
function scriptTextField($name, $sel){
    echo "<tr><td>".$name."</td><td><input type='text' name=".$name." value='".$sel."'></text></td></tr>";
}

/**
 * Function to create a quick start for a form
 */
function scriptFormHeader($name) {
    echo    "<form action='\index.php' method='post' name='".$name."'>";
    echo    "<table>";
}

/**
 * Function to create
 * @param $id
 */
function scriptFormFooter($id, $name="Submit"){
    echo    "<tr>";
    echo        "<td></td>";
    echo        "<input type='hidden' name='id' value='".$id."'>";
    echo        "<td><input class='nav' type='submit' value='{$name}'></td>";
    echo    "</tr>";
    echo    "</table>";
    echo    "</form>";
}