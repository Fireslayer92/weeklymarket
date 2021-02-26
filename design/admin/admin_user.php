<?php
	session_start();
?>
<!DOCTYPE HTML>
<html>
	<head>
        <!-- includes -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
		<link href="../includes/stylesheet.css" rel="stylesheet">
		<script
			src="https://code.jquery.com/jquery-3.5.1.min.js"
			integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
			crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
		<script src="../includes/jquery.tablesort.min.js"></script>
		<script src="../includes/script.js"></script>
		<title>Wochenmarkt</title>
		<meta charset="UTF-8">
		<?php
		$errt = "";     
            if ( 'admin' != $_SESSION['privilege'] ) { //check privileges
                // access denied
                header('Location: ../index.php');
            } //check privileges
			setlocale (LC_ALL, '');
			include '../includes/db.php';
			$dbo = createDbConnection();
			if (isset($_POST['userChange']) && isset($_SERVER['REQUEST_URI'])){
			    switch ($_POST['userChange']) {   
			
					case 'new': //Create new admin User
					    
			            if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['passwordRepeat'])){
			                $errt .= "Nicht alle Felder ausgef&uuml;llt";
			            }
			            elseif(!filter_var($_POST['username'], FILTER_VALIDATE_EMAIL)){
			                $errt .= "Bitte geben Sie eine E-Mail ein";
			            }
			            elseif($_POST['password'] !== $_POST['passwordRepeat']){
			                $errt .= "Passw&ouml;rter stimmen nicht &uuml;berein";
			            }
			            else{
			                $stmt = $dbo->prepare("SELECT * FROM user WHERE username = :username");
			                $stmt->execute(array('username' => $_POST['username']));
			                $result = $stmt->fetch();
			                
			                if($result !== false) {
								$errt .= "Benutzer mit dieser E-Mail existiert bereits";
			                }    
			                else{
			                        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT); //Create passwordhash and checking with DB
			                        
			                        $stmt = $dbo->prepare("INSERT INTO user (username, password, privilege) VALUES (:username, :password, 'admin')");
			                        $result = $stmt->execute(array('username' => $_POST['username'], 'password' => $password_hash));
			                        print_r($result);
			                        if(!$result) {        
			                            $errt .= "Fehler beim speichern des Benutzers";
			                        } else {
			                            echo('<script>window.location = window.location.href;</script>');
			                            exit();
			                        }
			                }
						}
			            break; //case new
			        
			        case 'delete': //delete admin user
			            $stmt = $dbo->prepare("SELECT count(idUser) as userCount from user where privilege='admin'");
			            $stmt->execute();
			            $result = $stmt->fetch();
			
			            if ($result['userCount'] > 1){ //Control if there is still an admin-account available
                            if ($_POST['idUser'] != $_SESSION['idUser']){ //Control if user to delete is currently logged in
                                $stmt = $dbo->prepare("Delete from user where idUser = :idUser"); //Delete User
                                $stmt->execute(array('idUser' => $_POST['idUser']));
                            }
                            else{
                                $errt .= 'Sie k&ouml;nnen sich nicht selbst l&ouml;schen';
                            }
			            }
			            else {
			                $errt .= 'letzter Admin-Benutzer kann nicht gel&ouml;scht werden';
			            }
			            break; //case delete
			
			        default:
                        break; //case default
			    } //switch ($_POST['userChange'])
			} //if (isset($_POST['userChange']) && isset($_SERVER['REQUEST_URI']))
			
			?>
	</head>
	<body>
		<?php
			include '../includes/errorhandling.php'; //include errormodal
			include '../includes/nav.php'; //include nav bar
			?>
		<div class="container-fluid">
			<div class="row">
				<div class="col m-1">    
				</div> <!-- <div class="col m-1"> -->
				<div class="col-10 m-1 "> <!-- main body for website -->
					<h2>Benutzerverwaltung</h2>
					<div class="d-flex justify-content-between"> <!-- Input row before table -->
						<input id=filterInput type="text" placeholder="Suchen..">
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUser">Benutzer hinzuf&uuml;gen</button> <!-- button to create new user-->
					</div> <!-- Input row before table -->
					<table class="table table-hover table-striped text-center"> <!-- usertable -->
						<thead>
							<tr>
                                <th><p>Name<p></th>
                                <th></th>
							</tr>
						<thead>
						<tbody id='filterTable'>
							<?php
								$stmt = $dbo -> prepare("SELECT idUser, username from user where privilege = 'admin'");
								$stmt -> execute();
								$result = $stmt -> fetchAll(); //SELECT idUser, username from user where privilege = 'admin'
								foreach ($result as $row){
								    echo('<tr>');
								    echo('<td>');
								        echo($row['username']);
								    echo('</td>');
								    echo('<td>');
								        echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#delete'.$row['idUser'].'">Benutzer l&ouml;schen</button>'); //Add button to delete User
								    echo('</td>');
								    echo('<div class="modal fade" id="delete'.$row['idUser'].'" tabindex="-1" role="dialog">'); //Modal for confirmation of user delete
								        echo('<div class="modal-dialog" role="document">');
								            echo('<div class="modal-content">');
								                echo('<div class="modal-header">');
								                    echo('<b></b><br/>');
                                                echo('</div>'); //<div class="modal-header">
                                                echo('<form method="POST">'); //Form for information about user delete
								                echo('<div class="modal-body">');
								                    echo('Wollen Sie den Benutzer '.$row['username'].' wirklich l&ouml;schen?<br/><p class="text-danger"><b>Warnung! Dies kann nicht r체ckg채ngig gemacht werden!</b></p>');
								                        echo('<input type hidden name="idUser" id="idUser" value="'.$row['idUser'].'"/><br/>');
								                        echo('<div class="modal-footer">');
								                            echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
								                            echo('<button type="submit" name="userChange" value="delete" class="btn btn-primary">Benutzer l&ouml;schen</button>');
								                        echo('</div>'); // <div class="modal-footer">
                                                echo('</div>'); // <div class="modal-body">
                                                echo('</form>'); //Form for information about user delete
								            echo('</div>'); // <div class="modal-content">
								        echo('</div>'); // <div class="modal-dialog" role="document">
								    echo('</div>'); //Modal for confirmation of user delete
								    echo('</tr>');
								}
								?>
						</tbody>
					</table> <!-- usertable -->
					<div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true"> <!-- Modal for user creation-->
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="addUserLabel">Benutzer hinzuf&uuml;gen</h5>
								</div> <!-- <div class="modal-header"> -->
								<form method="POST"> <!-- form for user creation -->
									<div class="modal-body">
										<div class="mb-3">
											<label for="username" class="col-form-label">Username:</label>
											<input type="email" class="form-control" id="username" name="username" required="required">
										</div> <!-- <div class="mb-3"> -->
										<div class="mb-3">
											<label for="passwort" class="col-form-label">Passwort:</label>
											<input type="password" class="form-control" id="password" name="password" required="required">
										</div> <!-- <div class="mb-3"> -->
										<div class="mb-3">
											<label for="passwordRepeat" class="col-form-label">Passwort wiederholen:</label>
											<input type="password" class="form-control" id="passwordRepeat" name="passwordRepeat" required="required">
										</div> <!-- <div class="mb-3"> -->
									</div> <!-- <div class="modal-body"> -->
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;
										<button class="btn btn-primary" id="userChange" type="submit" name="userChange" value="new">Benutzer hinzuf&uuml;gen</button>
									</div> <!-- <div class="modal-footer"> -->
								</form> <!-- form for user creation -->
							</div> <!-- <div class="modal-content"> -->
						</div> <!-- <div class="modal-dialog"> -->
					</div> <!-- Modal for user creation-->
				</div> <!-- main body for website -->
				<div class="col m-1">
				</div> <!-- <div class="col m-1"> -->
			</div> <!--<div class="row">-->
		</div> <!-- <div class="container-fluid"> -->
		
	</body>
</html>

