<?php
session_start();
?>
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
    ?>
    <!-- fontawesome icons -->
    <script src="https://kit.fontawesome.com/55e45674b5.js" crossorigin="anonymous"></script>
    <script>
          function myFunction() {
          // Get the checkbox
          var checkBox = document.getElementById("myCheck");
          // Get the output text
          var text = document.getElementById("liaddress");

          // If the checkbox is checked, display the output text
          if (checkBox.checked == false){
            text.style.display = "block";
            $('.li_input').prop('required',true);
            
          } else {
            text.style.display = "none";
            $('.li_input').prop('required',false);
          }
        }
      </script>
</head>
<body>


    <?php
        

        if(isset($_SESSION['id']))
        {
          if ( 'new' != $_SESSION['privilege'] ) {
            // access denied
            //header('Location: ../index.php');
          }
    
        }
        ?>
        <?php
          //set errohandler
          $errt = "";
          //import site
          if (isset($_POST['import']) && isset($_SERVER['REQUEST_URI']))
          {
            $idUser=$_SESSION['idUser'];
            $sitestmt = $dbo->prepare("SELECT name FROM site WHERE name = :name");
            $sitestmt -> execute(array('name' => $_POST['nameSite']));
            $nameSite = $sitestmt->fetch();
            
            if($nameSite !== false) {
              $errt .= 'Standortname <b>'.$_POST['nameSite'].'</b> ist schon vergeben';
            }
            else
            {
                  //insert address
              $insert = $dbo -> prepare ("INSERT INTO address (address, plz, city, email, phone) VALUES (:address, :plz, :city, :email, :phone)");
              $insert -> execute(array( 'address' => $_POST['address'], 'plz' => $_POST['plz'], 'city' =>  $_POST['city'], 'email' => $_POST['email'], 'phone' => $_POST['phone']));
              
              if (empty($_POST['li']))
              {
                //insert address
                $insert2 = $dbo -> prepare ("INSERT INTO address (address, plz, city, email, phone) VALUES (:address, :plz, :city, :email, :phone)");
                $insert2 -> execute(array( 'address' => $_POST['liaddress'], 'plz' => $_POST['liplz'], 'city' =>  $_POST['licity'], 'email' => $_POST['liemail'], 'phone' => $_POST['liphone']));
              } 
              if(isset($_POST['li'])) 
              {
                  $stmt = $dbo -> prepare("SELECT idAddress FROM address where address like :address and email like :email and phone like :phone");
                  $stmt -> execute(array('address' => $_POST['address'], 'email' => $_POST['email'],'phone' => $_POST['phone']));
                  $result = $stmt -> fetch();
                    $insertsite = $dbo -> prepare ("INSERT INTO site (name, spaces, iban, delivery, correspondence, user_idUser) VALUES (:name, :spaces, :iban, :delivery, :correspondence, :user_idUser)");
                    $res = $insertsite -> execute(array( 'name' => $_POST['nameSite'], 'spaces' => $_POST['spaces'],'iban' => $_POST['iban'], 'delivery' => $result['idAddress'] ,'correspondence' => $result['idAddress'], 'user_idUser' => $idUser));
                
                if( $res != true)
                {
                $errt .= 'Fehler beim speichern der Eingaben';
                }
                else
                {
                  header("Location: /site/reservations.php");
                  exit();
                }
                  
              }
              else
              {   
                  $stmt = $dbo -> prepare("SELECT idAddress FROM address where address = :address  and email = :email and phone = :phone");
                  $stmt -> execute(array('address' => $_POST['address'], 'email' => $_POST['email'],'phone' => $_POST['phone']));
                  $result = $stmt -> fetch();
                  $stmtli = $dbo -> prepare("SELECT idAddress FROM address where address = :address  and email = :email and phone = :phone");
                  $stmtli -> execute(array('address' => $_POST['liaddress'], 'email' => $_POST['liemail'],'phone' => $_POST['liphone']));
                  $resultli = $stmt -> fetch();
                  $insertSite = $dbo -> prepare ("INSERT INTO site (name, spaces, iban, delivery, correspondence, user_idUser) VALUES (:name, :spaces, :iban, :delivery, :correspondence, :user_idUser)");
                  $insertSite -> execute(array( 'name' => $_POST['nameSite'],'spaces' => $_POST['spaces'],'iban' => $_POST['iban'], 'delivery' => $resultli['idAddress'] ,'correspondence' => $result['idAddress'], 'user_idUser' => $idUser));
                  
                  if( $insertSite != true)
                  {
                  $errt .= 'Fehler beim speichern der Eingaben';
                  }
                  else
                  {
                    header("Location: /site/reservations.php");
                    exit();
                  }
                  if($insertSite== true)
                {
                  header("Location: /site/reservations.php");
                  exit();
                }
              } 
            }
         }
        ?>

    
    <?php         
        include '../includes/nav.php';
        include '../includes/errorhandling.php'; //include errormodal
    ?>

<div class="container-fluid">
  <div class="row">
      <div class="col m-1">
        </div>
      <div class="col-10 m-1 text-center">

        <h1>Herzlich Willkommen <span style="color: red;"><i class="fas fa-heart"></i></span></h1>
        <hr class="my-4">
        <h3>Standort Erstellung</h3> 
        
        <p>Vielen Dank für Ihre Registrierung auf unserer Plattform. Hier können Sie Ihren Standort erfassen und zur Verfügung stellen.</p>
        
        

        


        <hr class="my-4"><br>
            <div class="container-fluid padding">
              <div class="row welcom ">
                <div>
                <button class="btn btn-primary btn-lg" type="submit" data-toggle="modal" data-target="#adress_boothprovider">Marktstandort erstellen</button>
                        <?php
                            
                            echo('<form method="POST" action="./site.php">');
                            echo('<div class="modal fade " id="adress_boothprovider" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">');
                            echo('<div class="modal-dialog modal-notify modal-lg modal-success modal-fluid" role="document" data-backdrop="static">');
                            echo('<div class="modal-content">');
                            echo('<div class="modal-header">');
                            echo('<h2 class="modal-title" id="staticBackdropLabel">Marktstand registrieren</h2>');
                            echo('<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>');
                            echo('</div>');
                            echo('<div class="modal-body">');
                            echo('<table class="table">');
                              echo('<tbody>');
                                echo('<tr>');
                                  echo('<td>Marktstandort</td>');
                                  echo('<td><input class="form-control" type="text" name="nameSite" id="nameSite" maxlength="45" required="required"/></td>');
                                echo('</tr>');
                                echo('<tr>');
                                  echo('<td>Verfügbare Plätze am Standort</td>');
                                  echo('<td><input class="form-control" type="text" name="spaces" id="spaces" maxlength="11" required="required"/></td>');
                                echo('</tr>');
                                echo('<tr>');
                                  echo('<td>IBAN Nummer</td>');
                                  echo('<td><input class="form-control" type="text" name="iban" id="iban" maxlength="45" required="required"/></td>');
                                echo('</tr>');
                              echo('</tbody>');
                            echo('</table>');

                              echo('<table class="table">');
                                echo('<tbody>');
                                      echo('<tr><input class="form-check-input"  type="checkbox" value="true" id="myCheck" name="li" onclick="myFunction()" checked>');
                                      echo('<label class="form-check-label">&nbsp;identische Lieferadresse</label></tr>');
                                echo('</tbody>');
                              echo('</table>');
                              echo('<table class="table">');
                                echo('<tbody>'); 
                                  echo('<tr>');
                                    echo('<td>Adresse</td>');
                                    echo('<td><input class="form-control" type="address" name="address" id="adress" maxlength="200" required="required"/></td>');
                                  echo('</tr>');
                                  echo('<tr>');
                                    echo('<td>Plz</td>');
                                    echo('<td><input class="form-control" type="number"  name="plz" id="plz" required="required"/></td>');
                                  echo('</tr>');
                                  echo('<tr>');
                                    echo('<td><label>Stadt</label></td>');
                                    echo('<td><input class="form-control" type="text" name="city" id="city" maxlength="45" required="required"/></td>');                                                                           
                                  echo('</tr>');
                                  echo('<tr>');
                                    echo('<td><label>E-Mail</label></td>');
                                    echo('<td><input class="form-control" type="email" name="email" id="email" maxlength="200" required="required"/></td>');                                                                           
                                  echo('</tr>');
                                  echo('<tr>');
                                    echo('<td><label>Handy</label></td>');
                                    echo('<td><input class="form-control" type="phone" name="phone" id="phone" maxlength="20" required="required"/></td>');                                                                           
                                  echo('</tr>');
                                echo('</tbody>');
                              echo('</table>');
                              echo('<div class="liaddress" id="liaddress" style="display:none">');
                                echo('<table class="table">');
                                  echo('<tbody>');
                                    echo('<tr>');
                                      echo('<td>Lieferadresse</td>');
                                      echo('<td><input class="form-control li_input" type="address" name="liaddress" id="liaddress" maxlength="200"  /></td>');
                                    echo('</tr>');
                                    echo('<tr>');
                                      echo('<td>Plz</td>');
                                      echo('<td><input class="form-control li_input" type="number"  name="liplz" id="liplz"  style="hidden"/></td>');
                                    echo('</tr>');
                                    echo('<tr>');
                                      echo('<td><label>Stadt</label></td>');
                                      echo('<td><input class="form-control li_input" type="text" name="licity" id="licity" maxlength="45"  style="hidden"/></td>');                                                                           
                                    echo('</tr>');
                                    echo('<tr>');
                                      echo('<td><label>E-Mail</label></td>');
                                      echo('<td><input class="form-control li_input" type="email" name="liemail" id="liemail" maxlength="200"  style="hidden"/></td>');                                                                           
                                    echo('</tr>');
                                    echo('<tr>');
                                      echo('<td><label>Handy</label></td>');
                                      echo('<td><input class="form-control li_input" type="phone" name="liphone" id="liphone" maxlength="20"  style="hidden"/></td>');                                                                           
                                    echo('</tr>');
                                  echo('</tbody>');
                                echo('</table>');
                              echo('</div>');
                              echo('</div>');
                              //button exit and reservation');
                              echo('<div class="modal-footer justify-content-center">');
                                echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                echo('<button type="submit" class="btn btn-primary" name="import">Standort speichern</button>');
                              echo('</div>');
                            echo('</div>');
                            echo('</div>');
                            echo('</div>');
                            echo('</form>');       
                  ?>
                </div>
              </div>
            </div>
      </div>
      <div class="col m-1">
      </div>
   </div>
 </div>
</body>
</html>