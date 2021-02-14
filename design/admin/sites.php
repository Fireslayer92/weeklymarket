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
    
    <h2>Sites</h2>
    
    <div class="container-fluid">
  <div class="row">
      <div class="col m-1">
        </div>
      <div class="col-10 m-1 ">
      <input id="filterInput" type="text" placeholder="Suchen..">
    <table class="table table-hover table-striped text-center">
        <thead>
        <tr>
            <th>Name</th>
            <th>Anzahl Pl&auml;tze</th>
            <th>IBAN</th>
            <th>Lieferadresse</th>
            <th>Lieferung E-Mail</th>
            <th>Lieferung tel</th>
            <th>Korrespondenzadresse</th>
            <th>Korrespondenz E-Mail</th>
            <th>Korrespondenz tel</th>
        </tr>
        </thead>
        <tbody id='filterTable'>
        <?php
            $dbo = createDbConnection();
            $stmt = $dbo -> prepare("SELECT s.name as 'name', s.spaces as 'spaces', s.iban as 'iban', d.address as 'dAddress', d.plz as 'dPLZ', d.city as 'dCity', d.email as 'dEmail', d.phone as 'dPhone', c.address as 'cAddress', c.plz as 'cPLZ', c.city as 'cCity', c.email as 'cEmail', c.phone as 'cPhone' from site s join address d on d.idAddress = s.delivery join address c on c.idAddress = s.correspondence");
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
      </div>
      <div class="col m-1">
      </div>
   </div>
 </div>

    
</body>
</html>