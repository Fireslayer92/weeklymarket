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
    ?>
</head>
<body>
    <h1>Welcome to the Admin-Interface</h1>
    <h2>Qualit&auml;tspr&uuml;fung</h2>
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
    <?php
        if (isset($_POST['add_approval'])){
                $idReservation = $_POST['idReservation'];
                $date = date('d.m.Y');
            }
        echo('<form method="POST" action="checks.php">');
        echo('<label for="idReservation">Reservationsnummer</label><br/>');
        echo('<input type="text" name="idReservation" id="idReservation" value="'.$idReservation.'"/><br/>');
        echo('<label for="date">Datum</label><br/>');
        echo('<input type="text" name="date" id="date" value="'.$date.'"/><br/>');
        echo('<label>Pr&uuml;fung erfolgreich</label><br/>');
        echo('<input type="radio" id="approved" name="approved" value="1">');
        echo('<label for="approved">Ja</label><br/>');
        echo('<input type="radio" id="notapproved" name="approved" value="1">');
        echo('<label for="notApproved">Nein</label><br/>');
        echo('<input type hidden name="approvalChange" id="approvalChange" value="new"/>');
        echo('</form>');
    ?>
</body>
</html>