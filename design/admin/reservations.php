

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
		<meta charset="UTF-8">
		<?php
			if ( 'admin' != $_SESSION['privilege'] ) { //check privileges
			    // access denied
			    header('Location: ../index.php');
			} //check privileges
			setlocale (LC_ALL, '');
			include '../includes/db.php';
			$dbo = createDbConnection();
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
                    <h2>Reservationen</h2> 
					<h3>&Uuml;bersicht</h3>
					<div id="overview"> <!-- overview over all reservations by site -->
						<?php
							$sitestmt = $dbo -> prepare("SELECT idSite, name, spaces from site;");
							$sitestmt -> execute();
							$siteresult = $sitestmt -> fetchAll(); //SELECT idSite, name, spaces from site;
							foreach ($siteresult as $row){ //add extendable card for each site
							    echo('<div class="card">');
                                    echo('<div class="card-header" id="heading'.$row['idSite'].'">');
                                        echo('<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#overview'.$row['idSite'].'" aria-expanded="true" aria-controls="overview'.$row['idSite'].'">'.$row['name'].'</button>');
                                    echo('</div>'); //<div class="card-header" id="heading'.$row['idSite'].'">'
                                    echo('<div id="overview'.$row['idSite'].'" class="collapse" aria-labelledby="heading'.$row['idSite'].'" data-parent="#overview">');
                                        echo('<div class="card-body">');
                                            echo('<table class="table table-hover table-striped text-center">'); //table for overview of the next 12 months
                                                echo('<thead>');
                                                echo('<tr>');
                                                    echo('<th class="no-sort">Monat</th>');
                                                    echo('<th class="no-sort">Anzahl Reservationen</th>');
                                                    echo('<th class="no-sort">Freie Pl&auml;tze</th>');
                                                echo('</tr>');
                                                echo('</thead>');
                                                echo('<tbody>');
                                                    for ($i=0;$i<12;$i++){ //loop trough next 12 months and give out month name
                                                        $month = date('m',strtotime('first day of +'.$i.' month'));
                                                        $date = date('Y-m-d',strtotime('first day of +'.$i.' month'));
                                                        echo('<tr>');
                                                        echo('<td>');
                                                        if ($month != 03){
                                                            echo(strftime("%B %Y",strtotime('first day of +'.$i.' month')));
                                                        } else {
                                                            echo("M&auml;rz ".strftime("%Y",strtotime('first day of +'.$i.' month')));
                                                        }
                                                        echo('</td>');
                                                        echo('<td>');
                                                        $resstmt = $dbo -> prepare("select count(idReservation) as count from reservation where fromDate <= :date and toDate >= :date  and site_idSite = :siteID");
                                                        $resstmt -> execute(array('date' => $date,'siteID' => $row['idSite']));
                                                        $resresult = $resstmt -> fetch(); //select count(idReservation) as count from reservation where fromDate <= :date and toDate >= :date  and site_idSite = :siteID
                                                        echo($resresult['count']);
                                                        echo('</td>');
                                                        $freeSpaces = $row['spaces'] - $resresult['count']; //get free spaces by subtract current reservations
                                                        echo('<td>');
                                                        echo($freeSpaces);
                                                        echo('</td>');
                                                        echo('</tr>');
                                                    } // for ($i=0;$i<12;$i++)
                                                echo('</tbody>');
                                            echo('</table>'); //table for overview of the next 12 months
                                        echo('</div>'); //<div class="card-body">
                                    echo('</div>'); //<div id="overview'.$row['idSite'].'" class="collapse" aria-labelledby="heading'.$row['idSite'].'" data-parent="#overview">
							    echo('</div>'); //<div class="card">
							} //add extendable card for each site
							
							?>
					</div> <!-- overview over all reservations by site -->
					<br/>
					<h3>Details</h3>
					<input id=filterInput type="text" placeholder="Suchen..">
					<table class="table table-hover table-striped text-center"> <!-- table with detailed reservation informations -->
                        <thead>
                            <tr>
                                <th><p>Reservationsnummer</p></th>
                                <th><p>Anbieter</p></th>
                                <th><p>Standort</p></th>
                                <th><p>von</p></th>
                                <th><p>bis</p></th>
                                <th><p>Probe</p></th>
                                <th><p>Rechnung gestellt</p></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id='filterTable'>
                            <?php
                            $stmt = $dbo -> prepare("SELECT bp.name as 'boothprovider' , s.name as 'site', r.fromDate as 'fromdate', r.toDate as 'todate', r.trail as 'trail', r.paid as 'paid', r.idReservation as 'idReservation'  FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite");
                            $stmt -> execute();
                            $result = $stmt -> fetchAll(); //SELECT bp.name as 'boothprovider' , s.name as 'site', r.fromDate as 'fromdate', r.toDate as 'todate', r.trail as 'trail', r.paid as 'paid', r.idReservation as 'idReservation'  FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite
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
                                if ($row['trail'] == 1){ //translate db entries to german
                                    echo('ja');
                                } else {
                                    echo('nein');
                                }
                                echo('</td>');
                                echo('<td>');
                                if ($row['paid'] == 1){ //translate db entries to german
                                    echo('ja');
                                } else {
                                    echo('nein');
                                }
                                echo('</td>');
                                echo('<td>');
                                echo('</td>');
                                echo('<td>');
                                if ($row['paid'] == 0){
                                    echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#billTo'.$row['idReservation'].'">Rechnung stellen</button>'); //add button to make bill
                                }
                                echo('</td>');
                                echo('</tr>');
                            }
                                
                        echo('</tbody>');
                    echo('</table>'); //table with detailed reservation informations
                    $stmt = $dbo -> prepare("SELECT idReservation from reservation");
                    $stmt -> execute();
                    $result = $stmt -> fetchAll(); //SELECT idReservation from reservation
                    foreach ($result as $row){ //make modal for each reservation
                        $billStmt = $dbo -> prepare ("SELECT r.idReservation as 'idReservation', r.fromDate as 'fromDate', r.toDate as 'toDate', s.name as 'site', bp.name as 'name', a.address as 'address', a.plz as 'plz', a.city as 'city', a.email as 'email' from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join address a on a.idAddress = bp.billing join site s on s.idSite = r.site_idSite where idReservation = :idReservation");
                        $billStmt -> execute(array('idReservation' => $row['idReservation']));
                        $billRow = $billStmt -> fetch(); //SELECT r.idReservation as 'idReservation', r.fromDate as 'fromDate', r.toDate as 'toDate', s.name as 'site', bp.name as 'name', a.address as 'address', a.plz as 'plz', a.city as 'city', a.email as 'email' from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join address a on a.idAddress = bp.billing join site s on s.idSite = r.site_idSite where idReservation = :idReservation
                        $fromDate=date_create($billRow['fromDate']);
                        $toDate=date_create($billRow['toDate']);
                        $diff=date_diff($fromDate,$toDate);
                        $billingperiod = $diff->format("%m")+1;
                        echo('<div class="modal fade" id="billTo'.$row['idReservation'].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">');
                            echo('<div class="modal-dialog modal-notify modal-success modal-fluid modal-dialog-centered" role="document">');
                                echo('<div class="modal-content">');
                                    echo('<div class="modal-header">');
                                        echo('<b>Rechnung an '.$billRow['name'].' stellen</b>');
                                    echo('</div>'); //<div class="modal-header">
                                    echo('<form method="POST" action="./billing.php">'); //form for adding a bill
                                        echo('<div class="modal-body">');
                                            echo('<table class="table">');
                                                echo('<tbody>');
                                                    echo('<tr>');
                                                        echo('<td>Reservationsnummer</td>');
                                                        echo('<td>'.$billRow['idReservation'].'</td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>Standort</td>');
                                                        echo('<td>'.$billRow['site'].'</td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>Dauer in Monaten</td>');
                                                        echo('<td>'.$billingperiod.'</td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>Name</td>');
                                                        echo('<td>'.$billRow['name'].'</td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>Adresse</td>');
                                                        echo('<td>'.$billRow['address'].'<br/'.$billRow['plz'].'&nbsp;'.$billRow['city'].'</td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>E-Mail</td>');
                                                        echo('<td>'.$billRow['email'].'</td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>Zahlungskonditionen in Tagen</td>');
                                                        echo('<td><input type="number" class="form-control" name="billingCondition" id="billingCondition" value="30"/></td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>Rechnungsdatum</td>');
                                                        echo('<td><input type="date" name="billingDate" id="billingDate" value="'.date("Y-m-d").'"/></td>');
                                                    echo('</tr>');
                                                echo('</tbody>');
                                            echo('</table>');
                                        echo('</div>'); // <div class="modal-body">
                                        echo('<div class="modal-footer justify-content-center">');
                                            echo('<input type hidden name="billingChange" id="billingChange" value="new"/>');
                                            echo('<input type hidden name="idReservation" id="idReservation" value="'.$billRow['idReservation'].'"/>');
                                            echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                            echo('<button type="submit" class="btn btn-primary">Rechnung stellen</button>');
                                        echo('</div>'); //<div class="modal-footer justify-content-center">
                                    echo('</form>'); //form for adding a bill
                                echo('</div>'); // <div class="modal-content">
                            echo('</div>'); // <div class="modal-dialog modal-notify modal-success modal-fluid modal-dialog-centered" role="document">
                        echo('</div>'); // <div class="modal fade" id="billTo'.$row['idReservation'].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    } //make modal for each reservation
                    ?>
				</div> <!-- main body for website -->
				<div class="col m-1">
				</div> <!-- <div class="col m-1"> -->
			</div> <!-- <div class="row"> -->
		</div> <!-- <div class="container-fluid"> -->
	</body>
</html>

