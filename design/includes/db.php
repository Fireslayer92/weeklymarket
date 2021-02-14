<?php
function createDbConnection(){
    $user = 'root';
    $pass = '';
    $db = 'marktstand';
        try {
            $dsn = "mysql:host=localhost;dbname=$db";
            $dbconnection = new PDO($dsn, $user, false);
            return $dbconnection;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    
}
function getDBUser(){
    $dbo = createDbConnection();
    $stmt = $dbo->prepare("SELECT * from user where idUser = '1'");
    $stmt -> execute();
    $user = $stmt -> fetch();
    return $user;
}
?>
