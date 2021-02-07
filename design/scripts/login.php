<?php
if (isset($_POST['login-submit']))
{
    require 'db.php';
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(empty($username) || empty($password))
    {
        header("Location: ../index.php?error=emptyfields");
        exit();
    }
    else{

            $statement = $pdo->prepare("SELECT * FROM user WHERE username = :username");
            $result = $statement->execute(array('username' => $username));
            $user = $statement->fetch();

            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['idUser'] = $user['idUser'];
                $_SESSION['username'] = $user['username'];
                
                header("Location: ../index.php?login=success");
                exit();
            } else {
                header("Location: ../index.php?login=error");
                exit();
            }
        }
}
else{
    header("Location: ../index.php");
    exit();
    echo '<a href="scripts/logout.php" class="nav-link">Log out</a>';
}
?>
