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
        include '../includes/db.php';
    ?>
</head>
<body>
<?php
        include '../includes/nav.php';
    ?>
<h1>Welcome to the Admin-Interface</h1>
<h2>billing</h2>
<div class="container-fluid">
  <div class="row">
    <div class="col m-1">
    </div>
    <div class="col-10 m-1">
        <?php
            $dbo = createDbConnection();
            if (isset($_POST['billingChange']) && isset($_SERVER['REQUEST_URI'])){
                switch ($_POST['billingChange']) {
                    case 'new':
                        $billingDate = date('Y-m-d H:i:s',strtotime($_POST['billingDateInput']));
                        $insert = $dbo -> prepare ("INSERT INTO billing (billingDate, billingCondition, billingStatus, reservation_idReservation) VALUES ('".$billingDate."','".$_POST['billingConditionInput']."','Open','".$_POST['idReservationInput']."')");
                        $insert -> execute();
                        $update = $dbo -> prepare ("UPDATE reservation set paid = 1 where idReservation = ".$_POST['idReservationInput']);
                        $update -> execute();
                        break;
                    case 'paid':
                        $update = $dbo -> prepare ("UPDATE billing set billingStatus = 'paid' where idBilling = ".$_POST['idBilling']);
                        $update -> execute();
                        break;
                        
                    default:
                        break;
                }
                header ('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            } 
        ?>
        
        
        <a href="./sendbill.php" class="btn btn-primary">Rechnung stellen</a>
        <table class="table table-hover table-striped text-center">
            <thead>
            <tr>
                <th>Rechnungsdatum</th>
                <th>Zahlungsziel</th>
                <th>Status</th>
                <th>Reservationsnummer</th>
                <th>Standanbieter</th>
                <th>Standort</th>
            </tr>
            </thead>
            <tbody id='filterTable'>
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
            </tbody>
        </table>
    </div>
    <div class="col m-1">
    </div>
  </div>
</div>
</body>
</html>