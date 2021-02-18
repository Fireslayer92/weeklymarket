

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
        <div class="container-fluid">
            <div class="row">
                <div class="col m-1">
                </div>
                <div class="col-10 m-1">
                    <h2>Reservations</h2>
                    <h3>&Uuml;bersicht</h3>
                    <div id="overview">
                        <?php
                            $sitestmt = $dbo -> prepare("SELECT idSite, name, spaces from site where user_idUser=:idUser;");
                            $sitestmt -> execute(array('idUser' => $_SESSION['idUser']));
                            $siteresult = $sitestmt -> fetchAll();
                            foreach ($siteresult as $row){
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
                        </tr>
                    </thead>
                    <tbody id='filterTable'>
                        <?php
                            $stmt = $dbo -> prepare("SELECT bp.name as 'boothprovider' , s.name as 'site', r.fromDate as 'fromdate', r.toDate as 'todate', r.trail as 'trail', r.paid as 'paid', r.idReservation as 'idReservation'  FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite where s.user_idUser = :idUser");
                            $stmt -> execute(array('idUser' => $_SESSION['idUser']));
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
                                echo('</tr>');
                            }
                            
                            echo('</tbody>');
                            echo('</table>');
                            ?>
                </div>
                <div class="col m-1">
                </div>
            </div>
        </div>
    </body>
</html>

