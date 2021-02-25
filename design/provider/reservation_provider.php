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
             if ( 'provider' != $_SESSION['privilege'] ) 
             {
                        // access denied
                        header('Location: ../index.php');
             }
            include '../includes/db.php';
            $dbo = createDbConnection();
             
            ?>
        <script>
            $(function() 
            {
                $( "#datepicker" ).datepicker({dateFormat: 'yy M'});
            });
        </script>
    </head>
    <body>
        <?php   
              //set errorhandler
              $errt = "";
              //SESSION to variabl
              $idUser = $_SESSION['idUser'];
              //reservation
              if (isset($_POST['reservation']) && isset($_SERVER['REQUEST_URI']))
                {
                     
                    //new date
                    $query_date = $_POST['Datefrom'];
                    $date = new DateTime($query_date);
                    $resrduration = $_POST['flexRadioDefault'];
                    
                    
                    

                    //First day of month
                    $date->modify('first day of this month');
                    $firstday= $date->format('Y-m-d');
                    
                    //SQL Select reservation
                    $resstmt1 = $dbo -> prepare("SELECT bp.name as 'boothprovider' , bp.status as 'status', s.idSite as 'idSite', r.fromDate as 'fromdate', r.toDate as 'todate', s.spaces as 'spaces', r.boothProvider_idProvider as 'idProvider' FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite WHERE bp.user_idUser like $idUser");
                    $resstmt1 -> execute();
                    $result1 = $resstmt1 -> fetchAll();
                    //SQL Select reservation for user reservations
                    $activerescount = $dbo -> prepare("SELECT count(r.idReservation) as count from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider where toDate >= date(now()) AND bp.user_idUser like $idUser");
                    $activerescount -> execute();
                    $resultactivecount = $activerescount -> fetch();
                    //SQL Select reservation for three x 6 month Reservation
                    $activerescount2 = $dbo -> prepare("SELECT * from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider where toDate >= date(now()) AND bp.user_idUser like $idUser");
                    $activerescount2 -> execute();
                    $resultactivecount1 = $activerescount2 -> fetchAll();
                    //Period between fromDate to toDate
                    $interval=0;
                    foreach($resultactivecount1 as $rows)
                    {
                         //$interval = date_diff(date_create($rows['fromDate']),date_create($rows['toDate']));
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
                        $provstmt = $dbo -> prepare("SELECT * FROM boothprovider WHERE user_idUser like $idUser");
                        $provstmt -> execute();
                        $provresult = $provstmt -> fetchAll();
                        foreach ($provresult as $row3)
                        {
                            if ($row3['qCheck'] == 1){
                                $qCheck = 1;
                            } else {
                                $qCheck = 0;
                            }
                            //QualitiCkeck User
                            if($row3['status']=='trial')
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
                            
                            if($qCheck == 1){
                                //site spaces free
                                if($freeSpaces1 > 0)
                                { 
                                    //SQL INSERT reservation
                                    $insert = $dbo -> prepare ("INSERT INTO reservation (boothProvider_idProvider, site_idSite, fromDate, toDate, trail, paid) VALUES (:boothProvider_idProvider, :site_idSite, :fromDate, :toDate, :trail,'0')");
                                    $insert -> execute(array( 'boothProvider_idProvider' => $idprov, 'site_idSite' => $_POST['idSite'], 'fromDate' =>   $firstday, 'toDate' => $lastday, 'trail' => $trail));
                                    //insert successful
                                    if($insert== true) {
                                        //back to provider_reservation
                                        header("Location: ../provider/reservation_provider.php?");
                                        exit();
                                    }
                                    else
                                    {
                                        //back to provider_reservation
                                        header("Location: ../provider/reservation_provider.php?reservation=false");
                                        exit();

                                    }
                                }
                                else
                                {
                                        //back to provider_reservation
                                        header("Location: ../provider/reservation_provider.php?reservation=keine_freien_pleatze4");
                                        exit();
                                }
                            } else{
                                $errt .= "qCheck nicht durchgef&uuml;hrt";
                            }             
                    }
                   //loop for max reservations
                    elseif ($_POST['flexRadioDefault']==6 && $resultactivecount['count'] < 3 && $threeimport == false|| $_POST['flexRadioDefault']==12 && $resultactivecount['count'] < 1 || $_POST['flexRadioDefault']==2 && $resultactivecount['count'] < 1)
                    {
                        foreach ($result1 as $row2)
                        {
                            //Qualiti check
                            if($row2['status']=='trial')
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
                                    //back to provider_reservation
                                    header("Location: ../provider/reservation_provider.php");
                                    exit();
                                }
                                else
                                {   
                                    //back to provider_reservation
                                    $errt .= 'Reservierung konnte nicht gespeichert werden.';
                                } 
                            }
                            else
                            {
                                    //back to provider_reservation
                                    $errt .= 'Reservierung konnte nicht gespeichert werden.';
                            }       
                        }
                        
                    }
                    else
                    {
                                    //back to provider_reservation
                                    $errt .= 'Zu viele aktive Reservationen.';
                                    
                    }
                }
          //nacbar include
          include '../includes/nav.php';
          include '../includes/errorhandling.php'; //include errormodal
        ?>
        
        <h2>Reservations</h2>

        
       <!--start grid-->
        <div class="container-fluid">
            <!--//first row-->
            <div class="row">
                <div class="col m-1">
                </div>
                <!-- main row center-->
                <div class="col-10 m-1">
                    <h3>&Uuml;bersicht</h3>
                    <div id="overview">
                        <?php
                            //SQL SELECT site
                            $sitestmt = $dbo -> prepare("SELECT idSite, name, spaces from site");
                            $sitestmt -> execute();
                            $siteresult = $sitestmt -> fetchAll();
                            foreach ($siteresult as $row){
                               // site output free spaces
                                echo('<div class="card">');
                                echo('<div class="card-header d-flex justify-content-between" id="heading'.$row['idSite'].'">');
                                echo('<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#overview'.$row['idSite'].'" aria-expanded="true" aria-controls="overview'.$row['idSite'].'">'.$row['name'].'</button>');
                                echo('<button class="btn btn-primary" type="submit" data-toggle="modal" data-target="#reservation_prov'.$row['idSite'].'">Standplatz Reservieren</button>');
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
                                //month output generator
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
                                    //SQL SELECT count reservations for free spaces
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
                                    $provstmt = $dbo -> prepare("SELECT * FROM boothprovider WHERE user_idUser like $idUser");
                                    $provstmt -> execute();
                                    $provresult = $provstmt -> fetchAll();
                                    foreach ($provresult as $row3)
                                    {
                                                                            
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
                                                        echo('<td><input type="date" name="Datefrom" id="datepicker" required="required"/></td>');
                                                    echo('</tr>');
                                                    echo('<tr>');
                                                        echo('<td>Mietdauer</td>');
                                                        //loop trail
                                                        if($row3['status']=='approved')
                                                        {
                                                            //radio button
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
                                                        }
                                                        else
                                                        {
                                                            echo('<td><div class="form-check">');
                                                            echo('<input class="form-check-input" type="radio" name="flexRadioDefault" id="trail" value="2" checked>');
                                                            echo('<label class="form-check-label" for="flexRadioDefault3">');
                                                            echo('2 Monate Probemiete');
                                                            echo('</label>');
                                                            echo(' </div></td>');
                                                        }                                                          
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
                                }
                             
                            ?>
                    </div>
                    <br/>
                    <!-- your reservation table-->
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
                        </tr>
                    </thead>
                    <tbody id='filterTable'>
                        <?php
                            //SQL SELECT reservation join boothProvider and site
                            $idUser = $_SESSION['idUser']; 
                            $stmt = $dbo -> prepare("SELECT bp.name as 'boothprovider' , s.name as 'site', r.fromDate as 'fromdate', r.toDate as 'todate', r.trail as 'trail', r.paid as 'paid', r.idReservation as 'idReservation'  FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite WHERE bp.user_idUser like $idUser");
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
                                echo('</tr>');
                            }
                            
                            echo('</tbody>');
                            echo('</table>');

                            
                            ?>
                </div>
                <!--last row-->
                <div class="col m-1">
                </div>
            </div>
        </div>
    </body>
</html>

