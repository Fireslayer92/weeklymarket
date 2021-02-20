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
        $dbo = createDbConnection();
        if (isset($_POST['providerChange']) && isset($_SERVER['REQUEST_URI'])){
            switch ($_POST['providerChange']) {   

                case 'passed':
                    $update = $dbo -> prepare ("UPDATE boothprovider set qCheck = 1 where idProvider = :idProvider");
                    $update -> execute(array('idProvider' => $_POST['idProvider']));
                    break;
                
                case 'blocked':
                    $update = $dbo -> prepare ("UPDATE boothprovider set status = 'blocked' where idProvider = :idProvider");
                    $update -> execute(array('idProvider' => $_POST['idProvider']));
                    break;

                default:
                    break;
            }
            
        } 

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
            $stmt = $dbo -> prepare("SELECT bp.idProvider as 'idProvider', bp.name as 'name', bp.status as 'status', bp.qcheck as 'qCheck', b.address as 'bAddress', b.plz as 'bPLZ', b.city as 'bCity', b.email as 'bEmail', b.phone as 'bPhone', c.address as 'cAddress', c.plz as 'cPLZ', c.city as 'cCity', c.email as 'cEmail', c.phone as 'cPhone' from boothprovider bp join address b on b.idAddress = bp.billing join address c on c.idAddress = bp.correspondence");
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
                echo('<td>');
                if($row['qCheck'] == 0 && $row['status'] != 'blocked'){
                    echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#check'.$row['idProvider'].'">Qualit&auml;tscheck hinzuf&uuml;gen</button>');
                }
                echo('</td>');
                echo('<div class="modal fade" id="check'.$row['idProvider'].'" tabindex="-1" role="dialog">');
                    echo('<div class="modal-dialog" role="document">');
                        echo('<div class="modal-content">');
                            echo('<div class="modal-header">');
                                echo('<label><b>Qualitaetscheck f&uuml;r'.$row['name'].' hinzuf&uuml;gen</b></label><br/>');
                            echo('</div>');
                            echo('<div class="modal-body">');
                                echo('<label>Hat '.$row['name'].' die Pr&uuml;fung bestanden? <br/></label>');
                                echo('<form method="POST" action="provider.php">');
                                    echo('<input type hidden name="idProvider" id="idProvider" value="'.$row['idProvider'].'"/><br/>');
                                    echo('<div class="modal-footer">');
                                        echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                        echo('<button type="submit" name="providerChange" value="passed" class="btn btn-primary">Ja - freischalten</button>');
                                        echo('<button type="submit" name="providerChange" value="blocked" class="btn btn-danger">Nein - blockieren</button>');
                                        echo('</form>');
                                    echo('</div>');
                            echo('</div>');
                        echo('</div>');
                    echo('</div>');
                echo('</div>');
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