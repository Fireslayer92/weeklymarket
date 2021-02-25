<?php
session_start();
?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- fontawesome icons -->
    <script src="https://kit.fontawesome.com/55e45674b5.js" crossorigin="anonymous"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
      <?php
            include 'includes/db.php';
            $dbo = createDbConnection();
      ?>
    <title>Wochenmarkt</title>
  </head>
<body>

<?php
        if (isset($_SESSION['idUser']))
        {
            $firstlogin = $dbo->prepare("SELECT count(user_idUser) as count FROM boothprovider WHERE user_idUser like :idUser");
            $firstlogin -> execute(array('idUser' => $_SESSION['idUser']));
            $result = $firstlogin->fetch();
            if($result['count'] == 0)
            {
              if ( 'provider' == $_SESSION['privilege'] )
              {
                header('Location: provider/quali.php');
              }
            }
            $firstloginsite = $dbo->prepare("SELECT count(user_idUser) FROM site WHERE user_idUser like :idUser");
            $firstloginsite -> execute(array('idUser' => $_SESSION['idUser']));
            $resultsite = $firstloginsite->fetch();
            if($resultsite == 0)
            {
              if ( 'site' == $_SESSION['privilege'] )
              {
                header('Location: site/site.php');
              }
            }
        
        }

    if (isset($_SESSION['message']))
    { 
      
      
     echo $_SESSION['message'];
      unset($_SESSION['message']);
    }
      
?>
  <!--Navigation-->
<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
  <div class="container-fluid">
    <h3 class="navbar-brand">Wochenmarkt</h3>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenue" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
      <div class="collapse navbar-collapse justify-content-end" id="navMenue">
        <ul class="navbar-nav ml-auto justify-content-end">
          <li class="nav-item active">
            <a class="nav-link" href="index.html">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Über uns</a>
          </li>
          <li class="nav-item">
            <?php
                if (isset($_SESSION['username'])) {
                  echo '<a href="includes/logout.php" class="nav-link">Log out</a>';
                }
                else {
                  echo '<a href="#login" class="nav-link" data-bs-toggle="modal" data-bs-target="#myLogin">Login</a>';
                }
              ?>
          </li>
        </ul> 
      </div>
  </div>
</nav>

<!-- Modal login -->

<div class="modal fade" id="myLogin" tabindex="-1" aria-labelledby="myLoginLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myLoginLabel">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="includes/login.php" method="post">
      <div class="modal-body">
          <div class="mb-3">
            <label for="username" class="col-form-label">Username/E-Mail:</label>
            <input type="email" class="form-control" id="username" name="username" required="required">
          </div>
          <div class="mb-3">
            <label for="passwort" class="col-form-label">Passwort:</label>
            <input type="password" class="form-control" id="password" name="password">
          </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" id="userRegistration" data-bs-toggle="modal" data-bs-target="#myRegistration" data-bs-dismiss="modal">Registrieren</button>
        <button class="btn btn-primary" id="login-submit" type="submit" name="login-submit">Login</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal registration -->

<div class="modal fade" id="myRegistration" tabindex="-1" aria-labelledby="myRegistrationLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myRegistrationLabel">Registrieren</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="includes/signup.php" method="post">
      <div class="modal-body">
          <div class="mb-3">
            <label for="username" class="col-form-label">Username/E-Mail:</label>
            <input type="email" class="form-control" id="username" name="username" required="required">
          </div>
          <div class="mb-3">
            <label for="passwort" class="col-form-label">Passwort:</label>
            <input type="password" class="form-control" id="password" name="password" required="required">
          </div>
          <div class="mb-3">
            <label for="password-repeat" class="col-form-label">Passwort wiederholen:</label>
            <input type="password" class="form-control" id="password-repeat" name="password-repeat" required="required">
          </div>
          <div class="mb-3">
            <label for="type" class="col-form-label">Profil Typ:</label>
            <select class="form-select" id="profile_typ" name="profile_typ" aria-label="Default select example" required="required">
              <option value="provider">Markstand Betreiber</option>
              <option value="side">Standort</option>
            </select>
          </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="userRegistration" type="submit" name="singup">Registrieren</button>
      </div>
      </form>
    </div>
  </div>
</div>



<!--Image Slider -->
<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
  <ol class="carousel-indicators">
    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
    <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="picture/market_2.png" class="d-block w-100" alt="market_1">
      <div class="carousel-caption" style="top: 10%; ">
        <h1 style="font-size: 700%">Wilkommen</h1>
        <h3 style="font-size: 300%">Auf dem Wochenmarkt</h3>
      </div>
    </div>
    <div class="carousel-item">
      <img src="picture/market_3.png" class="d-block w-100" alt="market_3">
      <div class="carousel-caption" style="top: 10%; ">
        <h1 style="font-size: 700%">Wilkommen</h1>
        <h3 style="font-size: 300%">Auf dem Wochenmarkt</h3>
      </div>
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
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





 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
 
</body>
</html>


