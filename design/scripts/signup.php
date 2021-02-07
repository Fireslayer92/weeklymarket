<?php
if (isset($_POST['singup'])){
    require 'db.php';
    $error = false;
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordRepeat = $_POST['password-repeat'];
    $privilege = "new";
    
    if(empty($username) || empty($password) || empty($passwordRepeat)){
        header("Location: ../index.php?error=emtyfields&user=".$username. "&mail".$email);
        exit();
    }
    elseif (!filter_var($username, FILTER_VALIDATE_EMAIL) && !preg_match("/^[a-zA-Zäöüèé]*$/", $firstname) && !preg_match("/^[a-zA-Zäöüèé]*$/", $lastname))
    {
        header("Location: ../index.php?error=invalidmail&firstname=");
    }
    /*elseif (!preg_match("/^[a-zA-Zäöüèé]*$/", $firstname)){
        header("Location: ../index.php?error=invalidfirstname&lastname=" .$lastname. "&username".$username);
        exit();
    }
    elseif (!preg_match("/^[a-zA-Zäöüèé]*$/", $lastname)){
        header("Location: ../index.php?error=invalidmail&firstname=" .$firstname. "&lastname".$lastname);
        exit();
    } */
    elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)){
        header("Location: ../index.php?error=invalidmail&firstname=" .$firstname. "&lastname".$lastname);
        exit();
    }
    elseif($password !== $passwordRepeat){
        header("Location: ../index.php?error=passwordcheck&firstname=" .$lastname. "&username".$username);
        exit();
    }
    
    else {
        $statement = $pdo->prepare("SELECT * FROM user WHERE username = :username");
        $result = $statement->execute(array('username' => $username));
        $user = $statement->fetch();
        
        if($user !== false) {
            header("Location: ../index.php?error=username&firstname=" .$lastname. "&username".$username);
            exit();
            $error = true;
        }    

        
        else{
    
            if(!$error) {    
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $statement = $pdo->prepare("INSERT INTO user (username, password, privilege) VALUES (:username, :password, :privilege)");
                $result = $statement->execute(array('username' => $username, 'password' => $password_hash , 'privilege' => $privilege));
                
                if($result) {        
                    header("Location: ../index.php?sucess=Sign in");
                    exit();

                    $showFormular = false;
                } else {
                    echo 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
                }
            } 
        }
    }
}

