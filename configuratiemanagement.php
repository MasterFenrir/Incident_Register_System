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

}

function displayMenuConfig() {

}


    function displayAfdInkoopArtikel()
    {
        global $con;

        $sql = "SELECT Artikelnr, Naam, Hoeveelheid, Levancier, Besteld
				From artikel
                ORDER BY Naam";
        $query = mysqli_query($con, "$sql");

        echo "<table id=\"keuken\">";
        echo "<tr>";
        echo    "<th>Artiklnr</th>";
        echo    "<th>Naam</th>";
        echo    "<th>Hoeveelheid</th>";
        echo	"<th>Besteld</th>";
        echo    "<th>Leverancier</th>";

        while (list($artikelnr, $naam, $hoeveelheid, $leverancier, $besteld) = mysqli_fetch_row($query))
        {
            echo "<tr>";
            echo    "<td>".$artikelnr."</td>";
            echo    "<td>".$naam."</td>";
            echo    "<td>".$hoeveelheid."</td>";
            echo    "<td>".$besteld."</td>";
            echo    "<td>".$leverancier."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    function navigationInkoop()
    {
        echo    "<form action=\"/index.php\" method=\"post\">";
        echo    "<input type=\"hidden\" name=\"display\" value=\"Home\">";
        echo    "<input class=\"nav\" type=\"submit\" value=\"Home\">";
        echo    "</form>";

        echo    "<form action=\"/index.php\" method=\"post\">";
        echo    "<input type=\"hidden\" name=\"display\" value=\"inkoopartikelen\">";
        echo    "<input class=\"nav\" type=\"submit\" value=\"Artikelen\">";
        echo    "</form>";

        echo	"<form action=\"/index.php\" method=\"post\">";
        echo    "<input type=\"hidden\" name=\"display\" value=\"inkoopbestellen\">";
        echo    "<input class=\"nav\" type=\"submit\" value=\"Bestellen\">";
        echo    "</form>";
    }

    function InkoopInkooporderToevoegen()
    {
        global $con;

        $sql = "SELECT * FROM artikel
		GROUP BY Artikelnr";

        $row = mysqli_query($con, "$sql");

        echo "  <form method=\"post\" action=\"index.php\">";
        echo " 	<table>";
        echo "	<tr>";
        echo "	       <th>Artikelen</th>";
        echo "	      	<th>Hoeveelheid</th>";
        echo "	 </tr>";

        while($row2 = mysqli_fetch_array($row))
        {
            echo "<tr>";
            echo 	"<td><input type='checkbox' name='Artikelnr[]' value='".$row2['Artikelnr']."'>".ucfirst($row2['Naam'])."</td>";
            echo 	"<td><input type=\"text\" name=\"Hoeveelheid[]\" /></td>";
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
                //$artnr = $id[$x];
                //$hoeveel = $id2[$x];

                echo $artnr;
                //echo $hoeveel;

                $sql = "SELECT Naam FROM artikel
			WHERE Artikelnr = $artnr";
                $query = mysqli_query($con, "$sql");

                list($naam) = mysqli_fetch_array($query);

                echo 	"<tr>";
                echo 		"<td>$naam</td>";
                echo 		"<td>$hoeveel</td>";
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