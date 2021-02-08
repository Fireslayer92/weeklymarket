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
    <?php
        $dbo = createDbConnection();
        if (isset($_POST['billingChange']) && isset($_SERVER['REQUEST_URI'])){
            switch ($_POST['billingChange']) {
                case 'new':
                    $billingDate = date('Y-m-d H:i:s',strtotime($_POST['billingDateInput']));
                    $insert = $dbo -> prepare ("INSERT INTO billing (billingDate, billingCondition, billingStatus, reservation_idReservation) VALUES ('".$billingDate."','".$_POST['billingConditionInput']."','Open','".$_POST['idReservationInput']."')");
                    $insert -> execute();
                    header ('Location: ' . $_SERVER['REQUEST_URI']);
                    break;
                case 'paid':
                    $update = $dbo -> prepare ("UPDATE billing set billingStatus = 'paid' where idBilling = ".$_POST['idBilling']);
                    $update -> execute();
                    header ('Location: ' . $_SERVER['REQUEST_URI']);
                    break;
                    
                default:
                    header ('Location: ' . $_SERVER['REQUEST_URI']);
                    break;
            }
            
        } 
    ?>
    <h1>Welcome to the Admin-Interface</h1>
    <h2>billing</h2>
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
    <a href="./sendbill.php" class="btn btn-primary">Rechnung stellen</a>
    <table>
        <tr>
            <th>Rechnungsdatum</th>
            <th>Zahlungsziel</th>
            <th>Status</th>
            <th>Reservationsnummer</th>
            <th>Standanbieter</th>
            <th>Standort</th>
        </tr>
        <?php
            $stmt = $dbo -> prepare ("SELECT b.idBilling as 'idBilling', b.billingDate as 'billingDate', b.billingCondition as 'billingCondition', b.billingStatus as 'billingStatus', r.idReservation as 'idReservation', bp.name as 'boothProvider', s.name as 'site' from reservation r join billing b on b.reservation_idReservation = r.idReservation join site s on s.idSite = r.site_idSite join boothProvider bp on bp.idProvider = r.boothProvider_idProvider");
            $stmt -> execute();
            $result = $stmt -> fetchAll();
            foreach($result as $row){
                echo('<tr>');
                echo('<td>');
                echo(date('d.m.Y',strtotime($row['billingDate'])));
                echo('</td>');
                echo('<td>');
                echo($row['billingCondition']);
                echo('</td>');
                echo('<td>');
                echo($row['billingStatus']);
                echo('</td>');
                echo('<td>');
                echo($row['idReservation']);
                echo('</td>');
                echo('<td>');
                echo($row['boothProvider']);
                echo('</td>');
                echo('<td>');
                echo($row['site']);
                echo('</td>');
                echo('<td>');
                if($row['billingStatus'] != 'paid'){
                    echo('<form method="POST" action="./billing.php">');
                    echo('<input type="hidden" name="idBilling" id="idBilling" value="'.$row['idBilling'].'"/>');
                    echo('<input type="hidden" name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/>');
                    echo('<input type="hidden" name="billingChange" id="billingChange" value="paid"/>');
                    echo('<button type="submit">Rechnung bezahlt</button>');
                    echo('</form>');
                }
                echo('</td>');
                echo('</tr>');
                
            }
        ?>
</body>
</html>