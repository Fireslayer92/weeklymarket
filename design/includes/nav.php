<!--Navigation-->
<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
  <div class="container-fluid">  
  <h3 class="navbar-brand">Wochenmarkt</h3>  
      <div class="collapse navbar-collapse justify-content-end" id="navMenue">
        <ul class="navbar-nav ml-auto justify-content-end">
        <?php
        if(isset($_SESSION['idUser']))
        {
          if ( 'admin' == $_SESSION['privilege'] ) {
            
            echo ('<li class="nav-item dropdown">');
            echo ('<a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-toggle="dropdown" aria-expanded="false">');
            echo ('<span class="navbar-toggler-icon"></span> Menü');
            echo ('</a>');
            echo ('<ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">');
            echo ('<li><a class="dropdown-item" href="/admin/billing.php">Rechnungen</a></li>');
            echo ('<li><a class="dropdown-item" href="/admin/reservations.php">Reservationen</a></li>');
            echo ('<li><a class="dropdown-item" href="/admin/sites.php">Standorte</a></li>');
            echo ('<li><a class="dropdown-item" href="/admin/provider.php">Standanbieter</a></li>');
            echo ('<li><a class="dropdown-item" href="/admin/checks.php">Pr&uuml;fungen</a></li>');
            echo ('<li><a class="dropdown-item" href="/admin/admin_user.php">Benutzerverwaltung</a></li>');
            echo ('</ul>');
            echo ('</li>');
          } elseif ('provider' == $_SESSION['privilege']){
            echo ('<li class="nav-item dropdown">');
            echo ('<a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-toggle="dropdown" aria-expanded="false">');
            echo ('<span class="navbar-toggler-icon"></span> Menü');
            echo ('</a>');
            echo ('<ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">');
            echo ('<li><a class="dropdown-item" href="/provider/reservation_provider.php">Reservationen</a></li>');
            echo ('</ul>');
            echo ('</li>');
          } elseif ('site' == $_SESSION['privilege']){
            echo ('<li class="nav-item dropdown">');
            echo ('<a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink" role="button" data-toggle="dropdown" aria-expanded="false">');
            echo ('<span class="navbar-toggler-icon"></span> Menü');
            echo ('</a>');
            echo ('<ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDarkDropdownMenuLink">');
            echo ('<li><a class="dropdown-item" href="/site/reservations.php">Reservationen</a></li>');
            echo ('</ul>');
            echo ('</li>');
          }            
        } 
          
        ?>
          <li class="nav-item active">
            <a class="nav-link" href="../index.php">Home</a>
          </li>
          <li class="nav-item">
            <?php
                if(isset($_SESSION['id']))
                {
                  if ( 'registered' == $_SESSION['privilege'] ) {
                  echo '<a href="hoster.php" class="nav-link">Marktstand buchen</a>';
                  }
                }
                
              ?>
          </li>
          <li class="nav-item">
            <?php
                if (isset($_SESSION['username'])) {
                  echo '<a href="../includes/logout.php" class="nav-link">Log out</a>';
                }
                else {
                  echo '<a href="#login" class="nav-link" data-toggle="modal" data-target="#myLogin">Login</a>';
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
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
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
      <button type="button" class="btn btn-secondary" id="userRegistration" data-toggle="modal" data-target="#myRegistration" data-dismiss="modal">Registrieren</button>
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
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
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
              <option value="site">Standort</option>
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

<!-- <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js'></script> -->