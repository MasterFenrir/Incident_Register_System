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
        case "displayAddHardware" : displayAddHardware(); break;
        default : displayLandingConfig();
    }
}

function displayMenuConfig() {
    new Button("Hardware", "displayHardware");
    new Button("Software", "displaySoftware");
    new Button("Add_Hardware", "displayAddHardware");
}


    function displayHardware()
    {
        new HelpdeskTable("Hardware", "SELECT * FROM hardware");
    }

    function displaySoftware()
    {
        new HelpdeskTable("Software", "SELECT * FROM software");
    }

    function displayAddHardware()
    {
        formHeader();
        textField("Hardware_ID");
        dropDown("Soort", queryToArray("SELECT soort FROM hardware GROUP BY soort"));
        dropDown("Locatie", queryToArray("SELECT locatie FROM hardware GROUP BY locatie"));
        dropDown("OS", queryToArray("SELECT naam FROM software WHERE soort LIKE '%besturingssysteem%'"));
        CheckBoxes("Software", queryToArray("SELECT naam FROM software WHERE soort NOT LIKE '%besturingssysteem%'"), 3);
        textField("Leverancier");
        textField("Aanschaf_jaar");
        textField("Status");
        formFooter("addHardware");
    }

    function displayLandingConfig()
    {
        echo "Hello ".ucfirst($_SESSION['user']);
    }



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

	//Bugged, geeft hoeveelheid niet mee bij 1 artikel
	function InkoopInkooporderBevestig()
    {
        global $_POST;
        global $con;

        $id = $_POST['Artikelnr'];
        $id2 = $_POST['Hoeveelheid'];

        $sql2 = "SELECT COUNT(Artikelnr) AS aantal FROM artikel";
        $query2 = mysqli_query($con, "$sql2");

        list($aantal) = mysqli_fetch_array($query2);

        echo "<form method=\"post\" action=\"index.php\">";
        echo "<table>";
        echo 	"<tr>";
        echo 		"<th>Artikel</th>";
        echo 		"<th>Aantal</th>";
        echo 	"</tr>";

        for($x=0; $x<$aantal; $x++)
        {
            if(!empty($id[$x]))
            {
                $artnr = $id[$x];
                //$hoeveel = $id2[$x];


                //echo $hoeveel;

                $sql = "SELECT Naam FROM artikel
			WHERE Artikelnr = $artnr";
                $query = mysqli_query($con, "$sql");

                list($naam) = mysqli_fetch_array($query);

                echo 	"<tr>";
                echo 		"<td>$naam</td>";
                echo 	"</tr>";
            }
        }

        echo    "<th colspan=\"2\" class=\"klaar\">";
        echo    "<form action=\"/index.php\" method=\"post\">";
        echo    "<input type=\"hidden\" name=\"Artikelnr[]\" value=\"$id\">";
        echo    "<input type=\"hidden\" name=\"Hoeveelheid[]\" value=\"$id2\">";
        echo    "<input type=\"hidden\" name=\"bewerk\" value=\"inkoopbewerk\">";
        echo    "<input class=\"logout\" type=\"submit\" value=\"Bevestigen\">";
        echo    "</form>";

        echo    "<form action=\"/index.php\" method=\"post\">";
        echo    "<input type=\"hidden\" name=\"bewerk\" value=\"inkoopbewerk\">";
        echo    "<input type=\"hidden\" name=\"change\" value=\"x\">";
        echo    "<input class=\"logout\" type=\"submit\" value=\"Anuleren\">";
        echo    "</form>";
        echo    "</th>";
        echo "</table>";
        echo "</form>";
    }

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