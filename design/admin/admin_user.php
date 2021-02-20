<!DOCTYPE HTML>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
<script
			  src="https://code.jquery.com/jquery-3.5.1.min.js"
			  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
			  crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="../includes/jquery.tablesort.min.js"></script>
    <script src="../includes/script.js"></script>
    <title>Weeklymarket</title>
    <meta charset="UTF-8">
    <?php
        include '../includes/db.php';
        $dbo = createDbConnection();
        if (isset($_POST['userChange']) && isset($_SERVER['REQUEST_URI'])){
            switch ($_POST['userChange']) {   

                case 'new':
                    $error = false;
                    if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['passwordRepeat'])){
                        //Errorhandling
                        echo('Error');
                        $error = true;
                    }
                    elseif(!filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)){
                        /*Errorhandling*/
                        echo('Error in validate email');
                        $error = true;
                    }
                    elseif($_POST['password'] !== $_POST['passwordRepeat']){
                        //Errorhandling
                        echo('Error in validate password');
                        $error = true;
                    }
                    else{
                        $stmt = $dbo->prepare("SELECT * FROM user WHERE username = :username");
                        $stmt->execute(array('username' => $_POST['username']));
                        $result = $stmt->fetch();
                        
                        if($result !== false) {
                            //Errorhandling
                            echo('Error');
                            $error = true;
                        }    
                        else{
                            if(!$error) {    
                                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                                
                                $stmt = $dbo->prepare("INSERT INTO user (username, password, privilege) VALUES (:username, :password, 'admin')");
                                $result = $stmt->execute(array('username' => $_POST['username'], 'password' => $password_hash));
                                
                                if(!$result) {        
                                    echo ('Beim Abspeichern ist leider ein Fehler aufgetreten<br>');
                                }
                            }
                        }
                    }
                    break;
                
                case 'delete':
                    $stmt = $dbo->prepare("SELECT count(idUser) as userCount from user where privilege='admin'");
                    $stmt->execute();
                    $result = $stmt->fetch();

                    if ($result['userCount'] > 1){
                        $stmt = $dbo->prepare("Delete from user where idUser = :idUser");
                        $stmt->execute(array('idUser' => $_POST['idUser']));
                    }
                    else {
                        echo('letzter Admin-Benutzer kann nicht gel&ouml;scht werden');
                    }
                    break;

                default:
                    break;
            }
            
        } 

    ?>
</head>
<body>
    <?php
        include '../includes/nav.php';
    ?>
    
    <h2>Admin-Benutzer</h2>
   
    <div class="container-fluid">
  <div class="row">
        <div class="col m-1">
        </div>
        <div class="col-10 m-1 ">
        <div class="d-flex justify-content-between">
            <input id="filterInput" type="text" placeholder="Suchen..">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUser">Benutzer hinzuf&uuml;gen</button>
        </div>
    <table class="table table-hover table-striped text-center">
        <thead>
        <tr>
            <th>Name</th>
        </tr>
        <thead>
        <tbody id='filterTable'>
        <?php
            $stmt = $dbo -> prepare("SELECT idUser, username from user where privilege = 'admin'");
            $stmt -> execute();
            $result = $stmt -> fetchAll();
            foreach ($result as $row){
                echo('<tr>');
                echo('<td>');
                    echo($row['username']);
                echo('</td>');
                echo('<td>');
                    echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#delete'.$row['idUser'].'">Benutzer l&ouml;schen</button>');
                echo('</td>');
                echo('<div class="modal fade" id="delete'.$row['idUser'].'" tabindex="-1" role="dialog">');
                    echo('<div class="modal-dialog" role="document">');
                        echo('<div class="modal-content">');
                            echo('<div class="modal-header">');
                                echo('<b></b><br/>');
                            echo('</div>');
                            echo('<div class="modal-body">');
                                echo('Wollen Sie den Benutzer '.$row['username'].' wirklich l&ouml;schen?<br/><p class="text-danger"><b>Warnung! Dies kann nicht rückgängig gemacht werden!</b></p>');
                                echo('<form method="POST">');
                                    echo('<input type hidden name="idUser" id="idUser" value="'.$row['idUser'].'"/><br/>');
                                    echo('<div class="modal-footer">');
                                        echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                        echo('<button type="submit" name="userChange" value="delete" class="btn btn-primary">Benutzer l&ouml;schen</button>');
                                        echo('</form>');
                                    echo('</div>');
                            echo('</div>');
                        echo('</div>');
                    echo('</div>');
                echo('</div>');
                echo('</tr>');
            }
        ?>
        </tbody>
    </table>
    <div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserLabel">Benutzer hinzuf&uuml;gen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
            <div class="modal-body">
                <div class="mb-3">
                    <label for="username" class="col-form-label">Username:</label>
                    <input type="email" class="form-control" id="username" name="username" required="required">
                </div>
                <div class="mb-3">
                    <label for="passwort" class="col-form-label">Passwort:</label>
                    <input type="password" class="form-control" id="password" name="password" required="required">
                </div>
                <div class="mb-3">
                    <label for="passwordRepeat" class="col-form-label">Passwort wiederholen:</label>
                    <input type="password" class="form-control" id="passwordRepeat" name="passwordRepeat" required="required">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;
                <button class="btn btn-primary" id="userChange" type="submit" name="userChange" value="new">Benutzer hinzuf&uuml;gen</button>
            </div>
            </form>
            </div>
        </div>
    </div>
      </div>
      <div class="col m-1">
      </div>
   </div>
 </div>


        
</body>
</html>