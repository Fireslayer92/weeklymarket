<?php
session_start();
if (isset($_POST['login-submit']))
{
    require 'db.php';
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(empty($username) || empty($password))
    {
        echo ('<div class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true">');
        echo ('<div class="d-flex">');
        echo ('<div class="toast-body">');
        echo ('Hello, world! This is a toast message.');
        echo ('</div>');
        echo ('<button type="button" class="btn-close me-2 m-auto" data-dismiss="toast" aria-label="Close"></button>');
        echo ('</div>');
        echo ('</div>)');
                
        $_SESSION['message'] ='Es müssen alle Felder ausgefühlt werden.';
        header("Location: ../index.php");
        exit();
    }
    else{
            $pdo = createDbConnection();
            //$pdo= createDbConnection();
            $statement = $pdo->prepare("SELECT * FROM user WHERE username = :username");
            $result = $statement->execute(array('username' => $username));
            $user = $statement->fetch();

            if (password_verify($password, $user['password'])) {
                
                $_SESSION['idUser'] = $user['idUser'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['privilege'] = $user['privilege'];                
     
                $_SESSION['successMessage'] ='Erfolgreich Angemeldet.';
                header("Location: /index.php");
                exit();
                
            } else {
                
                $_SESSION['message'] ='Benutzername oder Kennwort ist falsch.';
                header("Location: /index.php");
                exit();
            }
        }
}
else{
    header("Location: /index.php");
    exit();
    echo '<a href="includes/logout.php" class="nav-link">Log out</a>';
}
?>