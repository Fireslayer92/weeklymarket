<?php
function createDbConnection(){
    $user = 'weeklyma_marktstand';
    $pass = '!asRijEYZsQGkruEJA61';
    $db = 'weeklyma_marktstand';
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
