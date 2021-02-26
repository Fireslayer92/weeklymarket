<?php
session_start();
?>
<!DOCTYPE html>
<html lang="de">
<head>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link href="/includes/stylesheet.css" rel="stylesheet">
        <script
			src="https://code.jquery.com/jquery-3.5.1.min.js"
			integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
			crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
		<script src="/includes/jquery.tablesort.min.js"></script>
        <script src="/includes/script.js"></script>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- fontawesome icons -->
    <script src="https://kit.fontawesome.com/55e45674b5.js" crossorigin="anonymous"></script>
      <?php
            include './includes/db.php';
            $dbo = createDbConnection();
      ?>
    <title>Wochenmarkt</title>
  </head>
<body>

<?php
      $errt = "";
        if (isset($_SESSION['idUser']))
        {
            $firstlogin = $dbo->prepare("SELECT count(user_idUser) as count FROM boothprovider WHERE user_idUser like :idUser");
            $firstlogin -> execute(array('idUser' => $_SESSION['idUser']));
            $result = $firstlogin->fetch();
            if($result['count'] == 0)
            {
              if ( 'provider' == $_SESSION['privilege'] )
              {
                header('Location: /provider/quali.php');
              }
            }
            $firstloginsite = $dbo->prepare("SELECT count(user_idUser) as count FROM site WHERE user_idUser like :idUser");
            $firstloginsite -> execute(array('idUser' => $_SESSION['idUser']));
            $resultsite = $firstloginsite->fetch();
            if($resultsite['count'] == 0)
            {
              if ( 'site' == $_SESSION['privilege'] )
              {
                header('Location: /site/site.php');
              }
            }
        
        }

    if (isset($_SESSION['message']))
    { 
     $errt = $_SESSION['message'];
      unset($_SESSION['message']);
    } elseif (isset($_SESSION['successMessage'])){
      $succ = $_SESSION['successMessage'];
      unset($_SESSION['successMessage']);
    }
      
?>
  <?php
      include './includes/errorhandling.php'; //include errorhandling
      include './includes/nav.php'; //include nav bar
      if (!empty($succ)){
        ?>
        <div id="success" class="modal fade" role="dialog"> <!-- Success handling -->
          <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
            <p class="text-success"><b>Erfolg</b></p>
            </div>
            <div class="modal-body">
              <?php echo($succ); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="window.location = window.location.href;">Schliessen</button>
            </div>
            </div>
  
          </div>
        </div> <!-- Success handling -->
        <?php
          }
          ?>



<!--Image Slider -->
<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="picture/market_2.png" class="d-block w-100" alt="market_1">
      <div class="carousel-caption" style="top: 10%; ">
        <h1 style="font-size: 700%">Willkommen</h1>
        <h3 style="font-size: 300%">Auf dem Wochenmarkt</h3>
      </div>
    </div>
    <div class="carousel-item">
      <img src="picture/market_3.png" class="d-block w-100" alt="market_3">
      <div class="carousel-caption" style="top: 10%; ">
        <h1 style="font-size: 700%">Willkommen</h1>
        <h3 style="font-size: 300%">Auf dem Wochenmarkt</h3>
      </div>
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span> 
  </a>
</div>

<!--Welcome section-->
<div class="container-fluid padding">
  <div class="row welcone text-center">
    <div class="col-12">
      <h1 class="display-4">Erstellt mit Liebe <span style="color: red;"><i class="fas fa-heart"></i></span></h1>
    </div>
    <div class="col-12">
      <p class="lead"> Der Wochenmarkt wurde von Marc Bannier & Fabian Schmid erstellt.</p>
    </div>
  </div>
  <hr class="my-4">
</div>

<!--Website Build-->
<div class="container-fluid padding">
    <div class="row row-cols-1 row-cols-sm-3 row-cols-md-6 text-center">
      <div class="">
      <span style="color: orange; font-size: 4em;"><i class="fab fa-html5"></i></span>
      <h3>HTML5</h3>
      <p>Erstellt mit der aktuellen HTML Version</p>
      </div>

      <div>
        <span style="color: blueviolet; font-size: 4em;"><i class="fab fa-bootstrap"></i></span>
        <h3>Bootstrap</h3>
        <p>Erstellt mit der aktuellen Bootstrap Version</p>
      </div>

      <div>
        <span style="color: blue; font-size: 4em;"><i class="fab fa-css3-alt fa-lg"></i></span>
        <h3>CSS</h3>
        <p>Erstellt mit der aktuellen CSS Version</p>
      </div>

      <div>
        <span style="color: lightblue; font-size: 4em;"><i class="fas fa-database"></i></span>
        <h3>MySQL</h3>
        <p>Erstellt mit der aktuellen MySQL Version</p>
      </div>

      <div>
        <span style="color: orange; font-size: 4em;"><i class="fab fa-php"></i></span>
        <h3>PHP</h3>
        <p>Erstellt mit der aktuellen PHP Version</p>
      </div>
 
      <div> 
        <span style="color: yellow; font-size: 4em;"><i class="fab fa-js"></i></span>
        <h3>JavaScript</h3>
        <p>Erstellt mit der aktuellen JavaScript Version</p>
      </div>

  </div>

    
      
    
  
</div>



<!--Footer
<footer class="footer footer-dark bg-dark">
  <div class="container-fluide padding">
    <div class="row text-center">
      <div class="col-md-4">
        <a style="color: white;">Wochenmarkt</a>
        
      </div>
  
    </div>
  
  
  </div>
</footer> -->

 
</body>
</html>


