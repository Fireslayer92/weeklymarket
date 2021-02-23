

<?php
	session_start();
	?>
<!DOCTYPE HTML>
<html>
	<head>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
		<link href="../includes/stylesheet.css" rel="stylesheet">
		<script
			src="https://code.jquery.com/jquery-3.5.1.min.js"
			integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
			crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
		<script src="../includes/jquery.tablesort.min.js"></script>
		<script src="../includes/script.js"></script>
		<title>Weeklymarket</title>
		<?php
			if ( 'admin' != $_SESSION['privilege'] ) { //check privileges
			    // access denied
			    header('Location: ../index.php');
			} //check privileges
			setlocale (LC_ALL, '');
			include '../includes/db.php';
			$dbo = createDbConnection();
			if (isset($_POST['approvalChange']) && isset($_SERVER['REQUEST_URI'])){
			    switch ($_POST['approvalChange']) {
			        case 'new': //Add Approval
			            $approvalDate = date('Y-m-d H:i:s',strtotime($_POST['approvalDate']));
			            $insert = $dbo -> prepare ("INSERT INTO approval (date, status, reservation_idReservation) VALUES (:approvalDate,:approved,:idReservation)");
			            $insert -> execute(array('approvalDate' => $approvalDate, 'approved' => $_POST['approved'], 'idReservation' => $_POST['idReservation']));
			            break; //case new
			        
			        case 'approved': //set boothprovider from trial to approved
			            $update = $dbo -> prepare ("UPDATE boothprovider set status = 'approved' where idProvider = :idProvider");
			            $update -> execute(array('idProvider' => $_POST['idProvider']));
			            break; //case approved
			        
			        case 'blocked': //block provider
			            $update = $dbo -> prepare ("UPDATE boothprovider set status = 'blocked' where idProvider = :idProvider");
			            $update -> execute(array('idProvider' => $_POST['idProvider']));
			            break; //case blocked
			
			        default:
			            break; //case default
			    } //switch ($_POST['approvalChange'])
			    
			} //if (isset($_POST['approvalChange']) && isset($_SERVER['REQUEST_URI']))
		?>
	</head>
	<body>
		<?php
			include '../includes/nav.php'; //include nav bar
			?>
		<div class="container-fluid">
			<div class="row">
				<div class="col m-1">
				</div> <!-- <div class="col m-1"> -->
				<div class="col-10 m-1 "> <!-- main body for website -->
					<h2>Qualit&auml;tspr&uuml;fung</h2>
					<input id=filterInput type="text" placeholder="Suchen..">
					<table class="table table-hover table-striped text-center">
						<thead>
							<tr>
								<th><p>Reservationsnummer</p></th>
								<th><p>Anbieter</p></th>
								<th><p>Standort</p></th>
								<th><p>von</p></th>
								<th><p>bis</p></th>
								<th><p>Anzahl durchgef&uuml;hrter Stichproben</p></th>
								<th><p>Anzahl Stichproben i.O.</p></th>
								<th><p>Qualit&auml;tscheck durchgef&uuml;hrt</p></th>
								<th></th>
							</tr>
						</thead>
						<tbody id='filterTable'>
							<?php
								$stmt = $dbo -> prepare("SELECT bp.idProvider as 'idProvider', bp.name as 'boothprovider', bp.qCheck as 'qCheck' , s.name as 'site', r.fromDate as 'fromdate', r.toDate as 'todate', r.trail as 'trail', r.paid as 'paid', r.idReservation as 'idReservation', (select count(idApproval) from approval where reservation_idReservation = r.idReservation) as approval, (select count(idApproval) from approval where reservation_idReservation = r.idReservation and status = 1) as approved FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite where bp.status = 'trial'");
								$stmt -> execute();
								$result = $stmt -> fetchAll(); //SELECT bp.idProvider as 'idProvider', bp.name as 'boothprovider', bp.qCheck as 'qCheck' , s.name as 'site', r.fromDate as 'fromdate', r.toDate as 'todate', r.trail as 'trail', r.paid as 'paid', r.idReservation as 'idReservation', (select count(idApproval) from approval where reservation_idReservation = r.idReservation) as approval, (select count(idApproval) from approval where reservation_idReservation = r.idReservation and status = 1) as approved FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite where bp.status = 'trial'
								foreach ($result as $row){
								    echo('<tr>');
								    echo('<td>');
								    echo($row['idReservation']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['boothprovider']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['site']);
								    echo('</td>');
								    echo('<td>');
								    echo(date('d.m.Y',strtotime($row['fromdate'])));
								    echo('</td>');
								    echo('<td>');
								    echo(date('d.m.Y',strtotime($row['todate'])));
								    echo('</td>');
								    echo('<td>');
								    echo($row['approval']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['approved']);
								    echo('</td>');
								    echo('<td>');
								    if ($row['qCheck']==1){
								        echo('Ja');
								    } else {
								        echo('Nein');
								    }
								    echo('</td>');
								    echo('<td>');
								    if ($row['approved']!=$row['approval']){
								        echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#blocked'.$row['idProvider'].'">Anbieter sperren</button>'); //add button to block user in case approval failed
								    } elseif ($row['approved']<2){
								        echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addApproval'.$row['idReservation'].'">Pr&uuml;fung hinzuf&uuml;gen</button>'); //add button to add approval
								    } elseif ($row['approved']==2&&$row['qCheck']==1){
								        echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#approved'.$row['idProvider'].'">Account freischalten</button>'); //add button to unlock account
								    }
								    echo('</td>');
								    echo('<div class="modal fade" id="addApproval'.$row['idReservation'].'" tabindex="-1" role="dialog">'); //modal for adding approval
								        echo('<div class="modal-dialog" role="document">');
								            echo('<div class="modal-content">');
								                echo('<div class="modal-header">');
								                    echo('<label><b>Pr&uuml;fung f&uuml;r '.$row['boothprovider'].' hinzuf&uuml;gen</b></label><br/>');
								                echo('</div>'); //<div class="modal-header">
                                                echo('<form method="POST" action="checks.php">'); //form for information about new approval
                                                    echo('<div class="modal-body">');
                                                            echo('<label for="approvalDate">Datum</label><br/>');
                                                            echo('<input type="date" name="approvalDate" class="form-control" id="approvalDate" value="'.date('Y-m-d').'"/><br/>');
                                                            echo('<label>Pr&uuml;fung erfolgreich</label><br/>');
                                                            echo('<input type="radio" id="approved" name="approved" value="1">');
                                                            echo('<label for="approved">Ja</label><br/>');
                                                            echo('<input type="radio" id="approved" name="approved" value="0">');
                                                            echo('<label for="notApproved">Nein</label><br/>');
                                                            echo('<input type hidden name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/><br/>');
                                                            echo('<input type hidden name="approvalChange" id="approvalChange" value="new"/>');
                                                    echo('</div>'); //<div class="modal-body">
                                                    echo('<div class="modal-footer">');
                                                        echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>');
                                                        echo('<button type="submit" class="btn btn-primary">Pr&uuml;fung speichern</button>');
                                                    echo('</div>'); //<div class="modal-footer">
                                                echo('</form>'); //form for information about new approval
								            echo('</div>'); //<div class="modal-content">
								        echo('</div>'); //<div class="modal-dialog" role="document">
								    echo('</div>'); //<div class="modal fade" id="addApproval'.$row['idReservation'].'" tabindex="-1" role="dialog">
								    echo('<div class="modal fade" id="approved'.$row['idProvider'].'" tabindex="-1" role="dialog">'); //modal for unlocking account
								        echo('<div class="modal-dialog" role="document">');
								            echo('<div class="modal-content">');
								                echo('<div class="modal-header">');
								                    echo('<label><b>'.$row['boothprovider'].' freischalten</b></label><br/>');
                                                echo('</div>'); //<div class="modal-header">
                                                echo('<form method="POST" action="checks.php">'); //form for information about user to unlock
                                                    echo('<div class="modal-body">');
                                                        echo('<label>Sind Sie sich sicher, dass Sie '.$row['boothprovider'].' freischalten wollen?</label>');
                                                        echo('<input type hidden name="idProvider" id="idProvider" value="'.$row['idProvider'].'"/><br/>');
                                                        echo('<input type hidden name="approvalChange" id="approvalChange" value="approved"/>');
                                                    echo('</div>'); //<div class="modal-body">
                                                    echo('<div class="modal-footer">');
                                                        echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                                        echo('<button type="submit" class="btn btn-primary">Freischalten</button>');
                                                    echo('</div>'); //<div class="modal-footer">
                                                echo('</form>');//form for information about user to unlock
								            echo('</div>'); //<div class="modal-content">
								        echo('</div>'); //<div class="modal-dialog" role="document">
								    echo('</div>'); // <div class="modal fade" id="approved'.$row['idProvider'].'" tabindex="-1" role="dialog">
								    echo('<div class="modal fade" id="blocked'.$row['idProvider'].'" tabindex="-1" role="dialog">'); //modal for blocking account
								        echo('<div class="modal-dialog" role="document">');
								            echo('<div class="modal-content">');
								                echo('<div class="modal-header">');
								                    echo('<label><b>'.$row['boothprovider'].' blockieren</b></label><br/>');
                                                echo('</div>'); //<div class="modal-header">
                                                echo('<form method="POST" action="checks.php">'); //form for information about user to block
                                                    echo('<div class="modal-body">');
                                                        echo('<label>Sind Sie sich sicher, dass Sie '.$row['boothprovider'].' blockieren wollen? <br/><p class="text-danger"><b>Warnung! Dies kann nicht rückgängig gemacht werden!</b></p></label>');
                                                        echo('<input type hidden name="idProvider" id="idProvider" value="'.$row['idProvider'].'"/><br/>');
                                                        echo('<input type hidden name="approvalChange" id="approvalChange" value="blocked"/>');
                                                    echo('</div>'); //<div class="modal-body">
                                                    echo('<div class="modal-footer">');
                                                        echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                                        echo('<button type="submit" class="btn btn-primary">blockieren</button>');
                                                    echo('</div>'); //<div class="modal-footer">
                                                echo('</form>'); //form for information about user to block
								            echo('</div>'); //<div class="modal-content">
								        echo('</div>'); //<div class="modal-dialog" role="document">
								    echo('</div>'); //<div class="modal fade" id="blocked'.$row['idProvider'].'" tabindex="-1" role="dialog">
								
								    echo('</tr>');
								}
								?>
						</tbody>
					</table>
				</div> <!-- main body for website -->
				<div class="col m-1">
				</div> <!-- <div class="col m-1"> -->
			</div> <!-- <div class="row"> -->
		</div> <!-- <div class="container-fluid"> -->
	</body>
</html>

