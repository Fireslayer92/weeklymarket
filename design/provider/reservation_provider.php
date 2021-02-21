

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
              
              if (isset($_POST['reservation']) && isset($_SERVER['REQUEST_URI'])){
                $fromDate= $_POST['Datefrom'];
                $idUser = $_SESSION['idUser'];  

                $query_date = $_POST['Datefrom'];
                $date = new DateTime($query_date);
                //First day of month
                $date->modify('first day of this month');
                $firstday= $date->format('Y-m-d');

                
                
                
                
                //$idprov = 1;
                $provstmt = $dbo -> prepare("SELECT * FROM boothprovider WHERE user_idUser like $idUser");
                $provstmt -> execute();
                $provresult = $provstmt -> fetchAll();
                foreach ($provresult as $row2){
                if($row2['qCheck']=0)
                {
                    $trail=1;
                    
                
                    //Last day of month
                    $query_date2 = $_POST['Datefrom'];
                    $date2 = new DateTime($query_date2);
                    $date2->modify('last day of '.$_POST['flexRadioDefault'].' month');
                    $lastday= $date2->format('Y-m-d');
                }
                else
                {
                    $trail=0;
                    //Last day of month
                    $query_date2 = $_POST['Datefrom'];
                    $date2 = new DateTime($query_date2);
                    $date2->modify('last day of 1 month');
                    $lastday= $date2->format('Y-m-d');
                }
                $idprov = $row2['idProvider'];
                } 
                $insert = $dbo -> prepare ("INSERT INTO reservation (boothProvider_idProvider, site_idSite, fromDate, toDate, trail, paid) VALUES (:boothProvider_idProvider, :site_idSite, :fromDate, :toDate, :trail,'0')");
                $insert -> execute(array( 'boothProvider_idProvider' => $idprov, 'site_idSite' => $_POST['idSite'], 'fromDate' =>   $firstday, 'toDate' => $lastday, 'trail' => $trail));
                if($insert== true) {
                    header("Location: ../provider/reservation_provider.php?".$firstday."");
                    exit();
                }
                else
                {
                    header("Location: ../provider/reservation_provider.php?reservation=false");
                    exit();
                } 
            }
        
          
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
                            $sitestmt = $dbo -> prepare("SELECT idSite, name, spaces from site");
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
                                    echo('<td>');
                                    if ($freeSpaces != 0)
                                    {
                                        //echo('<form method="post" action="./reservation_provider.php">');
                                        echo('<button class="btn btn-primary" type="submit" data-toggle="modal" data-target="#reservation_prov'.$row['idSite'].'">Standplatz Reservieren</button>');
                                        //echo('</form>');
                                    }
                                    echo('</td>');
                                    echo('</tr>');
                                }
                                echo('</tbody>');
                                echo('</table>');
                                echo('</div>');
                                echo('</div>');
                                echo('</div>');
                             }


                                // Modal
                                $idstmt = $dbo -> prepare ("SELECT idSite from site");
                                $idstmt -> execute();
                                $idresult = $idstmt -> fetchAll();
                                foreach ($idresult as $idrow){
                                    $prov2stmt = $dbo -> prepare ("SELECT * from site where idSite = :idSite");
                                    $prov2stmt -> execute(array('idSite' => $idrow['idSite']));
                                    $prov2Row = $prov2stmt -> fetch();
                                                                            
                                    echo('<form method="POST" action="./reservation_provider.php">');
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
                                                    echo('<td><input type="month" name="Datefrom" id="Datefrom" required="required"/></td>');
                                                echo('</tr>');
                                                echo('<tr>');
                                                    echo('<td>Mietdauer</td>');
                                                    echo('<td><div class="form-check">');
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
                                                    echo('</div></td>');
                                                echo('</tr>');
                                            echo('</tbody>');
                                            echo('</table>');
                                
                                            echo('</div>');
                                            echo('<div class="modal-footer justify-content-center">');
                                            //echo('<input type hidden name="idReservation" id="idReservation" value="hallo"/>');
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
                            <th></th>
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
                                                  echo('<td><input type="date" name="reservationDate" id="reservationDate" value="'.date("Y-m-d").'"/></td>');
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

