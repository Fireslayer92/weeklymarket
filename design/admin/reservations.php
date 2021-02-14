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
        include '../includes/db.php';
        $dbo = createDbConnection();
        if (isset($_POST['reservationChange']) && isset($_SERVER['REQUEST_URI'])){
            switch ($_POST['reservationChange']) {
                case 'paid': 
                    $stmt = $dbo -> prepare("Update reservation set paid = 1 where idReservation = '".$_POST['idReservation']."';");
                    $stmt -> execute();
                    break;
                case 'notPaid':
                    $stmt = $dbo -> prepare("Update reservation set paid = 0 where idReservation = '".$_POST['idReservation']."';");
                    $stmt -> execute();
                    break;
            }
            header ('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }
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
      <?php
        $sitestmt = $dbo -> prepare("SELECT idSite, name, spaces from site;");
        $sitestmt -> execute();
        $siteresult = $sitestmt -> fetchAll();
        foreach ($siteresult as $row){
            echo('<div>');
            echo('<h5>'.$row['name'].'</h5>');
            echo('<table class="table table-hover table-striped text-center">');
            echo('<tr>');
            echo('<th>Monat</th>');
            echo('<th>Anzahl Reservationen</th>');
            echo('<th>Freie Pl&auml;tze</th>');
            echo('</tr>');
            for ($i=0;$i<12;$i++){
                $month = date('m',strtotime('first day of +'.$i.' month'));
                echo('<tr>');
                echo('<td>');
                //echo(strtotime('first day of +'.$i.' month'));
                if ($month != 03){
                    echo(strftime("%B %Y",strtotime('first day of +'.$i.' month')));
                } else {
                    echo("M&auml;rz ".strftime("%Y",strtotime('first day of +'.$i.' month')));
                }
                echo('</td>');
                echo('<td>');
                $resstmt = $dbo -> prepare("select count(idReservation) as count from reservation where fromDate < '2021-".$month."-01' and toDate > '2021-".$month."-01'  and site_idSite = ".$row['idSite']);
                $resstmt -> execute();
                $resresult = $resstmt -> fetch();
                echo($resresult['count']);
                echo('</td>');
                $freeSpaces = $row['spaces'] - $resresult['count'];
                echo('<td>');
                echo($freeSpaces);
                echo('</td>');
                echo('</tr>');
            }
            echo('</table>');
            echo('</div>');
        }

    ?>
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
                if ($row['paid'] == 0){
                    echo('<form method="POST">');
                    echo('<input type="hidden" name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/>');
                    echo('<input type="hidden" name="reservationChange" id="reservationChange" value="paid"/>');
                    echo('<button type="submit">Rechnung bezahlt</button>');
                    echo('</form>');
                } else{
                    echo('<form method="POST">');
                    echo('<input type="hidden" name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/>');
                    echo('<input type="hidden" name="reservationChange" id="reservationChange" value="notPaid"/>');
                    echo('<button type="submit">Rechnung nicht bezahlt</button>');
                    echo('</form>');
                }
                echo('</td>');
                echo('<td>');
                if ($row['paid'] == 0){
                    echo('<form method="POST" action="./sendbill.php">');
                    echo('<input type="hidden" name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/>');
                    echo('<input type="hidden" name="sendbill" id="sendbill" value="1"/>');
                    echo('<button type="submit">Rechnung stellen</button>');
                    echo('</form>');
                }
                echo('</td>');
                echo('</tr>');
            }
        ?>
        </tbody>
    </table>
      </div>
      <div class="col m-1">
      </div>
   </div>
 </div>

    
    
   
    
    
</body>
</html>