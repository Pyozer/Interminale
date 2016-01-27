<?php
$titre_page = "Connexion";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(connect()) {
    $user->redirect('/');
}

/* Connexion de l'utilisateur */
if(isset($_POST['submitconnexion'])) {
	$encoding = 'utf-8';
	$username = trim($_POST['email']);
	$username = htmlspecialchars($username, ENT_QUOTES, $encoding);
    $password = trim($_POST['password']);
    $password = htmlspecialchars($password, ENT_QUOTES, $encoding);

    $username = mb_strtolower($username, 'UTF-8');
 
    if(empty($username) || empty($password)) {
    	$error = erreur('USER_NO_FIELDTEXT');
    	setFlash($error,"danger");
   	} else {
	    if($user->login($username,$password)) {
			$user->redirect('/');
		} else {
			$error = erreur('USER_ID_ERROR');
			setFlash($error,"danger");
		}
  	}
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-2 col-md-3"></div>
		<div class="col-xs-12 col-sm-8 col-md-6">
			<h2 class="page-header" style="margin-bottom: 35px;border-bottom: 1px solid #9E9E9E;text-align: center;">Connexion</h2>
			<form method="post" accept-charset="UTF-8">
				<div class="row">
					<?php flash(); ?>
					<div class="col-xs-12" style="margin-bottom: 30px;">
						<input type="email" class="form-control input-lg floating-label" name="email" placeholder="Adresse mail" style="width: 100%;" value="<?php if(isset($error)) { echo $username; } ?>" required>
					</div>
					<div class="col-xs-12" style="margin-bottom: 30px;">
						<input type="password" class="form-control input-lg floating-label" name="password" placeholder="Mot de passe" style="width: 100%;" value="<?php if(isset($error)) { echo $password; } ?>" autocomplete="off" required>
						<br />
						<span>Mot de passe oublié ? <a href="/forget_password" title="Mot de passe oublié">Cliquez ici</a></span>
					</div>
					<div class="col-xs-12">
						<div class="form-group">
							<input type="submit" class="btn btn-outline btn-outline-primary" name="submitconnexion" value="Connexion" style="margin-right: 15px;" >
							<div class="checkbox checkbox-primary" style="display: inline-block;vertical-align: middle;">
								<label>
									<input type="checkbox" name="rememberme"> Se souvenir de moi
								</label>
							</div>
						</div>
					</div>
				</div>
			</form>
			<span style="float: right;">Pas encore de compte ? <a href="/inscription" title="Inscrivez vous">Inscrivez vous !</a></span>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>