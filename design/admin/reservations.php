

<?php
    session_start();
    ?>
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
            setlocale (LC_ALL, '');
            /* if ( 'site' != $_SESSION['privilege'] ) {
                        // access denied
                        header('Location: ../index.php');
                    }*/
            include '../includes/db.php';
            $dbo = createDbConnection();
            ?>
    </head>
    <body>
        <?php
            include '../includes/nav.php';
            ?>
        <h2>Reservations</h2>
        <div class="container-fluid">
            <div class="row">
                <div class="col m-1">
                </div>
                <div class="col-10 m-1">
                    <h3>&Uuml;bersicht</h3>
                    <div id="overview">
                        <?php
                            $sitestmt = $dbo -> prepare("SELECT idSite, name, spaces from site;");
                            $sitestmt -> execute();
                            $siteresult = $sitestmt -> fetchAll();
                            foreach ($siteresult as $row){
                                echo('<div class="card">');
                                echo('<div class="card-header" id="heading'.$row['idSite'].'">');
                                echo('<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#overview'.$row['idSite'].'" aria-expanded="true" aria-controls="overview'.$row['idSite'].'">'.$row['name'].'</button>');
                                echo('</div>');
                                echo('<div id="overview'.$row['idSite'].'" class="collapse" aria-labelledby="heading'.$row['idSite'].'" data-parent="#overview">');
                                echo('<div class="card-body">');
                                echo('<table class="table table-hover table-striped text-center">');
                                echo('<thead>');
                                echo('<tr>');
                                echo('<th>Monat</th>');
                                echo('<th>Anzahl Reservationen</th>');
                                echo('<th>Freie Pl&auml;tze</th>');
                                echo('</tr>');
                                echo('</thead>');
                                echo('<tbody>');
                                for ($i=0;$i<12;$i++){
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
                                    $resresult = $resstmt -> fetch();
                                    echo($resresult['count']);
                                    echo('</td>');
                                    $freeSpaces = $row['spaces'] - $resresult['count'];
                                    echo('<td>');
                                    echo($freeSpaces);
                                    echo('</td>');
                                    echo('</tr>');
                                }
                                echo('</tbody>');
                                echo('</table>');
                                echo('</div>');
                                echo('</div>');
                                echo('</div>');
                            }
                            
                            ?>
                    </div>
                    <br/>
                    <h3>Details</h3>
                    <input id="filterInput" type="text" placeholder="Suchen..">
                    <table class="table table-hover table-striped text-center">
                    <thead>
                        <tr>
                            <th>Reservationsnummer</th>
                            <th>Anbieter</th>
                            <th>Standort</th>
                            <th>von</th>
                            <th>bis</th>
                            <th>Probe</th>
                            <th>Rechnung gestellt</th>
                        </tr>
                    </thead>
                    <tbody id='filterTable'>
                        <?php
                            $stmt = $dbo -> prepare("SELECT bp.name as 'boothprovider' , s.name as 'site', r.fromDate as 'fromdate', r.toDate as 'todate', r.trail as 'trail', r.paid as 'paid', r.idReservation as 'idReservation'  FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite");
                            $stmt -> execute();
                            $result = $stmt -> fetchAll();
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
                                if ($row['trail'] == 1){
                                    echo('ja');
                                } else {
                                    echo('nein');
                                }
                                echo('</td>');
                                echo('<td>');
                                if ($row['paid'] == 1){
                                    echo('ja');
                                } else {
                                    echo('nein');
                                }
                                echo('</td>');
                                echo('<td>');
                                /*if ($row['paid'] == 0){
                                    echo('<form method="POST">');
                                    echo('<input type="hidden" name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/>');
                                    echo('<input type="hidden" name="reservationChange" id="reservationChange" value="paid"/>');
                                    echo('<button class="btn btn-primary" type="submit">Rechnung bezahlt</button>');
                                    echo('</form>');
                                } else{
                                    echo('<form method="POST">');
                                    echo('<input type="hidden" name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/>');
                                    echo('<input type="hidden" name="reservationChange" id="reservationChange" value="notPaid"/>');
                                    echo('<button class="btn btn-primary" type="submit">Rechnung nicht bezahlt</button>');
                                    echo('</form>');
                                }*/
                                echo('</td>');
                                echo('<td>');
                                if ($row['paid'] == 0){
                                    echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#billTo'.$row['idReservation'].'">Rechnung stellen</button>');
                                }
                                echo('</td>');
                                echo('</tr>');
                            }
                            
                            echo('</tbody>');
                            echo('</table>');
                            $stmt = $dbo -> prepare("SELECT idReservation from reservation");
                            $stmt -> execute();
                            $result = $stmt -> fetchAll();
                            foreach ($result as $row){
                            $billStmt = $dbo -> prepare ("SELECT r.idReservation as 'idReservation', r.fromDate as 'fromDate', r.toDate as 'toDate', s.name as 'site', bp.name as 'name', a.address as 'address', a.plz as 'plz', a.city as 'city', a.email as 'email' from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join address a on a.idAddress = bp.billing join site s on s.idSite = r.site_idSite where idReservation = :idReservation");
                            $billStmt -> execute(array('idReservation' => $row['idReservation']));
                            $billRow = $billStmt -> fetch();
                            $fromDate=date_create($billRow['fromDate']);
                            $toDate=date_create($billRow['toDate']);
                            $diff=date_diff($fromDate,$toDate);
                            $billingperiod = $diff->format("%m")+1;
                            echo('<form method="POST" action="./billing.php">');
                            echo('<div class="modal fade" id="billTo'.$row['idReservation'].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">');
                            echo('<div class="modal-dialog modal-notify modal-success modal-fluid modal-dialog-centered" role="document">');
                               echo('<div class="modal-content">');
                                  echo('<div class="modal-header">');
                                  echo('<b>Rechnung an '.$billRow['name'].' stellen</b>');
                                  echo('</div>');
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
                                                  echo('<td><input type="text" name="billingCondition" id="billingCondition" value="30"/></td>');
                                              echo('</tr>');
                                              echo('<tr>');
                                                  echo('<td>Rechnungsdatum</td>');
                                                  echo('<td><input type="date" name="billingDate" id="billingDate" value="'.date("Y-m-d").'"/></td>');
                                              echo('</tr>');
                                           echo('</tbody>');
                                        echo('</table>');
                            
                                  echo('</div>');
                                  echo('<div class="modal-footer justify-content-center">');
                                  echo('<input type hidden name="billingChange" id="billingChange" value="new"/>');
                                  echo('<input type hidden name="idReservation" id="idReservation" value="'.$billRow['idReservation'].'"/>');
                                  echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                  echo('<button type="submit" class="btn btn-primary">Rechnung stellen</button>');
                                  echo('</div>');
                               echo('</div>');
                            echo('</div>');
                            echo('</div>');
                            echo('</form>');
                            }
                            ?>
                </div>
                <div class="col m-1">
                </div>
            </div>
        </div>
    </body>
</html>

