<!DOCTYPE HTML>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Weeklymarket</title>
    <meta charset="UTF-8">
    <?php
        setlocale (LC_ALL, '');
        include '../includes/db.php';
        $dbo = createDbConnection();
        if (isset($_POST['paid'])){
            $stmt = $dbo -> prepare("Update reservation set paid = 1 where idReservation = '".$_POST['idReservation']."';");
            $stmt -> execute();
            header ('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } elseif(isset($_POST['notpaid'])){
            $stmt = $dbo -> prepare("Update reservation set paid = 0 where idReservation = '".$_POST['idReservation']."';");
            $stmt -> execute();
            header ('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }
    ?>
</head>
<body>
    <h1>Welcome to the Admin-Interface</h1>
    <h2>reservations</h2>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="./index.php">Admin-Interface</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="./billing.php">Rechnungen</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="./reservations.php">Reservationen</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="./sites.php">Standorte</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="./checks.php">Pr&uuml;fungen</a>
                </li>
            </ul>
            </div>
        </div>
    </nav>
    <h3>&Uuml;bersicht</h3>
    <?php
        $sitestmt = $dbo -> prepare("SELECT idSite, name, spaces from site;");
        $sitestmt -> execute();
        $siteresult = $sitestmt -> fetchAll();
        foreach ($siteresult as $row){
            echo('<div>');
            echo('<h5>'.$row['name'].'</h5>');
            echo('<table>');
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
                    echo(strftime("%B",strtotime('first day of +'.$i.' month')));
                } else {
                    echo("M&auml;rz");
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
    <table>
        <tr>
            <th>Reservationsnummer</th>
            <th>Anbieter</th>
            <th>Standort</th>
            <th>von</th>
            <th>bis</th>
            <th>Probe</th>
            <th>Rechnung gestellt</th>
        </tr>
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
                    echo('<input type="hidden" name="paid" id="paid" value="1"/>');
                    echo('<button type="submit">Rechnung bezahlt</button>');
                    echo('</form>');
                } else{
                    echo('<form method="POST">');
                    echo('<input type="hidden" name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/>');
                    echo('<input type="hidden" name="notpaid" id="notpaid" value="1"/>');
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
    </table>
</body>
</html>