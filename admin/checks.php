<!DOCTYPE HTML>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script
			  src="https://code.jquery.com/jquery-3.5.1.min.js"
			  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
			  crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <title>Weeklymarket</title>
    <meta charset="UTF-8">
    <?php
        setlocale (LC_ALL, '');
        include '../includes/db.php';
        $dbo = createDbConnection();
        if (isset($_POST['approvalChange']) && isset($_SERVER['REQUEST_URI'])){
            switch ($_POST['approvalChange']) {
                case 'new':
                    $approvalDate = date('Y-m-d H:i:s',strtotime($_POST['approvalDate']));
                    $insert = $dbo -> prepare ("INSERT INTO approval (date, status, reservation_idReservation) VALUES ('".$approvalDate."','".$_POST['approved']."','".$_POST['idReservation']."')");
                    $insert -> execute();
                    header ('Location: ' . $_SERVER['REQUEST_URI']);
                    break;
                
                case 'approved':
                    $update = $dbo -> prepare ("UPDATE boothprovider set status = 'approved' where idProvider = '".$_POST['idProvider']."'");
                    $update -> execute();
                    header ('Location: ' . $_SERVER['REQUEST_URI']);
                    break;
                    
                default:
                    header ('Location: ' . $_SERVER['REQUEST_URI']);
                    break;
            }
            
        } 
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
    <table>
        <tr>
            <th>Reservationsnummer</th>
            <th>Anbieter</th>
            <th>Standort</th>
            <th>von</th>
            <th>bis</th>
            <th>Anzahl durchgef&uuml;hrter Stichproben</th>
            <th>Anzahl Stichproben i.O.</th>
            <th>Qualit&auml;tscheck durchgef&uuml;hrt</th>
        </tr>
        <?php
            $stmt = $dbo -> prepare("SELECT bp.idProvider as 'idProvider', bp.name as 'boothprovider', bp.qCheck as 'qCheck' , s.name as 'site', r.fromDate as 'fromdate', r.toDate as 'todate', r.trail as 'trail', r.paid as 'paid', r.idReservation as 'idReservation', (select count(idApproval) from approval where reservation_idReservation = r.idReservation) as approval, (select count(idApproval) from approval where reservation_idReservation = r.idReservation and status = 1) as approved FROM reservation r join boothProvider bp on bp.idProvider = r.boothProvider_idProvider join site s on s.idSite = r.site_idSite where bp.status = 'trial'");
            $stmt -> execute();
            $result = $stmt -> fetchAll();
            foreach ($result as $row){
                echo('<tr>');
                echo('<td>');
                echo($row['idReservation']);
                echo('</td>');
                echo('<td>');
                echo($row['boothprovider']);
                echo('</td>');
                echo('<td>');
                echo($row['site']);
                echo('</td>');
                echo('<td>');
                echo(date('d.m.Y',strtotime($row['fromdate'])));
                echo('</td>');
                echo('<td>');
                echo(date('d.m.Y',strtotime($row['todate'])));
                echo('</td>');
                echo('<td>');
                echo($row['approval']);
                echo('</td>');
                echo('<td>');
                echo($row['approved']);
                echo('</td>');
                echo('<td>');
                if ($row['qCheck']==1){
                    echo('Ja');
                } else {
                    echo('Nein');
                }
                echo('</td>');
                echo('<td>');
                if ($row['approved']!=$row['approval']){
                    echo('<form method="POST">');
                    echo('<input type="hidden" name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/>');
                    echo('<input type="hidden" name="approval_failed" id="approval_failed" value="1"/>');
                    echo('<button type="submit">Konto sperren</button>');
                    echo('</form>');
                } elseif ($row['approved']<3){
                    echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addApproval'.$row['idReservation'].'">Pr&uuml;fung hinzuf&uuml;gen</button>');
                } elseif ($row['approved']==3&&$row['qCheck']==1){
                    echo('<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#approved'.$row['idProvider'].'">Account freischalten</button>');
                }
                echo('</td>');
                echo('<div class="modal fade" id="addApproval'.$row['idReservation'].'" tabindex="-1" role="dialog">');
                    echo('<div class="modal-dialog" role="document">');
                        echo('<div class="modal-content">');
                            echo('<div class="modal-header">');
                                echo('<label><b>Pr&uuml;fung f&uuml;r '.$row['boothprovider'].' hinzuf&uuml;gen</b></label><br/>');
                            echo('</div>');
                            echo('<div class="modal-body">');
                                echo('<form method="POST" action="checks.php">');
                                    echo('<label for="approvalDate">Datum</label><br/>');
                                    echo('<input type="text" name="approvalDate" id="approvalDate" value="'.date('d.m.Y').'"/><br/>');
                                    echo('<label>Pr&uuml;fung erfolgreich</label><br/>');
                                    echo('<input type="radio" id="approved" name="approved" value="1">');
                                    echo('<label for="approved">Ja</label><br/>');
                                    echo('<input type="radio" id="notapproved" name="approved" value="0">');
                                    echo('<label for="notApproved">Nein</label><br/>');
                                    echo('<input type hidden name="idReservation" id="idReservation" value="'.$row['idReservation'].'"/><br/>');
                                    echo('<input type hidden name="approvalChange" id="approvalChange" value="new"/>');
                                    echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>');
                                    echo('<button type="submit" class="btn btn-primary">Pr&uuml;fung speichern</button>');
                                echo('</form>');
                            echo('</div>');
                        echo('</div>');
                    echo('</div>');
                echo('</div>');
                echo('<div class="modal fade" id="approved'.$row['idProvider'].'" tabindex="-1" role="dialog">');
                echo('<div class="modal-dialog" role="document">');
                    echo('<div class="modal-content">');
                        echo('<div class="modal-header">');
                            echo('<label><b>'.$row['boothprovider'].' freischalten</b></label><br/>');
                        echo('</div>');
                        echo('<div class="modal-body">');
                            echo('<form method="POST" action="checks.php">');
                                echo('<input type hidden name="idProvider" id="idProvider" value="'.$row['idProvider'].'"/><br/>');
                                echo('<input type hidden name="approvalChange" id="approvalChange" value="approved"/>');
                                echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                echo('<button type="submit" class="btn btn-primary">Freischalten</button>');
                            echo('</form>');
                        echo('</div>');
                    echo('</div>');
                echo('</div>');
            echo('</div>');

                echo('</tr>');
            }
        ?>
    </table>
</body>
</html>