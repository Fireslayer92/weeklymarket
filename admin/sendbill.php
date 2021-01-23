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
    <h1>Willkommen im Admin-Interface</h1>
    <h2>Rechnung stellen</h2>
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
            </ul>
            </div>
        </div>
    </nav>
    <?php
        $dbo = createDbConnection();
        if (isset($_POST['sendbill'])){
            $stmt = $dbo -> prepare ("SELECT r.idReservation as 'idReservation', r.fromDate as 'fromDate', r.toDate as 'toDate', s.name as 'site', bp.name as 'name', a.address as 'address', a.plz as 'plz', a.city as 'city', a.email as 'email' from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join address a on a.idAddress = bp.billing join site s on s.idSite = r.site_idSite");
            $stmt -> execute();
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
        echo('<label for="idReservationInput">Reservationsnummer</label><br>');
        echo('<input type="text" name="idReservationInput" id="idReservationInput" value="'.$idReservation.'"/><br/>');
        echo('<label for="siteInput">Standort</label><br>');
        echo('<input type="text" name="siteInput" id="siteInput" value="'.$site.'"/><br/>');
        echo('<label for="periodInput">Dauer in Monaten</label><br>');
        echo('<input type="text" name="periodInput" id="periodInput" value="'.$billingperiod.'"/><br/>');
        echo('<label for="nameInput">Name</label><br>');
        echo('<input type="text" name="nameInput" id="nameInput" value="'.$name.'"/><br/>');
        echo('<label for="addressInput">Adresse</label><br>');
        echo('<input type="text" name="addressInput" id="addressInput" value="'.$address.'"/><br/>');
        echo('<label for="plzInput">PLZ</label><br>');
        echo('<input type="text" name="plzInput" id="plzInput" value="'.$plz.'"/><br/>');
        echo('<label for="cityInput">Ort</label><br>');
        echo('<input type="text" name="cityInput" id="cityInput" value="'.$city.'"/><br/>');
        echo('<label for="emailInput">E-Mail</label><br>');
        echo('<input type="text" name="emailInput" id="emailInput" value="'.$email.'"/><br/>');
        echo('<label for="billingConditionInput">Zahlungskonditionen in Tagen</label><br>');
        echo('<input type="text" name="billingConditionInput" id="billingConditionInput" value="30"/><br/>');
        echo('<label for="billingDateInput">Rechnungsdatum</label><br>');
        echo('<input type="text" name="billingDateInput" id="billingDateInput" value="'.date("d.m.Y").'"/><br/>');
        echo('<input type hidden name="billingChange" id="billingChange" value="new"/>')
        ?>
        <button>Rechnung erstellen</button>
    </form>
</body>
</html>