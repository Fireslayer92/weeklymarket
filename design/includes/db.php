<?php
function createDbConnection(){
    $user = $_ENV['dbuser'];
    $pass = $_ENV['dbpassword'];
    $db = $_ENV['db'];
        try {
            $dsn = "mysql:host=localhost;dbname=$db";
            $dbconnection = new PDO($dsn, $user, $pass);
            return $dbconnection;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    
}
?>
