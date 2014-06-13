<?php
/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 12-6-14
 * Time: 11:52
 */

//dummycode for configuratiemagement
// to do change name and remove some code

function displayContentConfig($postData) {
    switch($postData) {
        case "displayHardware" : displayHardware(); break;
        case "displaySoftware" : displaySoftware(); break;
        default : echo "Hello ".ucfirst($_SESSION['user']);
    }
}

function displayMenuConfig() {
    new Button("Hardware", "displayHardware");
    new Button("Software", "displaySoftware");
}


    function displayHardware()
    {
        new HelpdeskTable("Hardware", "SELECT * FROM hardware");

    }

    function displaySoftware()
    {
        new HelpdeskTable("Software", "SELECT * FROM software");
    }



    //function CI_Toevoegen()
    function InkoopInkooporderToevoegen()
    {
        global $con;

        $sql = "SELECT * FROM hardware
		GROUP BY id_hardware";

        $row = mysqli_query($con, "$sql");

        echo "  <form method=\"post\" action=\"index.php\">";
        echo " 	<table>";
        echo "	<tr>";
        echo "	       <th>id_hardware</th>";
        echo "	      	<th>soort</th>";
        echo "          <th>locatie</th>";
        echo "	       <th>os</th>";
        echo "	      	<th>merk</th>";
        echo "          <th>leverancier</th>";
        echo "          <th>aanschafjaar</th>";
        echo "	 </tr>";

        while($row2 = mysqli_fetch_array($row))
        {
            echo "<tr>";
            echo " <td><input type='checkbox' name='Artikelnr[]' value='".$row2['Artikelnr']."'>".ucfirst($row2['Naam'])."</td>";
            echo "	<td><input type=\"text\" name=\"Hoeveelheid[]\" /></td>";
            echo "</tr>";
        }

        echo"		<tr>";
        echo"			<td></td>";
        echo"			<td><input type=\"submit\" value=\"Bestellen\" /></td>";
        echo"			    <input type=\"hidden\" name='display' value=\"inkoopbestelbevestig\" />";
        echo"		</tr>";
        echo" 	</table>";

        echo" </form>";
    }


    //function CI_wijzigen
	function InkoopInkooporderBewerk()
    {
        global $_POST;
        global $con;

        $id = $_POST['Artikelnr'];
        $id2 = $_POST['Hoeveelheid'];
        $check = $_POST['change'];
        $aantal=count($id);

        if($check !== 'x')
        {
            for($x=0; $x<=$aantal; $x++)
            {
                $artnr = $id[$x];
                $hoeveel = $id2[$x];

                echo $artnr;
                echo $hoeveel;

                $sql = "INSERT INTO inkooporder (Artikelnr, Hoeveelheid)
	            VALUES ($artnr, $hoeveel)";

                mysqli_query($con, "$sql");

                $sql2 = "UPDATE artikel
		    SET Besteld = (Besteld + $hoeveel)
		    WHERE Artikelnr = $artnr";
                mysqli_query($con, "$sql2");
            }
        }

        $_POST['display'] = 'inkoopartikelen';
    }
?>