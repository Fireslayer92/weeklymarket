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
    <h2>Sites</h2>
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
    <table>
        <tr>
            <th>Name</th>
            <th>Anzahl Pl&auml;tze</th>
            <th>IBAN</th>
            <th>Lieferadresse</th>
            <th>Lieferung E-Mail</th>
            <th>Lieferung tel</th>
            <th>Rechnungsadresse</th>
            <th>Rechnung E-Mail</th>
            <th>Rechnung tel</th>
            <th>Korrespondenzadresse</th>
            <th>Korrespondenz E-Mail</th>
            <th>Korrespondenz tel</th>
        </tr>
        <?php
            $dbo = createDbConnection();
            $stmt = $dbo -> prepare("SELECT s.name as 'name', s.spaces as 'spaces', s.iban as 'iban', d.address as 'dAddress', d.plz as 'dPLZ', d.city as 'dCity', d.email as 'dEmail', d.phone as 'dPhone', b.address as 'bAddress', b.plz as 'bPLZ', b.city as 'bCity', b.email as 'bEmail', b.phone as 'bPhone', c.address as 'cAddress', c.plz as 'cPLZ', c.city as 'cCity', c.email as 'cEmail', c.phone as 'cPhone' from site s join address d on d.idAddress = s.delivery join address b on b.idAddress = s.billing join address c on c.idAddress = s.correspondence");
            $stmt -> execute();
            $result = $stmt -> fetchAll();
            foreach ($result as $row){
                echo('<tr>');
                echo('<td>');
                echo($row['name']);
                echo('</td>');
                echo('<td>');
                echo($row['spaces']);
                echo('</td>');
                echo('<td>');
                echo($row['iban']);
                echo('</td>');
                echo('<td>');
                echo($row['dAddress']."<br/>".$row['dPLZ']." ".$row['dCity']);
                echo('</td>');
                echo('<td>');
                echo($row['dEmail']);
                echo('</td>');
                echo('<td>');
                echo($row['dPhone']);
                echo('</td>');
                echo('<td>');
                echo($row['bAddress']."<br/>".$row['bPLZ']." ".$row['dCity']);
                echo('</td>');
                echo('<td>');
                echo($row['bEmail']);
                echo('</td>');
                echo('<td>');
                echo($row['bPhone']);
                echo('</td>');
                echo('<td>');
                echo($row['cAddress']."<br/>".$row['cPLZ']." ".$row['cCity']);
                echo('</td>');
                echo('<td>');
                echo($row['cEmail']);
                echo('</td>');
                echo('<td>');
                echo($row['cPhone']);
                echo('</td>');
                echo('</tr>');
            }
        ?>
    </table>
</body>
</html>