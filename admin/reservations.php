<!DOCTYPE HTML>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Weeklymarket</title>
    <?php
        include '../includes/db.php';
    ?>
</head>
<body>
    <h1>Welcome to the Admin-Interface</h1>
    <h2>reservations</h2>
    <table>
        <tr>
            <th>Reservationsnummer</th>
            <th>Anbieter</th>
            <th>Standort</th>
            <th>von</th>
            <th>bis</th>
            <th>Probe</th>
            <th>bezahlt</th>
        </tr>
        <?php
            $dbo = createDbConnection();
            if (isset($_POST['paid'])){
                $stmt = $dbo -> prepare("Update reservation set paid = 1 where idReservation = '".$_POST['idReservation']."';");
                $stmt -> execute();
            } elseif(isset($_POST['notpaid'])){
                $stmt = $dbo -> prepare("Update reservation set paid = 0 where idReservation = '".$_POST['idReservation']."';");
                $stmt -> execute();
            }
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
                echo($row['fromdate']);
                echo('</td>');
                echo('<td>');
                echo($row['todate']);
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