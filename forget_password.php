<?php
$titre_page = "Mot de passe oublié";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(connect()) {
    $user->redirect('/');
}

/* Connexion de l'utilisateur */
if(isset($_POST['submitforget'])) {
	$encoding = 'utf-8';
	$username = trim($_POST['email']);
	$username = htmlspecialchars($username, ENT_QUOTES, $encoding);

    $username = mb_strtolower($username, 'UTF-8');
 
    if(empty($username)) {
    	$error = erreur('USER_NO_FIELDTEXT');
    	setFlash($error,"danger");
   	} else {
   		$result = $user->forget_password($username);
   		if($result['status'] != 0) {
   			setFlash($result['err'], 'success');
   		} else {
   			setFlash($result['err'], 'danger');
   		}
  	}
}

if(isset($_POST['submitreset'])) {
	$password = $_POST['password'];
	$passwordrepeat = $_POST['passwordrepeat'];

	if($password == $passwordrepeat) {
		if(strlen(utf8_decode($password)) > 5) {
			$passwordhash = passwordhash($password);

			$changepass = $DB_con->prepare("UPDATE users SET password=:password WHERE email=:email");   
			$changepass->execute(array(
				'password' => $passwordhash,
				'email' => $_GET['email']
			));

			$deletetoken = $DB_con->prepare("DELETE FROM forget_password WHERE email=:email AND token=:token");   
			$deletetoken->execute(array(
				'email' => $_GET['email'],
				'token' => $_GET['token']
			));

			setFlash('Votre mot de passe a bien été changé ! Reconnectez vous',"success");
			$user->redirect('/connexion');
		} else {
			$error = erreur('USER_PASSWORD_CARACT');
      		setFlash($error,"danger");
		}
	} else {
		$error = erreur('USER_SAME_PASSWORD');
      	setFlash($error,"danger");
	}
}

if(isset($_GET['email']) && isset($_GET['token'])) {
	$checktoken = $DB_con->prepare("SELECT * FROM forget_password WHERE email=:email AND token=:token LIMIT 1");
    $checktoken->execute(array(
      'email'=> $_GET['email'],
      'token' => $_GET['token']
    ));
    
	if($checktoken->rowCount() > 0) {
		$resetpasswordok = true;
	} else {
		setFlash('L\'url de réinitialisation est incorrect !', "danger");
		$user->redirect('/connexion');
	}
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-2 col-md-3"></div>
		<div class="col-xs-12 col-sm-8 col-md-6">
			<?php
			if(isset($resetpasswordok)) { ?>
				<h2 class="page-header" style="margin-bottom: 35px;border-bottom: 1px solid #9E9E9E;text-align: center;">Réinitialisez votre mot de passe</h2>
				<form method="post" accept-charset="UTF-8">
					<div class="row">
						<?php flash(); ?>
						<div class="col-xs-12" style="margin-bottom: 30px;">
							<input type="password" class="form-control input-lg floating-label" name="password" placeholder="Votre nouveau mot de passe" style="width: 100%;" autocomplete="off" required>
							<br />
							<input type="password" class="form-control input-lg floating-label" name="passwordrepeat" placeholder="Confirmez nouveau mot de passe" style="width: 100%;" autocomplete="off" required>
						</div>
						<div class="col-xs-12">
							<div class="form-group">
								<input type="submit" class="btn btn-outline btn-outline-primary" name="submitreset" value="Valider" style="margin-right: 15px;">
							</div>
						</div>
					</div>
				</form>
				<span style="float: right;">Vous voulez revenir à l'accueil ? <a href="/home" style="font-style: italic;">Cliquez ici</a></span>
			<?php
			} else { ?>
				<h2 class="page-header" style="margin-bottom: 35px;border-bottom: 1px solid #9E9E9E;text-align: center;">Mot de passe oublié <a role="button" data-toggle="collapse" href="#collapseHelpemail" aria-expanded="false" aria-controls="collapseHelpemail" style="font-size: 18px;"><i class="fa fa-info-circle"></i></a></h2>
				<form method="post" accept-charset="UTF-8">
					<div class="row">
						<?php flash(); ?>
						<div class="col-xs-12" style="margin-bottom: 30px;">
							<input type="email" class="form-control input-lg floating-label" name="email" placeholder="Adresse mail" style="width: 100%;" value="<?php if(isset($error)) { echo $username; } ?>" autocomplete="off" required>

							<div class="collapse" id="collapseHelpemail">
							  <blockquote class="blockquote-help">
							    <p class="help-block text-justify" style="padding: 15px 0 15px 0;">Un mail va vous être envoyé avec un lien pour changer le mot de passe.</p>
							  </blockquote>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="form-group">
								<input type="submit" class="btn btn-outline btn-outline-primary" name="submitforget" value="Valider" style="margin-right: 15px;">
							</div>
						</div>
					</div>
				</form>
				<span style="float: right;">Vous avez retrouvé le mot de passe ? <a href="/connexion" style="font-style: italic;">Connectez vous</a></span>
			<?php } ?>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>