<?php
/*
 * Class that makes a sortable table from a given SQL query's response.
 */
class HelpdeskTable
{
    //Result from the given query
    private $result;

    //Name given for the table
    private $name;
    //Query in string format
    private $query;
    private $nav;

        /*
         * Constructor for the class
         *
         * @param $name: Name of the table, displayed in top row
         * @param $query: SQL query to make table from
         */
        public function __construct($name, $query, $nav)
        {
            $this->query = $query;
            $this->name = $name;
            $this->nav = $nav;

            //If order and sort GET information are set it appends the SQL query to include them
            if(isset($_POST['order']) && isset($_POST['sort'])) {
                $this->query = $query . " ORDER BY ".$_POST['order']. " " . $_POST['sort'];
            }

            //Executes the query and stores result in $result
            global $con;
            $this->result = mysqli_query($con, $this->query);

            //Displays table to screen
            $this->makeTable();
        }

        /*
         * Echo's the table using the result from the query.
         */
        public function makeTable()
        {
            //Fetches a row from the results and resest pointer to first row
            $row = mysqli_fetch_assoc($this->result);
            mysqli_data_seek($this->result, 0);

            //Checks how many columns the result has
            $count = count($row);

            echo "<table class='gen'>";
            //Makes the title row of the table
            echo "<tr><th colspan=$count class='tableTitle'>".$this->name."</th></tr>";

            $this->makeColumns();
            $this->makeRows();

            echo "</table>";
        }

        /*
         * Makes the titles for the columns
         */
        private function makeColumns()
        {
            //Fetches a row from the results and resest pointer to first row
            $row = mysqli_fetch_assoc($this->result);
            mysqli_data_seek($this->result, 0);

            echo "<tr>";

            //Gets the key/value pairs from the rows and echo's them as url's with GET info
            //used to sort the columns.
            foreach($row as $key=>$value) {
                echo "<th>";
                if($_POST['sort'] == 'asc' && $_POST['order'] == $key) {
                    echo    "<form action='/index.php' method='post'>";
                    echo    "<input type='hidden' name='sort' value='desc'>";
                    echo    "<input type='hidden' name='order' value=".$key.">";
                    echo    "<input type='hidden' name='display' value=".$this->nav.">";
                    echo    "<input class='order' type='submit' value=".ucfirst($key).">";
                    echo    "</form>";
                } else {
                    echo    "<form action='/index.php' method='post'>";
                    echo    "<input type='hidden' name='sort' value='asc'>";
                    echo    "<input type='hidden' name='order' value=".$key.">";
                    echo    "<input type='hidden' name='display' value=".$this->nav.">";
                    echo    "<input class='order' type='submit' value='".ucfirst($key)."'>";
                    echo    "</form>";
                }
                echo "</th>";
            }
            echo "</tr>";
        }

        /*
         * Makes the rows of the table.
         */
        private function makeRows()
        {
            //Iterates over every row in the results
            while($row = mysqli_fetch_row($this->result)) {
                echo "<tr>";
                //echo's a table value for each value in the row
                foreach($row as $value) {
                    echo "<td>".ucfirst($value)."</td>";
                }
                echo "</tr>";
            }
            //Resest pointer to first row
            mysqli_data_seek($this->result, 0);
        }
}
