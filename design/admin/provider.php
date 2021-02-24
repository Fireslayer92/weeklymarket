

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
		<title>Wochenmarkt</title>
		<meta charset="UTF-8">
		<?php
			if ( 'admin' != $_SESSION['privilege'] ) { //check privileges
			    // access denied
			    header('Location: ../index.php');
			} //check privileges
			setlocale (LC_ALL, '');
			include '../includes/db.php';
			$dbo = createDbConnection();
			if (isset($_POST['providerChange']) && isset($_SERVER['REQUEST_URI'])){
			    switch ($_POST['providerChange']) {   
			
			        case 'passed': //set qCheck to passed
			            $update = $dbo -> prepare ("UPDATE boothprovider set qCheck = 1 where idProvider = :idProvider");
			            $update -> execute(array('idProvider' => $_POST['idProvider']));
			            break; //case passed
			        
			        case 'blocked': //block user for failing qCheck
			            $update = $dbo -> prepare ("UPDATE boothprovider set status = 'blocked' where idProvider = :idProvider");
			            $update -> execute(array('idProvider' => $_POST['idProvider']));
			            break; //case blocked
			
			        default:
			            break; //case default
			    }
			    
			} 
			
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
					<h2>Standanbieter</h2>
					<input id=filterInput type="text" placeholder="Suchen..">
					<table class="table table-hover table-striped text-center"> <!-- provider-Table -->
						<thead>
							<tr>
								<th><p>Name</p></th>
								<th><p>Status</p></th>
								<th><p>Qualitätscheck erfolgt</p></th>
								<th><p>Rechnungsadresse</p></th>
								<th><p>Rechnung E-Mail</p></th>
								<th><p>Rechnung tel</p></th>
								<th><p>Korrespondenzadresse</p></th>
								<th><p>Korrespondenz E-Mail</p></th>
								<th><p>Korrespondenz tel</p></th>
								<th></th>
							</tr>
						<thead>
						<tbody id='filterTable'>
							<?php
								$stmt = $dbo -> prepare("SELECT bp.idProvider as 'idProvider', bp.name as 'name', bp.status as 'status', bp.qcheck as 'qCheck', b.address as 'bAddress', b.plz as 'bPLZ', b.city as 'bCity', b.email as 'bEmail', b.phone as 'bPhone', c.address as 'cAddress', c.plz as 'cPLZ', c.city as 'cCity', c.email as 'cEmail', c.phone as 'cPhone' from boothprovider bp join address b on b.idAddress = bp.billing join address c on c.idAddress = bp.correspondence");
								$stmt -> execute();
								$result = $stmt -> fetchAll(); //SELECT bp.idProvider as 'idProvider', bp.name as 'name', bp.status as 'status', bp.qcheck as 'qCheck', b.address as 'bAddress', b.plz as 'bPLZ', b.city as 'bCity', b.email as 'bEmail', b.phone as 'bPhone', c.address as 'cAddress', c.plz as 'cPLZ', c.city as 'cCity', c.email as 'cEmail', c.phone as 'cPhone' from boothprovider bp join address b on b.idAddress = bp.billing join address c on c.idAddress = bp.correspondence
								foreach ($result as $row){
								    echo('<tr>');
								    echo('<td>');
								    echo($row['name']);
								    echo('</td>');
								    echo('<td>');
								    switch ($row['status']){ //translate db entries to german
								        case 'trial':
								            echo('Probe');
								            break;
								        case 'approved':
								            echo('Definitiv');
								            break;
								        case ('blocked'):
								            echo('Blockiert');
								            break;
								    }
								    echo('</td>');
								    echo('<td>');
								    if ($row['qCheck']==1){ //translate db entries to german
								        echo('Ja');
								    } else {
								        echo('Nein');
								    }
								    echo('</td>');
								    echo('<td>');
								    echo($row['bAddress']."<br/>".$row['bPLZ']." ".$row['bCity']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['bEmail']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['bPhone']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['cAddress']."<br/>".$row['cPLZ']." ".$row['cCity']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['cEmail']);
								    echo('</td>');
								    echo('<td>');
								    echo($row['cPhone']);
								    echo('</td>');
								    echo('<td>');
								    if($row['qCheck'] == 0 && $row['status'] != 'blocked'){
								        echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#check'.$row['idProvider'].'">Qualit&auml;tscheck hinzuf&uuml;gen</button>'); //button to add qCheck
								    }
								    echo('</td>');
								    echo('<div class="modal fade" id="check'.$row['idProvider'].'" tabindex="-1" role="dialog">'); //modal for adding qCheck (unlock trial on pass, blcok user on fail)
								        echo('<div class="modal-dialog" role="document">');
								            echo('<div class="modal-content">');
								                echo('<div class="modal-header">');
								                    echo('<label><b>Qualitaetscheck f&uuml;r'.$row['name'].' hinzuf&uuml;gen</b></label><br/>');
								                echo('</div>'); //<div class="modal-header">
                                                echo('<form method="POST" action="provider.php">'); //form for qCheck add
                                                    echo('<div class="modal-body">');
                                                        echo('<label>Hat '.$row['name'].' die Pr&uuml;fung bestanden? <br/></label>');
                                                        echo('<input type hidden name="idProvider" id="idProvider" value="'.$row['idProvider'].'"/><br/>');
                                                    echo('</div>'); //<div class="modal-body">
                                                    echo('<div class="modal-footer">');
                                                        echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                                        echo('<button type="submit" name="providerChange" value="passed" class="btn btn-primary">Ja - freischalten</button>');
                                                        echo('<button type="submit" name="providerChange" value="blocked" class="btn btn-danger">Nein - blockieren</button>');
                                                    echo('</div>'); //<div class="modal-footer">
                                                echo('</form>'); //form for qCheck add
								            echo('</div>'); //div class="modal-content">
								        echo('</div>'); //<div class="modal-dialog" role="document">
								    echo('</div>'); //modal for adding qCheck (unlock trial on pass, blcok user on fail)
								    echo('</tr>');
								}
								?>
						</tbody>
					</table> <!-- provider-Table -->
				</div> <!-- main body for website -->
				<div class="col m-1">
				</div> <!-- <div class="col m-1"> -->
			</div> <!-- <div class="row"> -->
		</div> <!-- <div class="container-fluid"> -->
	</body>
</html>

