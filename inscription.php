<?php
$titre_page = "Inscription";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(connect()) {
    $user->redirect('/');
}

if(isset($_POST['submitsignupFERMER']))
{
	$prenom = trim($_POST['prenom']);
	$prenom = htmlspecialchars($prenom, ENT_QUOTES);

	$nom = trim($_POST['nom']);
	$nom = htmlspecialchars($nom, ENT_QUOTES);

	$email = trim($_POST['email']);
	$email = htmlspecialchars($email, ENT_QUOTES);
	$email = mb_strtolower($email);

	$classe = trim($_POST['classe']);
	$classe = htmlspecialchars($classe, ENT_QUOTES);

	$password = trim($_POST['password']);
	$password = htmlspecialchars($password, ENT_QUOTES);

	$repeatpassword = trim($_POST['confirmpassword']);
	$repeatpassword = htmlspecialchars($repeatpassword, ENT_QUOTES);

	$dobjour = trim($_POST['jour']);
	$dobjour = htmlspecialchars($dobjour, ENT_QUOTES);

	$dobmois = trim($_POST['mois']);
	$dobmois = htmlspecialchars($dobmois, ENT_QUOTES);

	$dobannee = trim($_POST['annee']);
	$dobannee = htmlspecialchars($dobannee, ENT_QUOTES);

	$sexe = trim($_POST['sexe']);
	$sexe = htmlspecialchars($sexe, ENT_QUOTES);

  if($sexe == "Homme") { $sexehomme = "checked"; } else { $sexehomme = ""; }
  if($sexe == "Femme") { $sexefemme = "checked"; } else { $sexefemme = ""; }

  if($classe == "terminale-s") { $ts = "selected"; } else { $ts = ""; }
  if($classe == "terminale-es") { $tes = "selected"; } else { $tes = ""; }
  if($classe == "terminale-l") { $tl = "selected"; } else { $tl = ""; }

	if(empty($classe) || empty($prenom) || empty($nom) || empty($email) || empty($password) || empty($repeatpassword) || empty($dobjour) || empty($dobmois) || empty($dobannee) || empty($sexe)) {
    $error = erreur('USER_NO_FIELDTEXT');
		setFlash($error,"danger");
	} else if($classe == "nochoice") {
    $error = "Tu n'a pas selectionné ta classe";
    setFlash($error,"danger");
  } else if(!only_letters($prenom) || !only_letters($nom)) {
		$error = erreur('INPUT_ONLY_LETTERS');
		setFlash($error,"danger");
	} else if(strlen(utf8_decode($dobjour)) > 2 || strlen(utf8_decode($dobmois)) > 2 || strlen(utf8_decode($dobannee)) > 4) {
		$error = erreur('USER_WRONG_DATEOFBIRTH');
		setFlash($error,"danger");
	} else if(!checkdate($dobmois,$dobjour,$dobannee)) {
		$error = erreur('USER_WRONG_DATEOFBIRTH');
		setFlash($error,"danger");
	} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = erreur('USER_VALID_EMAIL');
		setFlash($error,"danger");
	} else if($password != $repeatpassword){
		$error = erreur('USER_SAME_PASSWORD');
		setFlash($error,"danger");
	} else if(strlen(utf8_decode($password)) < 6){
		$error = erreur('USER_PASSWORD_CARACT');
		setFlash($error,"danger");
	} else {
      /* Met en majuscule la 1ere lettre du prenom et nom */
      $prenom = ucname($prenom);
      $nom = ucname($nom);
      /* Supprime accent etc.. pour le pseudo */
      $nom_min = suppr_accents($nom);
      $prenom_min = suppr_accents($prenom);
      $pseudo = $prenom_min.".".$nom_min;

      $prenomplusnom = $prenom." ".$nom;

      try
      {
        /* On vérifie si pas déjà adresse mail */
        $checkemail = $DB_con->prepare("SELECT * FROM users WHERE email=:email");
        $checkemail->execute(array(
          'email' => $email
        ));
        $row = $checkemail->fetch(PDO::FETCH_ASSOC);

        /* On vérifie si pas le même nom / prenom */
        $checkuser = $DB_con->prepare("SELECT * FROM users WHERE prenomplusnom=:prenomplusnom");
        $checkuser->execute(array(
          'prenomplusnom' => $prenomplusnom
        ));

        $datedenaissance = $dobannee."-".$dobmois."-".$dobjour;

        $emailexist = $checkemail->rowCount();
        $userexist = $checkuser->rowCount();

        if($emailexist > 0) {
            $error = erreur('USER_EMAIL_TAKE');
            setFlash($error,"danger");
        } else {
          if($userexist > 0) {
            $pseudo = $pseudo.".".$userexist;
          }
          if($user->register($pseudo,$prenom,$nom,$email,$password,$classe,$datedenaissance,$sexe)) {
            /* On connecte l'utilisateur directement */
            $session = md5(rand());
            $lastco = strftime('%d %B %Y à %H:%M');
            $updateMembre = $DB_con->prepare('UPDATE users SET session=:session, lastco=:lastco WHERE email=:email');
            $updateMembre->execute(array(
              'email' => $email,
              'session' => $session,
              'lastco' => $lastco
            ));

            $getuserinfo = $DB_con->prepare("SELECT id FROM users WHERE email=:email LIMIT 1");
            $getuserinfo->execute(array(
              'email'=> $email
            ));
            $userRow = $getuserinfo->fetch(PDO::FETCH_ASSOC);

            $_SESSION['session'] = $session;
            $_SESSION['userid'] = $userRow['id'];
            $_SESSION['userpseudo'] = $prenomplusnom;
            $_SESSION['username'] = $pseudo;
            $_SESSION['userclasse'] = $classe;

            $user->redirect('/');
          } else {
            setFlash(erreur('SIGNUP_FAIL'),"danger");
          }
        }
     }
     catch(PDOException $e) {
        echo $e->getMessage();
     }
  } 
}
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-2 col-md-3"></div>
		<div class="col-xs-12 col-sm-8 col-md-6" style="padding: 0;">
			<div class="panel panel-default" style="border-radius: 3px;">
			    <div class="panel-heading" style="text-align: center;margin-bottom: 10px;"><h3>Inscription</h3></div>
			    <div class="panel-body" style="padding: 25px;">
			      <div class="row">
			        <?php flash(); ?>
				      <form method="post" accept-charset="UTF-8">
   							<div class="col-xs-12 col-sm-6" style="margin-bottom: 30px;">
   								<input type="text" class="form-control input-lg floating-label" name="prenom" autocomplete="off" placeholder="Prénom" value="<?php if(isset($error)) { echo $prenom; } ?>" required>
   							</div>
   							<div class="col-xs-12 col-sm-6" style="margin-bottom: 30px;">
   								<input type="text" class="form-control input-lg floating-label" name="nom" autocomplete="off" placeholder="Nom" value="<?php if(isset($error)) { echo $nom; } ?>" required>
   							</div>
   							<div class="col-xs-12" style="margin-bottom: 30px;">
   								<input type="email" class="form-control input-lg floating-label" name="email" autocomplete="off" placeholder="Adresse mail" value="<?php if(isset($error)) { echo $email; } ?>" required>
   							</div>
   							<div class="col-xs-12 col-sm-6" style="margin-bottom: 30px;">
   								<input type="password" class="form-control input-lg floating-label" name="password" autocomplete="off" placeholder="Mot de passe" required>
   							</div>
   							<div class="col-xs-12 col-sm-6" style="margin-bottom: 30px;">
   								<input type="password" class="form-control input-lg floating-label" name="confirmpassword" autocomplete="off" placeholder="Confirmer mot de passe" required>
   							</div>
   							<div class="col-xs-12">
   								<span style="font-size: 18px;">Date de naissance</span>
   								<hr style="margin-top: 5px;">
   							</div>
   							<div class="col-xs-4" style="margin-bottom: 30px;">
   								<input type="number" class="form-control input-lg floating-label" step="1" name="jour" autocomplete="off" placeholder="Jour" min="1" max="31" value="<?php if(isset($error)) { echo $dobjour; } else { echo "1"; } ?>" required>
   							</div>
   							<div class="col-xs-4" style="margin-bottom: 30px;">
   								<input type="number" class="form-control input-lg floating-label" step="1" name="mois" autocomplete="off" placeholder="Mois" min="1" max="12" value="<?php if(isset($error)) { echo $dobmois; } else { echo "1"; } ?>" required>
   							</div>
   							<div class="col-xs-4" style="margin-bottom: 30px;">
   								<input type="number" class="form-control input-lg floating-label" step="1" name="annee" autocomplete="off" placeholder="Année" min="1950" max="2015" value="<?php if(isset($error)) { echo $dobannee; } else { echo "1998"; } ?>" required>
   							</div>
                <div class="col-xs-2" style="margin-top: 15px;text-align: center;">
                   <span style="font-size: 18px;">Classe</span>
                </div>
                <div class="col-xs-10" style="margin-bottom: 30px;">
                   <select class="form-control input-lg" name="classe" required>
                      <option value="nochoice">Selectionne ta classe</option>
                      <option value="terminale-s" <?php if(isset($error)) { echo $ts; } ?>>Terminale S</option>
                      <option value="terminale-es" <?php if(isset($error)) { echo $tes; } ?>>Terminale ES</option>
                      <option value="terminale-l" <?php if(isset($error)) { echo $tl; } ?>>Terminale L</option>
                   </select>
                </div>
   							<div class="col-xs-12">
   								<div class="form-group">
   									<input type="button" class="btn btn-success btn-disabled" style="display: inline-block;margin-right: 35px;" name="submitsignup" value="S'inscrire (fermé)" disabled>
                      <div class="radio radio-primary" style="display: inline-block;">
                        <label>
                          <input type="radio" name="sexe" value="Femme" <?php if(isset($error)) { echo $sexefemme; } ?> required><span class="circle"></span><span class="check"></span>
                          <i class="fa fa-female"></i> Femme
                        </label>
                      </div>
                      <div class="radio radio-primary" style="display: inline-block;">
                        <label>
                          <input type="radio" name="sexe" value="Homme" <?php if(isset($error)) { echo $sexehomme; } ?> required><span class="circle"></span><span class="check"></span>
                          <i class="fa fa-male"></i> Homme
                        </label>
                      </div>
   								</div>
   							</div>
						  </form>
					  </div>
			    </div>
			</div>
			<span style="float: right;">Vous avez déjà un compte ? <a href="/connexion">Connectez vous !</a></span>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>