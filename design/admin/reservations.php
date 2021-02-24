

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
        <script>
            $(function() { 
                $(".providerSelect").change(function(){ 
                    var element = $(this).find('option:selected'); 
                    var status = element.attr("status"); 

                    if (status == 'trial'){
                    $( '.trial' ).show();
                    $( '.approved').hide();
                    } else {
                    $( '.trial' ).hide();
                    $( '.approved').show();
                    }
                }).trigger('change'); 
            });
        </script>
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
            $errt = '';
            if (isset($_POST['reservation']) && isset($_SERVER['REQUEST_URI'])){
                     
                //new date
                $query_date = $_POST['Datefrom'];
                $date = new DateTime($query_date);
                $resrduration = $_POST['flexRadioDefault'];
                
                
                

                //First day of month
                $date->modify('first day of this month');
                $firstday= $date->format('Y-m-d');
                
                //SQL Select reservation
                $resstmt1 = $dbo -> prepare("SELECT bp.name as 'boothprovider' , bp.qCheck as 'qCheck', s.idSite as 'idSite', r.fromDate as 'fromdate', r.toDate as 'todate', s.spaces as 'spaces', r.boothProvider_idProvider as 'idProvider' FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite WHERE bp.idProvider like :idProvider");
                $resstmt1 -> execute(array("idProvider"=>$_POST['idProvider']));
                $result1 = $resstmt1 -> fetchAll();
                //SQL Select reservation for user reservations
                $activerescount = $dbo -> prepare("SELECT count(r.idReservation) as count from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider where toDate >= date(now()) AND bp.idProvider like :idProvider");
                $activerescount -> execute(array("idProvider"=>$_POST['idProvider']));
                $resultactivecount = $activerescount -> fetch();
                //SQL Select reservation for three x 6 month Reservation
                $activerescount2 = $dbo -> prepare("SELECT * from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider where toDate >= date(now()) AND bp.idProvider like :idProvider");
                $activerescount2 -> execute();
                $resultactivecount1 = $activerescount2 -> fetchAll();
                //Period between fromDate to toDate
                $interval=0;

                foreach($resultactivecount1 as $rows)
                {
                     $fromDate=date_create($rows['fromDate']);
                     $toDate=date_create($rows['toDate']);
                     $diff=date_diff($fromDate,$toDate);
                     $interval = $diff->format("%m");
                     
                }
              
                //call for variable for 3x 6 month
                if($interval >6)
                {
                    $threeimport = true;
                }
                else
                {
                    $threeimport = false;
                }
                //first reservation import
                if(empty($result1))
                {
                   //SQL Select boothprovider
                    $provstmt = $dbo -> prepare("SELECT * FROM boothprovider WHERE idProvider like :idProvider");
                    $provstmt -> execute(array("idProvider"=>$_POST['idProvider']));
                    $provresult = $provstmt -> fetchAll();
                    foreach ($provresult as $row3)
                    {
                        if ($_POST['flexRadioDefault'] == 2){
                            $trail=1;
                        } else{
                            $trail=0;
                        }
                            //Last day of month
                            $query_date2 = $_POST['Datefrom'];
                            $date2 = new DateTime($query_date2);
                            $date2->modify('last day of '.$_POST['flexRadioDefault'].' month');
                            $lastday= $date2->format('Y-m-d');
                        //Variable idProvider
                        $idprov = $row3['idProvider'];
                    }
                        //SQL SELECT site
                        $spaces =$dbo -> prepare("SELECT spaces FROM site where idSite like :idSite");
                        $spaces -> execute(array('idSite' => $_POST['idSite']));
                        $resultspaces = $spaces -> fetch();
                        //SQL Count reservation
                        $resstmt = $dbo -> prepare("select count(idReservation) as count from reservation where fromDate <= :datefrom and toDate >= :dateto  and site_idSite = :siteID");
                        $resstmt -> execute(array('datefrom' => $firstday, 'dateto' => $lastday,'siteID' => $_POST['idSite']));
                        $resresult = $resstmt -> fetch();
                        $freeSpaces1 = $resultspaces['spaces'] - $resresult['count'];
                        
                        //site spaces free
                        if($freeSpaces1 > 0)
                        { 
                            //SQL INSERT reservation
                            $insert = $dbo -> prepare ("INSERT INTO reservation (boothProvider_idProvider, site_idSite, fromDate, toDate, trail, paid) VALUES (:boothProvider_idProvider, :site_idSite, :fromDate, :toDate, :trail,'0')");
                            $insert -> execute(array( 'boothProvider_idProvider' => $idprov, 'site_idSite' => $_POST['idSite'], 'fromDate' =>   $firstday, 'toDate' => $lastday, 'trail' => $trail));
                            //insert successful
                            if($insert== true) {
                                //back to reservation
                                header("Location: ../admin/reservations.php");
                            }
                            else
                            {
                                //give error to errorhandling
                                $errt .= 'Bei der Reservation ist etwas schiefgelaufen, versuchen Sie es erneut.';
                            }
                        }
                        else
                        {
                                //give error to errorhandling
                                $errt .= 'Leider sind an zu ihrem gew&auml;hlten Zeitpunkt keine Pl&auml;tze mehr verf&uuml;gbar';
                        }             
                }
               //loop for max reservations
                elseif ($_POST['flexRadioDefault']==5 && $resultactivecount['count'] < 3 && $threeimport == false || $_POST['flexRadioDefault']==12 && $resultactivecount['count'] < 1 || $_POST['flexRadioDefault']==2 && $resultactivecount['count'] < 1)
                {
                    foreach ($result1 as $row2)
                    {
                        //Qualiti check
                        if($row2['qCheck']==0)
                        {
                            $trail=1;
                            //Last day of month
                            $query_date2 = $_POST['Datefrom'];
                            $date2 = new DateTime($query_date2);
                            $date2->modify('last day of 1 month');
                            $lastday= $date2->format('Y-m-d');         
                        }
                        else
                        {
                            $trail=0;
                            //Last day of month
                            $query_date2 = $_POST['Datefrom'];
                            $date2 = new DateTime($query_date2);
                            $date2->modify('last day of '.$_POST['flexRadioDefault'].' month');
                            $lastday= $date2->format('Y-m-d');
                        }
                                //SQL SELECT count reservations
                                $idprov = $row2['idProvider'];
                                $resstmt = $dbo -> prepare("select count(idReservation) as count from reservation where fromDate <= :datefrom and toDate >= :dateto  and site_idSite = :siteID");
                                $resstmt -> execute(array('datefrom' => $firstday, 'dateto' => $lastday,'siteID' => $_POST['idSite']));
                                $resresult = $resstmt -> fetch();
                                $freeSpaces = $row2['spaces'] - $resresult['count'];
                        
                        //free spaces site        
                        if($freeSpaces > 0)
                        { 
                            //insert reservation
                            $insert = $dbo -> prepare ("INSERT INTO reservation (boothProvider_idProvider, site_idSite, fromDate, toDate, trail, paid) VALUES (:boothProvider_idProvider, :site_idSite, :fromDate, :toDate, :trail,'0')");
                            $insert -> execute(array( 'boothProvider_idProvider' => $idprov, 'site_idSite' => $_POST['idSite'], 'fromDate' =>   $firstday, 'toDate' => $lastday, 'trail' => $trail));
                            if($insert== true) {
                                //give error to errorhandling
                                header("Location: ../admin/reservations.php");
                            }
                            else
                            {   
                                //give error to errorhandling
                                $errt .= 'Bei der Reservation ist etwas schiefgelaufen, versuchen Sie es erneut.';
                            } 
                        }
                        else
                        {
                                //give error to errorhandling
                                $errt .= 'Leider sind an zu ihrem gew&auml;hlten Zeitpunkt keine Pl&auml;tze mehr verf&uuml;gbar';
                        }       
                    }
                    
                }
                else
                {
                                //back to reservation
                                $errt .= 'Sie haben bereits zu viele aktive Abos';
                }
            }





			?>
	</head>
	<body>
        <?php
            include '../includes/errorhandling.php'; //include errorhandling
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
                                        echo('<button class="btn btn-primary" type="submit" data-toggle="modal" data-target="#reservation_prov'.$row['idSite'].'">Standplatz Reservieren</button>');
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
                            
                            // Modal reservation
                                //SQL SELECT site
                                $idstmt = $dbo -> prepare ("SELECT idSite from site");
                                $idstmt -> execute();
                                $idresult = $idstmt -> fetchAll();
                                foreach ($idresult as $idrow){
                                    //SQL SELECT site
                                    $prov2stmt = $dbo -> prepare ("SELECT * from site where idSite = :idSite");
                                    $prov2stmt -> execute(array('idSite' => $idrow['idSite']));
                                    $prov2Row = $prov2stmt -> fetch();
                                        echo('<form method="POST" action="./reservations.php">');
                                        echo('<div class="modal fade" id="reservation_prov'.$idrow['idSite'].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">');
                                        echo('<div class="modal-dialog modal-notify modal-success modal-fluid modal-dialog-centered" role="document">');
                                        echo('<div class="modal-content">');
                                        echo('<div class="modal-header">');
                                        echo('<b>Standort reservieren</b>');
                                        echo('</div>');
                                        echo('<div class="modal-body">');
                                                echo('<table class="table">');
                                                echo('<tbody>');
                                                    
                                                    echo('<tr>');
                                                        echo('<td>Standort</td>');
                                                        echo('<td>'.$prov2Row['name'].'</td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>Start Monat</td>');
                                                        echo('<td><input type="date" name="Datefrom" id="datepicker" required="required"/></td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                    echo('<tr>');
                                                        echo('<td>Standanbieter</td>');
                                                        echo('<td><select class="providerSelect" name="idProvider">'); //add dropdown for provider selection
                                                            $provstmt = $dbo -> prepare("SELECT * FROM boothprovider WHERE qCheck = 1 and status != 'blocked'");
                                                            $provstmt -> execute();
                                                            $provresult = $provstmt -> fetchAll();
                                                            foreach ($provresult as $providerRow){
                                                                echo('<option value='.$providerRow['idProvider'].' status="'.$providerRow['status'].'">'.$providerRow['name'].'</option>');
                                                            }
                                                        echo('</select></td>'); //add dropdown for provider selection
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>Mietdauer</td>');
                                                            echo('<td><div class="approved" style="display:none;">');
                                                            echo('<div class="form-check">');
                                                            echo('<input class="form-check-input" type="radio" name="flexRadioDefault" id="six_month" value="5" checked>');
                                                            echo('<label class="form-check-label" for="flexRadioDefault1">');
                                                            echo(' &nbsp; 6 Monate');
                                                            echo('</label>');
                                                            echo(' </div>');
                                                            echo('<div class="form-check">');
                                                            echo('<input class="form-check-input" type="radio" name="flexRadioDefault" id="twelve_month" value="12">');
                                                            echo('<label class="form-check-label" for="flexRadioDefault2">');
                                                            echo('12 Monate');
                                                            echo('</label>');
                                                            echo('</div>');
                                                            echo('</div>');
                                                            echo('<div class="trial" class="form-check" style="display:none;">');
                                                            echo('<input class="form-check-input" type="radio" name="flexRadioDefault" id="trail" value="2" checked>');
                                                            echo('<label class="form-check-label" for="flexRadioDefault3">');
                                                            echo('2 Monate Probemiete');
                                                            echo('</label>');
                                                            echo(' </div></td>');                                                       
                                                echo('</tr>');
                                                echo('</tbody>');
                                                echo('</table>');
                                                echo('</div>');
                                                //button exit and reservation
                                                echo('<div class="modal-footer justify-content-center">');
                                                echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                                echo('<input type hidden name="idSite" id="idSite" value="'.$prov2Row['idSite'].'"/>');
                                                echo('<button type="submit" class="btn btn-primary" name="reservation">Marktplatz reservieren</button>');
                                                echo('</div>');
                                        echo('</div>');
                                        echo('</div>');
                                        echo('</div>');
                                        echo('</form>');
                                    
                                }

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

