<?php
session_start();
if (isset($_POST['singup'])){
    require 'db.php';
    $error = false;
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordRepeat = $_POST['password-repeat'];
    $privilege = $_POST['profile_typ'];
    
    if(empty($username) || empty($password) || empty($passwordRepeat)){
        
        $_SESSION['message'] ='Alle Felder müssen ausgefühlt werden.';
        header("Location: ../index.php");
        exit();
    }
    elseif (!filter_var($username, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Zäöüèé]*$/", $firstname) && !preg_match("/^[a-zA-Zäöüèé]*$/", $lastname))
    {
        
        $_SESSION['message'] ='Benutzername ist keine gültige E-Mailadresse.';
        header("Location: ../index.php");
        exit();
    }
    
    elseif($password !== $passwordRepeat){
        
        $_SESSION['message'] ='Das Passwort stimmt nicht überein.';
        header("Location: ../index.php");
        exit();
    }
    
    else {
        $pdo = createDbConnection();
        $statement = $pdo->prepare("SELECT * FROM user WHERE username = :username");
        $result = $statement->execute(array('username' => $username));
        $user = $statement->fetch();
        
        if($user !== false) {
           
            $_SESSION['message'] ='Der Benutzername ist schon vergeben.';
            header("Location: ../index.php");
            exit();
           
        }    

        
        else{
    
            if(!$error) {    
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $statement = $pdo->prepare("INSERT INTO user (username, password, privilege) VALUES (:username, :password, :privilege)");
                $result = $statement->execute(array('username' => $username, 'password' => $password_hash , 'privilege' => $privilege));
                
                if($result) {        
                    
                    $_SESSION['message'] ='Erfolgreich Registriert.';
                    header("Location: ../index.php");
                    exit();

                    $showFormular = false;
                } else {
                  
                    $_SESSION['message'] ='Beim Abspeichern ist leider ein Fehler aufgetreten.';
                    header("Location: ../index.php");
                    exit();
                }
            } 
        }
    }
}

