<script>
    function show_confirm(){
        return confirm("Weet je zeker dat je deze entry wil verwijderen?");
    }
</script>
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
    private $primeKey;
    private $edit;
    private $delete;
    private $details;

        /*
         * Constructor for the class
         *
         * @param $name: Name of the table, displayed in top row
         * @param $query: SQL query to make table from
         * @param $nav: Navigation id used for re-ordering the table
         *
         * The below paramaters are optional and can be null if you don't want
         * edit or delete functionality in the table.
         *
         * @param $edit: Navigation id used to find the correct edit function
         * @param $delete: Navigation id used to find the correct delete function
         * @param $primeKey: Primary key to use for edit and delete functions
         */
        public function __construct($name, $query, $nav, $edit, $delete, $primeKey, $search, $details)
        {
            $this->query = $query;
            $this->name = $name;
            $this->nav = $nav;
            $this->primeKey = $primeKey;
            $this->edit = $edit;
            $this->delete = $delete;
            $this->search = $search;
            $this->details = $details;

            //If order and sort GET information are set it appends the SQL query to include them
            if(isset($_POST['order']) && isset($_POST['sort'])) {
                $this->query = $query . " ORDER BY ".$_POST['order']. " " . $_POST['sort'];
            }

            //Executes the query and stores result in $result
            global $con;
            $this->result = mysqli_query($con, $this->query) or die(mysqli_error($con));

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
            if(count($row) > 0) {
                echo "<tr><th colspan=$count class='tableTitle'>".$this->name."</th></tr>";
            }

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

            if(count($row) > 0) {
                echo "<tr>";

                //Gets the key/value pairs from the rows and echo's them as url's with GET info
                //used to sort the columns.
                foreach($row as $key=>$value) {
                    echo "<th>";
                    if($_POST['sort'] == 'asc' && $_POST['order'] == $key && $this->nav != null) {
                        echo    "<form action='/index.php' method='post'>";
                        echo    "<input type='hidden' name='sort' value='desc'>";
                        echo    "<input type='hidden' name='order' value=".$key.">";
                        if($this->search != null) {echo "<input type='hidden' name='search' value='".$this->search."'>";}
                        echo    "<input type='hidden' name='display' value=".$this->nav.">";
                        echo    "<input class='order' type='submit' value=".ucfirst($key).">";
                        echo    "</form>";
                    } elseif($this->nav != null) {
                        echo    "<form action='/index.php' method='post'>";
                        echo    "<input type='hidden' name='sort' value='asc'>";
                        echo    "<input type='hidden' name='order' value=".$key.">";
                        if($this->search != null) {echo "<input type='hidden' name='search' value='".$this->search."'>";}
                        echo    "<input type='hidden' name='display' value=".$this->nav.">";
                        echo    "<input class='order' type='submit' value='".ucfirst($key)."'>";
                        echo    "</form>";
                    } else {
                        echo ucfirst($key);
                    }
                    echo "</th>";
                }
                echo "</tr>";
            }
        }

        /*
         * Makes the rows of the table.
         */
        private function makeRows()
        {
            //Iterates over every row in the results
            while($row = mysqli_fetch_assoc($this->result)) {
                echo "<tr>";
                //echo's a table value for each value in the row
                foreach($row as $value) {
                    echo "<td>".$value."</td>";
                }
                $this->makeOptions($row);
                echo "</tr>";
            }
            //Resest pointer to first row
            mysqli_data_seek($this->result, 0);
        }

        /*
         *  If option values are entered into the constructor the edit and/or delete
         *  button are made.
         *
         * @param $row: Row currently being made.
         */
        private function makeOptions($row)
        {
            if($this->primeKey != null && $this->edit != null) {
                echo "<td>";
                echo    "<form action='/index.php' method='post'>";
                echo    "<input type='hidden' name='display' value='$this->edit'>";
                echo    "<input type='hidden' name='key' value=".$row[$this->primeKey].">";
                echo    "<input class='option' type='submit' value='Edit'>";
                echo    "</form>";
                if($this->delete == null) {echo "</td>";}
            }

            if($this->primeKey != null && $this->delete != null && ($row[$this->primeKey] != $_SESSION['user'])) {
                if($this->edit == null) {echo "<td>";}
                echo    "<form action='/index.php' method='post' onclick='return show_confirm();'>";
                echo    "<input type='hidden' name='id' value='$this->delete'>";
                echo    "<input type='hidden' name='display' value='".$this->nav."'>";
                echo    "<input type='hidden' name='key' value='".$row[$this->primeKey]."'>";
                echo    "<input class='option' type='submit' value='Delete'>";
                echo    "</form>";
                if($this->details == null) {echo "</td>";}
            }

            if($this->primeKey != null && $this->details != null) {
                if($this->edit == null && $this->delete == null) {echo "<td>";}
                echo    "<form action='/index.php' method='post'>";
                echo    "<input type='hidden' name='display' value='$this->details'>";
                echo    "<input type='hidden' name='key' value=".$row[$this->primeKey].">";
                echo    "<input class='option' type='submit' value='Details'>";
                echo    "</form>";
                echo "</td>";
            }
        }
}
