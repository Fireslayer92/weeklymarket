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
			if ( 'admin' != $_SESSION['privilege'] ) { //check privileges
			    // access denied
			    header('Location: ../index.php');
			} //check privileges
			setlocale (LC_ALL, '');
			         include '../includes/db.php'; //include database settings
			         $dbo = createDbConnection();
			         if (isset($_POST['billingChange']) && isset($_SERVER['REQUEST_URI'])){
			             switch ($_POST['billingChange']) {
			                 case 'new': //add new bill
			                     $billingDate = date('Y-m-d H:i:s',strtotime($_POST['billingDate']));
			                     $insert = $dbo -> prepare ("INSERT INTO billing (billingDate, billingCondition, billingStatus, reservation_idReservation) VALUES (:billingDate,:billingCondition,'Open',:idReservation)");
			                     $insert -> execute(array('billingDate' => $billingDate, 'billingCondition' => $_POST['billingCondition'], 'idReservation' => $_POST['idReservation']));
			                     $update = $dbo -> prepare ("UPDATE reservation set paid = 1 where idReservation = :idReservation");
			                     $update -> execute(array('idReservation' => $_POST['idReservation']));
			                     break; //case new
			                 case 'paid': //mark bill as paid
			                     $update = $dbo -> prepare ("UPDATE billing set billingStatus = 'paid' where idBilling = :idBilling");
			                     $update -> execute(array('idBilling' => $_POST['idBilling']));
			                     break; //case paid
			                 default:
			                     break; //case default
			             } //switch ($_POST['billingChange'])
			    header("Location: ./billing.php"); //refresh page to clear $_POST
			         } //if (isset($_POST['billingChange']) && isset($_SERVER['REQUEST_URI']))
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
				<div class="col-10 m-1"> <!-- main body for website -->
					<h2>Rechnungen</h2>
					<input id=filterInput type="text" placeholder="Suchen..">
					<table class="table table-hover table-striped text-center"> <!-- billingtable -->
						<thead>
							<tr>
								<th><p>Rechnungsdatum</p></th>
								<th><p>Zahlungsziel</p></th>
								<th><p>Status</p></th>
								<th><p>Reservationsnummer</p></th>
								<th><p>Standanbieter</p></th>
								<th><p>Standort</p></th>
								<th></th>
							</tr>
						</thead>
						<tbody id='filterTable'>
							<?php
								$stmt = $dbo -> prepare ("SELECT b.idBilling as 'idBilling', b.billingDate as 'billingDate', b.billingCondition as 'billingCondition', b.billingStatus as 'billingStatus', r.idReservation as 'idReservation', bp.name as 'boothProvider', s.name as 'site' from reservation r join billing b on b.reservation_idReservation = r.idReservation join site s on s.idSite = r.site_idSite join boothProvider bp on bp.idProvider = r.boothProvider_idProvider");
								$stmt -> execute();
								$result = $stmt -> fetchAll(); //SELECT b.idBilling as 'idBilling', b.billingDate as 'billingDate', b.billingCondition as 'billingCondition', b.billingStatus as 'billingStatus', r.idReservation as 'idReservation', bp.name as 'boothProvider', s.name as 'site' from reservation r join billing b on b.reservation_idReservation = r.idReservation join site s on s.idSite = r.site_idSite join boothProvider bp on bp.idProvider = r.boothProvider_idProvider
								foreach($result as $row){
								    echo('<tr>');
								    echo('<td>');
								    echo(date('d.m.Y',strtotime($row['billingDate'])));
								    echo('</td>');
								    echo('<td>');
								    echo($row['billingCondition']);
								    echo('</td>');
                                    echo('<td>');
                                    if($row['billingStatus'] == 'Open'){
                                        echo('offen');
                                    } elseif ($row['billingStatus']== 'paid'){
                                        echo('bezahlt');
                                    }
								    echo('</td>');
								    echo('<td>');
								    echo($row['idReservation']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['boothProvider']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['site']);
								    echo('</td>');
								    echo('<td>');
								    if($row['billingStatus'] != 'paid'){ //add button to set bill to status paid
								        echo('<form method="POST" action="./billing.php">');
								        echo('<input type="hidden" name="idBilling" id="idBilling" value="'.$row['idBilling'].'"/>');
								        echo('<input type="hidden" name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/>');
								        echo('<input type="hidden" name="billingChange" id="billingChange" value="paid"/>');
								        echo('<button class="btn btn-primary" type="submit">Rechnung bezahlt</button>');
								        echo('</form>');
								    }
								    echo('</td>');
								    echo('</tr>');
								    
								}
								?>
						</tbody>
					</table> <!-- billingtable -->
				</div> <!-- main body for website -->
				<div class="col m-1">
				</div> <!-- <div class="col m-1"> -->
			</div> <!-- <div class="row"> -->
		</div> <!-- <div class="container-fluid"> -->
	</body>
</html>