<?php
function createDbConnection(){
    $user = getenv('db_user');
    $pass = getenv('db_password');
    $db = getenv('db');
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
