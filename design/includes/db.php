<?php
function createDbConnection(){
    $user = 'USER407941_markt';
    $pass = 'Ibz123405';
    $db = 'db_407941_3';
        try {
            $dsn = "mysql:host=localhost;dbname=$db";
            $dbconnection = new PDO($dsn, $user, false);
            return $dbconnection;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    
}
?>
