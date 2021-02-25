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
          var text = document.getElementById("rgadress");

          // If the checkbox is checked, display the output text
          if (checkBox.checked == false){
            text.style.display = "block";
            $('.reg_input').prop('required',true);
            
          } else {
            text.style.display = "none";
            $('.reg_input').prop('required',false);
          }
        }
      </script>
</head>
<body>


    <?php
      $errt = "";
        if(isset($_SESSION['id']))
        {
          if ( 'new' != $_SESSION['privilege'] ) {
            // access denied
            //header('Location: ../index.php');
          }
    
        }
        
        ?>
        <?php
          if (isset($_POST['import']) && isset($_SERVER['REQUEST_URI']))
          {
            $idUser=$_SESSION['idUser'];
            $sidestmt = $dbo->prepare("SELECT * FROM boothprovider WHERE name = :name");
            $sidestmt -> execute(array('name' => $_POST['nameSide']));
            $nameSide = $sidestmt->fetch();
            
            if($nameSide !== false) {
                $errt .= 'Der angegebene Standname '.$_POST['nameSide'].' ist schon vergeben.';
                
            }
            else
            {
                  //insert address
              $insert = $dbo -> prepare ("INSERT INTO address (address, plz, city, email, phone) VALUES (:address, :plz, :city, :email, :phone)");
              $insert -> execute(array( 'address' => $_POST['address'], 'plz' => $_POST['plz'], 'city' =>  $_POST['city'], 'email' => $_POST['email'], 'phone' => $_POST['phone']));
              
              if( $insert != true)
              {
              $errt .= 'Fehler beim speichern der Eingaben';
              }
                  

              if (empty($_POST['rg']))
              {
                //insert address
                $insert2 = $dbo -> prepare ("INSERT INTO address (address, plz, city, email, phone) VALUES (:address, :plz, :city, :email, :phone)");
                $insert2 -> execute(array( 'address' => $_POST['rgaddress'], 'plz' => $_POST['rgplz'], 'city' =>  $_POST['rgcity'], 'email' => $_POST['rgemail'], 'phone' => $_POST['rgphone']));
              } 
              if(isset($_POST['rg'])) 
              {
                  $stmt = $dbo -> prepare("SELECT idAddress FROM address where address = :address  and email = :email and phone = :phone");
                  $stmt -> execute(array('address' => $_POST['address'], 'email' => $_POST['email'],'phone' => $_POST['phone']));
                  $result = $stmt -> fetch();

                    $insertUser = $dbo -> prepare ("INSERT INTO boothprovider (name, status, qcheck, correspondence, billing, user_idUser) VALUES (:name,'trial', '0', :correspondence, :billing, :user_idUser)");
                    $insertUser -> execute(array( 'name' => $_POST['nameSide'], 'correspondence' => $result['idAddress'],'billing' => $result['idAddress'] , 'user_idUser' => $idUser));

                    if( $insertUser != true)
                    {
                    $errt .= 'Fehler beim speichern der Eingaben';
                    }
                    else
                    {
                      header("Location: ../provider/reservation_provider.php");
                      exit();
                    }
                  
              }
              else
              {   
                  $stmt = $dbo -> prepare("SELECT idAddress FROM address where address = :address  and email = :email and phone = :phone");
                  $stmt -> execute(array('address' => $_POST['address'], 'email' => $_POST['email'],'phone' => $_POST['phone']));
                  $result = $stmt -> fetch();
                  $stmtrg = $dbo -> prepare("SELECT idAddress FROM address where address = :address  and email = :email and phone = :phone");
                  $stmtrg -> execute(array('address' => $_POST['rgaddress'], 'email' => $_POST['rgemail'],'phone' => $_POST['rgphone']));
                  $resultrg = $stmt -> fetch();
                  $insertUser = $dbo -> prepare ("INSERT INTO boothprovider (name, status, qcheck, correspondence, billing, user_idUser) VALUES (:name,'trial', '0', :correspondence, :billing, :user_idUser)");
                  $insertUser -> execute(array( 'name' => $_POST['nameSide'], 'correspondence' => $result['idAddress'],'billing' => $resultrg['idAddress'] , 'user_idUser' => $idUser));
                  
                  if( $insertUser != true)
                    {
                    $errt .= 'Fehler beim speichern der Eingaben';
                    }
                    else
                    {
                      header("Location: ../provider/reservation_provider.php");
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

        <h1>Herzlich Wilkommen <span style="color: red;"><i class="fas fa-heart"></i></span></h1>
        <hr class="my-4">
        <h3>Qualification</h3> 
        
        <p>Vielen Dank für Ihre Registrierung auf unserer Plattform. Damit Sie einen Markstand über unsere Webseite betreiben können, müssen Sie ein Qualifikation Verfahren durchlaufen.</p>
        <p>Mit der eingabe alle Dokumente werden Sie überprüft und danach können Sie einen Termin für die Probemonate reservieren.</p>
        <p>Wenn die zwei Monate Probemonat fertig sind und alle Qualifikationen abgeschlossen sind, können Sie entweder einen stand für 12 Monate oder 3 mal einen Stand für 6 Monate reservieren.</p>
        <p>Nach dem Sie sich kostenpfichtig registriert haben und Ihre Adresse und den Marktstand angegeben haben, erhalten sie von uns einen Brief in den nächsten Tagen.</p>

        


        <hr class="my-4"><br>
            <div class="container-fluid padding">
              <div class="row welcom ">
                <div>
                <button class="btn btn-primary btn-lg" type="submit" data-toggle="modal" data-target="#adress_boothprovider">Jetzt kostenpflichtig Registrieren</button>
                        <?php
                            
                            echo('<form method="POST" action="./quali.php">');
                            echo('<div class="modal fade " id="adress_boothprovider" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">');
                            echo('<div class="modal-dialog modal-notify modal-lg modal-success modal-fluid" role="document" data-backdrop="static">');
                            echo('<div class="modal-content">');
                            echo('<div class="modal-header">');
                            echo('<h2 class="modal-title" id="staticBackdropLabel">Marktstand registrieren</h2>');
                            echo('<button type="button" class="btn-close"  data-dismiss="modal" aria-label="Close"></button>');
                            echo('</div>');
                            echo('<div class="modal-body">');
                            echo('<table class="table">');
                              echo('<tbody>');
                                echo('<tr>');
                                  echo('<td>Marktstand</td>');
                                  echo('<td><input class="form-control" type="address" name="nameSide" id="nameSide" maxlength="200" required="required"/></td>');
                                echo('</tr>');
                              echo('</tbody>');
                            echo('</table>');

                              echo('<table class="table">');
                                echo('<tbody>');
                                      echo('<tr><input class="form-check-input"  type="checkbox" value="true" id="myCheck" name="rg" onclick="myFunction()" checked>');
                                      echo('<label class="form-check-label">&nbsp;identische Rechnungsadresse</label></tr>');
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
                              echo('<div class="rgadress" id="rgadress" style="display:none">');
                                echo('<table class="table">');
                                  echo('<tbody>');
                                    echo('<tr>');
                                      echo('<td>Rechnungsadresse</td>');
                                      echo('<td><input class="form-control reg_input" type="address" name="rgaddress" id="rgadress" maxlength="200"  /></td>');
                                    echo('</tr>');
                                    echo('<tr>');
                                      echo('<td>Plz</td>');
                                      echo('<td><input class="form-control reg_input" type="number"  name="rgplz" id="rgplz"  style="hidden"/></td>');
                                    echo('</tr>');
                                    echo('<tr>');
                                      echo('<td><label>Stadt</label></td>');
                                      echo('<td><input class="form-control reg_input" type="text" name="rgcity" id="rgcity" maxlength="45"  style="hidden"/></td>');                                                                           
                                    echo('</tr>');
                                    echo('<tr>');
                                      echo('<td><label>E-Mail</label></td>');
                                      echo('<td><input class="form-control reg_input" type="email" name="rgemail" id="rgemail" maxlength="200"  style="hidden"/></td>');                                                                           
                                    echo('</tr>');
                                    echo('<tr>');
                                      echo('<td><label>Handy</label></td>');
                                      echo('<td><input class="form-control reg_input" type="phone" name="rgphone" id="rgphone" maxlength="20"  style="hidden"/></td>');                                                                           
                                    echo('</tr>');
                                  echo('</tbody>');
                                echo('</table>');
                              echo('</div>');
                              echo('</div>');
                              //button exit and reservation');
                              echo('<div class="modal-footer justify-content-center">');
                                echo('<button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>&nbsp;');
                                echo('<button type="submit" class="btn btn-primary" name="import">Adresse speichern</button>');
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