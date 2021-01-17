<!DOCTYPE HTML>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Weeklymarket</title>
    <?php
        include './includes/db.php';


        $user = getDBUser();
        echo('<meta http-equiv="refresh" content="5; url=./'.$user['privilege'].'">');
            
    ?>
</head>
<body>
    <h1>Welcome to the marketadministration</h1>
    <h2>Login</h2>
    <?php       
        echo('<p>welcome ' . $user['username'] . ', you will be redirected to ' . $user['privilege'] . '-interface within 5 seconds</p>');
    ?>         
</body>
</html>