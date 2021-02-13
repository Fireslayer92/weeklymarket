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
    <h1>Welcome to the Admin-Interface</h1>
    <h2>Sites</h2>
    <?php
        include '../includes/nav.php';
    ?>
        <input id="filterInput" type="text" placeholder="Suchen..">
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Qualitätscheck erfolgt</th>
            <th>Rechnungsadresse</th>
            <th>Rechnung E-Mail</th>
            <th>Rechnung tel</th>
            <th>Korrespondenzadresse</th>
            <th>Korrespondenz E-Mail</th>
            <th>Korrespondenz tel</th>
        </tr>
        <thead>
        <tbody id='filterTable'>
        <?php
            $dbo = createDbConnection();
            $stmt = $dbo -> prepare("SELECT bp.name as 'name', bp.status as 'status', bp.qcheck as 'qCheck', b.address as 'bAddress', b.plz as 'bPLZ', b.city as 'bCity', b.email as 'bEmail', b.phone as 'bPhone', c.address as 'cAddress', c.plz as 'cPLZ', c.city as 'cCity', c.email as 'cEmail', c.phone as 'cPhone' from boothprovider bp join address b on b.idAddress = bp.billing join address c on c.idAddress = bp.correspondence");
            $stmt -> execute();
            $result = $stmt -> fetchAll();
            foreach ($result as $row){
                echo('<tr>');
                echo('<td>');
                echo($row['name']);
                echo('</td>');
                echo('<td>');
                switch ($row['status']){
                    case 'trial':
                        echo('Probe');
                        break;
                    case 'approved':
                        echo('Definitiv');
                        break;
                    case ('blocked'):
                        echo('Blockiert');
                        break;
                }
                echo('</td>');
                echo('<td>');
                if ($row['qCheck']==1){
                    echo('Ja');
                } else {
                    echo('Nein');
                }
                echo('</td>');
                echo('<td>');
                echo($row['bAddress']."<br/>".$row['bPLZ']." ".$row['bCity']);
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
        </tbody>
    </table>
</body>
</html>