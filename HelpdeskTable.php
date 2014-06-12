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
         * Main function
         */
        public function makeTable()
        {
            $row = mysqli_fetch_assoc($this->result);

            echo "<table>";
            echo "<tr><th colspan=".count($row)." class='tableTitle'>".$this->name."</th></tr>";

            $this->makeColumns();
            $this->makeRows();

            echo "</table>";
        }

        private function makeColumns()
        {
            $row = mysqli_fetch_assoc($this->result);

            echo "<tr>";
            foreach($row as $key=>$value) {
                if($_GET['sort'] == 'asc' && $_GET['order'] == $key) {
                    echo "<th><a href='index.php?order=$key&sort=desc'>".ucfirst($key)."</th>";
                } else {
                    echo "<th><a href='index.php?order=$key&sort=asc'>".ucfirst($key)."</th>";
                }
            }
            echo "</tr>";
        }

        private function makeRows()
        {
            while($row = mysqli_fetch_assoc($this->result)) {
                echo "<tr>";
                foreach($row as $key=>$value) {
                    echo "<td>".ucfirst($value)."</td>";
                }
                echo "</tr>";
            }
        }
} 