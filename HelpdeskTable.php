<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 11-6-14
 * Time: 11:39
 */

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

        /*
         * Constructor for the class
         *
         * @param $name: Name of the table, displayed in top row
         * @param $query: SQL query to make table from
         */
        public function __construct($name, $query)
        {
            $this->query = $query;
            $this->name = $name;

            //If order and sort GET information are set it appends the SQL query to include them
            if(isset($_GET['order']) && isset($_GET['sort'])) {
                $this->query = $query . " ORDER BY ".$_GET['order']. " " . $_GET['sort'];
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
            //Fetches a row from the results
            $row = mysqli_fetch_assoc($this->result);
            //Checks how many columns the result has
            $count = count($row);

            echo "<table>";
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
            //Fetches a row from the results
            $row = mysqli_fetch_assoc($this->result);

            echo "<tr>";

            //Gets the key/value pairs from the rows and echo's them as url's with GET info
            //used to sort the columns.
            foreach($row as $key=>$value) {
                if($_GET['sort'] == 'asc' && $_GET['order'] == $key) {
                    echo "<th><a href='index.php?order=$key&sort=desc'>".ucfirst($key)."</th>";
                } else {
                    echo "<th><a href='index.php?order=$key&sort=asc'>".ucfirst($key)."</th>";
                }
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
        }
} 