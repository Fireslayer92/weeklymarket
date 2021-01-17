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
        if (isset($_POST['sendbill'])){
            $stmt = $dbo -> prepare ("SELECT r.idReservation as 'idReservation', r.fromDate as 'fromDate', r.toDate as 'toDate', s.name as 'site', bp.name as 'name', a.address as 'address', a.plz as 'plz', a.city as 'city', a.email as 'email' from reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join address a on a.idAddress = bp.billing join site s on s.idSite = r.site_idSite");
            $stmt -> execute();
            $result = $stmt -> fetch()
            $idReservation = $row['idReservation'];
            $fromDate=date_create($row['fromDate']);
            $toDate=date_create($row['toDate']);
            $diff=date_diff($fromDate,$toDate);
            $billingperiod = $diff->format("%m");
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
        }
    ?>
    <h1>Willkommen im Admin-Interface</h1>
    <h2>Rechnung stellen</h2>
    <form method type="POST" action="billing.php">

    </form>
</body>
</html>