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
        include '../includes/db.php';
    ?>
</head>
<body>
    <?php
        include '../includes/nav.php';
    ?>
    <h1>Willkommen im Admin-Interface</h1>
    <h2>Rechnung stellen</h2>
    
    <div class="container-fluid">
  <div class="row">
      <div class="col m-1">
        </div>
      <div class="col-10 m-1">
      <?php
        $dbo = createDbConnection();
        if (isset($_POST['sendbill'])){
            $stmt = $dbo -> prepare ("SELECT r.idReservation as 'idReservation', r.fromDate as 'fromDate', r.toDate as 'toDate', s.name as 'site', bp.name as 'name', a.address as 'address', a.plz as 'plz', a.city as 'city', a.email as 'email' from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join address a on a.idAddress = bp.billing join site s on s.idSite = r.site_idSite where idReservation = :idReservation");
            $stmt -> execute(array('idReservation' => $_POST['idReservation']));
            $row = $stmt -> fetch();
            $idReservation = $row['idReservation'];
            $fromDate=date_create($row['fromDate']);
            $toDate=date_create($row['toDate']);
            $diff=date_diff($fromDate,$toDate);
            $billingperiod = $diff->format("%m")+1;
            $site = $row['site'];
            $name = $row['name'];
            $address = $row['address'];
            $plz = $row['plz'];
            $city = $row['city'];
            $email = $row['email'];
        } else{
            $idReservation = "";
            $billingperiod = "";
            $site = "";
            $name = "";
            $address = "";
            $plz = "";
            $city = "";
            $email = "";
            echo ('<a href="./reservations.php" class="btn btn-primary">Reservation ausw&auml;hlen</a>');
        }
        echo('<form method="POST" action="billing.php">');
        echo('<label for="idReservation">Reservationsnummer</label><br/>');
        echo('<input type="text" name="idReservation" id="idReservation" value="'.$idReservation.'"/><br/>');
        echo('<label for="site">Standort</label><br/>');
        echo('<input type="text" name="site" id="site" value="'.$site.'"/><br/>');
        echo('<label for="period">Dauer in Monaten</label><br/>');
        echo('<input type="text" name="period" id="period" value="'.$billingperiod.'"/><br/>');
        echo('<label for="name">Name</label><br/>');
        echo('<input type="text" name="name" id="name" value="'.$name.'"/><br/>');
        echo('<label for="address">Adresse</label><br/>');
        echo('<input type="text" name="address" id="address" value="'.$address.'"/><br/>');
        echo('<label for="plz">PLZ</label><br/>');
        echo('<input type="text" name="plz" id="plz" value="'.$plz.'"/><br/>');
        echo('<label for="cityInput">Ort</label><br/>');
        echo('<input type="text" name="city" id="city" value="'.$city.'"/><br/>');
        echo('<label for="email">E-Mail</label><br/>');
        echo('<input type="text" name="email" id="email" value="'.$email.'"/><br/>');
        echo('<label for="billingCondition">Zahlungskonditionen in Tagen</label><br/>');
        echo('<input type="text" name="billingCondition" id="billingCondition" value="30"/><br/>');
        echo('<label for="billingDate">Rechnungsdatum</label><br/>');
        echo('<input type="date" name="billingDate" id="billingDate" value="'.date("Y-m-d").'"/><br/>');
        echo('<input type hidden name="billingChange" id="billingChange" value="new"/>')
        ?>
        <br>
        <button class="btn btn-primary">Rechnung erstellen</button>
    </form>
      </div>
      <div class="col m-1">
      </div>
   </div>
 </div>

    
</body>
</html>