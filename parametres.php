<?php
$titre_page = "Paramètres";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/');
}

$user_id = $_SESSION['username'];
$info_profil = $profil->get_profil_info($user_id);
$id_user = $info_profil['id'];
$username = $info_profil['pseudo'];
$emailactual = $info_profil['email'];

$allowFindSearch = $info_profil['allowFindSearch'];
$comptePrive = $info_profil['comptePrive'];
$notifMailPrive = $info_profil['notifMailPrive'];

if($allowFindSearch == "true") { $allowFindSearch = "checked"; } else { $allowFindSearch = ""; }
if($comptePrive == "true") { $comptePrive = "checked"; } else { $comptePrive = ""; }
if($notifMailPrive == "true") { $notifMailPrive = "checked"; } else { $notifMailPrive = ""; }

if(isset($_COOKIE['theme'])) {
	if($_COOKIE['theme'] == "default") {
		$themedefault = "checked";
	} else {
		$themedefault = "";
	}
	if($_COOKIE['theme'] == "night") {
		$themenuit = "checked";
	} else {
		$themenuit = "";
	}
	if($_COOKIE['theme'] == "navbar") {
		$themenavbar = "checked";
	} else {
		$themenavbar = "";
	}
} else {
	$themedefault = "checked";
	$themenuit = "";
	$themenavbar = "";
}

/* Si modif mdp */
if(isset($_POST['validMDP'])) {
	if(!empty($_POST['ActualMdp']) || !empty($_POST['NewMdp1']) || !empty($_POST['NewMdp2'])) {
		$newmdp1 = htmlspecialchars($_POST['NewMdp1'], ENT_QUOTES);
		$newmdp2 = htmlspecialchars($_POST['NewMdp2'], ENT_QUOTES);
		$actualmdptype = htmlspecialchars($_POST['ActualMdp'], ENT_QUOTES);

		if($newmdp1 == $newmdp2) {
			if(strlen($newmdp1) > 5 && strlen($newmdp2) > 5){
				$actualmdp = $info_profil['password'];
				if(password_verify($actualmdptype, $actualmdp)) {
					if($profil->change_settings_password($user_id, $newmdp1)) {
						$error = erreur('CHANGE_PASSWORD_SUCCESS');
						setFlash($error,"success");
						$user->redirect('/deconnexion?logout=true&edit=password');
					} else {
						$error = erreur('CHANGE_INFO_FAIL');
						setFlash($error,"danger");
					}
				} else {
					$error = erreur('USER_ACTUAL_PASSWORD_FAIL');
					setFlash($error,"danger");
				}
			} else {
				$error = erreur('USER_PASSWORD_CARACT');
			    setFlash($error,"danger");
			}
		} else {
			$error = erreur('USER_SAME_PASSWORD');
			setFlash($error,"danger");
		}
	} else {
		$error = erreur('USER_NO_FIELDTEXT');
		setFlash($error,"danger");
	}
}

/* Si modif email */
if(isset($_POST['validEmail'])) {
	if(!empty($_POST['newEmail'])) {
		$email = trim($_POST['newEmail']);
   		$email = htmlspecialchars($email, ENT_QUOTES);
   		$email = mb_strtolower($email);

		if(filter_var($email, FILTER_VALIDATE_EMAIL)) {

			$stmt = $DB_con->prepare("SELECT * FROM users WHERE email=:email");
			$stmt->execute(array(
				'email' => $email
			));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if($row['email'] == $email) {
				$error = erreur('USER_EMAIL_TAKE');
				setFlash($error,"danger");
			} else {
				if($profil->change_settings_email($user_id, $email)) {
					$error = erreur('CHANGE_EMAIL_SUCCESS');
					setFlash($error,"success");
					$user->redirect('/parametres');
				} else {
					$error = erreur('CHANGE_INFO_FAIL');
					setFlash($error,"danger");
				}
			}
		} else {
			$error = erreur('USER_VALID_EMAIL');
		    setFlash($error,"danger");
		}
	} else {
		$error = erreur('USER_NO_FIELDTEXT');
		setFlash($error,"danger");
	}
}

/* Si modif conf */
if(isset($_POST['validConf'])) {
	if(isset($_POST['allowFindSearch']) && $_POST["allowFindSearch"] == "true") {
		$newallowFindSearch = "true";
	} else {
		$newallowFindSearch = "false";
	}
	if(isset($_POST['comptePrive']) && $_POST["comptePrive"] == "true") {
		$newcomptePrive = "true";
	} else {
		$newcomptePrive = "false";
	}
	if(isset($_POST['notifMailPrive']) && $_POST["notifMailPrive"] == "true") {
		$newnotifMailPrive = "true";
	} else {
		$newnotifMailPrive = "false";
	}
	if(isset($_POST['theme'])) {
		if($_POST["theme"] == "night") {
			setcookie("theme", "night", time()+60*60*24*31);
		} else if($_POST["theme"] == "navbar") {
			setcookie("theme", "navbar", time()+60*60*24*31);
		} else {
			setcookie("theme", "default", time()+60*60*24*31);
		}
	}
	if(!empty($newallowFindSearch) && !empty($newcomptePrive) && !empty($newnotifMailPrive)) {
		if($profil->change_settings_conf($user_id, $newallowFindSearch, $newcomptePrive, $newnotifMailPrive)) {
			$error = erreur('CHANGE_INFO_SUCCESS');
			setFlash($error,"success");
			$user->redirect('/parametres');
		} else {
			$error = erreur('CHANGE_INFO_FAIL');
			setFlash($error,"danger");
		}
	}
}

if(isset($_POST['validSuppr'])) {
	if(isset($_GET['suppr']) && !empty($_GET['suppr']) && $_GET['suppr'] == "user") {
		if(!empty($_POST['ActualMdpVerif'])) {
			if(isset($_POST['confirmSuppr']) && $_POST["confirmSuppr"] == "true") {
				$actualmdptype = htmlspecialchars($_POST['ActualMdpVerif'], ENT_QUOTES);
				$actualmdp = $info_profil['password'];

				if(password_verify($actualmdptype, $actualmdp)) {
					if($profil->delete_user($user_id, $_SESSION['userid'])) {
						$error = erreur('ACCOUNT_SUCCESS_DELETE');
						setFlash($error,"success");
						$user->redirect('/deconnexion?logout=true');
					} else {
						$error = erreur('ACCOUNT_FAIL_DELETE');
						setFlash($error,"danger");
					}
				} else {
					$error = erreur('USER_ACTUAL_PASSWORD_FAIL');
					setFlash($error,"danger");
				}
			} else {
				$error = erreur('USER_NO_CHECK');
				setFlash($error,"danger");
			}
		} else {
			$error = erreur('USER_NO_FIELDTEXT');
			setFlash($error,"danger");
		}
	}
}
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-2 col-md-3"></div>
		<div class="col-xs-12 col-sm-8 col-md-6" id="divparams">
			<h2 class="page-header">Paramètres du compte</h2>
			
			<?php flash();
			
			if(isset($_GET['suppr']) && !empty($_GET['suppr']) && $_GET['suppr'] == "user"){
			?>
				<form method="post" accept-charset="UTF-8">
					<h4 style="margin-top: 45px;margin-bottom: 25px;text-align: center;">Voulez vous vraiment supprimer votre compte ?</h4>
					<div class="form-group">
						<label class="control-label" for="inputActualMdp">Mot de passe actuel</label>
						<input type="password" class="form-control input-lg" name="ActualMdpVerif" id="inputActualMdpVerif" placeholder="Mot de passe actuel">
					</div>
					<div class="checkboxparams">
						<div class="text">Cocher pour valider la suppression</div>
						<div class="checkbox checkbox-primary">
						  <label>
							<input type="checkbox" name="confirmSuppr" value="true">
						  </label>
						</div>
					</div>
					<input type="submit" class="btn btn-danger" name="validSuppr" value="Supprimer le compte définitivement">
				</form>
			<?php } else { ?>

			<!-- Modifier MDP -->
			<form method="post" accept-charset="UTF-8">
				<h4 style="margin-top: 45px;margin-bottom: 25px;text-align: center;">Modifier mon mot de passe</h4>
				<div class="form-group">
					<label class="control-label" for="inputActualMdp">Mot de passe actuel</label>
					<input type="password" class="form-control input-lg" name="ActualMdp" id="inputActualMdp" placeholder="Mot de passe actuel">
				</div>
				<div class="row">
					<div class="form-group col-xs-12 col-sm-6">
						<label class="control-label" for="inputNewMdp1">Nouveau mot de passe</label>
						<input type="password" class="form-control input-lg" name="NewMdp1" id="inputNewMdp1" placeholder="Nouveau mot de passe">
					</div>
					<div class="form-group col-xs-12 col-sm-6">
						<label class="control-label" for="inputNewMdp2">Confirmer mot de passe</label>
						<input type="password" class="form-control input-lg" name="NewMdp2" id="inputNewMdp2" placeholder="Confirmer mot de passe">
					</div>
				</div>
				<input type="submit" class="btn btn-outline-gray" name="validMDP" value="Enregistrer">
			</form>
			<!-- FIN MDP -->
			
			<hr>
			
			<!-- Modifier EMAIL -->
			<form method="post" accept-charset="UTF-8">
				<h4 style="margin-top: 45px;margin-bottom: 25px;text-align: center;">Modifier mon email</h4>
				<div class="form-group">
					<label class="control-label" for="inputEmail">Adresse mail</label>
					<input type="email" class="form-control input-lg" name="newEmail" id="inputEmail" placeholder="Votre adresse mail" value="<?php echo $emailactual; ?>">
				</div>
				<input type="submit" class="btn btn-outline-gray" name="validEmail" value="Enregistrer">
			</form>
			<!-- FIN EMAIL -->				
			<hr>
			<!-- Modifier Autres -->
			<form method="post" accept-charset="UTF-8">
				<h4 style="margin-top: 45px;margin-bottom: 25px;text-align: center;">Autres</h4>		
				<div class="checkboxparams">
					<div class="text">Permettre de me trouver via une recherche  <a role="button" data-toggle="collapse" href="#collapseHelpConfSearch" aria-expanded="false" aria-controls="collapseHelpConfSearch"><i class="fa fa-info-circle"></i></a></div>
					<div class="checkbox checkbox-primary">
					  <label>
						<input type="checkbox" name="allowFindSearch" value="true" <?php echo $allowFindSearch; ?>>
					  </label>
					</div>
					<div class="collapse" id="collapseHelpConfSearch">
					  <blockquote class="blockquote-help">
					    <p class="help-block text-justify" style="padding: 15px 0 15px 0;">Permet à une personne de vous trouver si elle fait une recherche avec votre prénom, nom</p>
					  </blockquote>
					</div>
				</div>
				<div class="checkboxparams">
					<div class="text">Compte privé <a role="button" data-toggle="collapse" href="#collapseHelpConfPrive" aria-expanded="false" aria-controls="collapseHelpConfPrive"><i class="fa fa-info-circle"></i></a></div>
					<div class="checkbox checkbox-primary">
					  <label>
						<input type="checkbox" name="comptePrive" value="true" <?php echo $comptePrive; ?>>
					  </label>
					</div>
					<div class="collapse" id="collapseHelpConfPrive">
					  <blockquote class="blockquote-help">
					    <p class="help-block text-justify" style="padding: 15px 0 15px 0;">Le compte privé vous permet de n'autoriser que vos amis à voir vos publications</p>
					  </blockquote>
					</div>
				</div>
				<div class="checkboxparams">
					<div class="text">Recevoir mail lors d'un message privé  <a role="button" data-toggle="collapse" href="#collapseHelpConfMail" aria-expanded="false" aria-controls="collapseHelpConfMail"><i class="fa fa-info-circle"></i></a></div>
					<div class="checkbox checkbox-primary">
					  <label>
						<input type="checkbox" name="notifMailPrive" value="true" <?php echo $notifMailPrive; ?>>
					  </label>
					</div>
					<div class="collapse" id="collapseHelpConfMail">
					  <blockquote class="blockquote-help">
					    <p class="help-block text-justify" style="padding: 15px 0 15px 0;">Un mail vous sera envoyé si vous recevez un message privé alors que vous n'êtes pas connecté</p>
					  </blockquote>
					</div>
				</div>
				<div class="checkboxparams">
					<div class="text">Thème du site</div>
					<div class="radio radio-primary" style="display: inline-block;margin-left: 35px;">
					    <label>
					    	<input type="radio" name="theme" value="default" <?php echo $themedefault; ?>><span class="circle"></span><span class="check"></span>
					    	 Par defaut
					    </label>
					</div>
					<div class="radio radio-primary" style="display: inline-block;">
					    <label>
					    	<input type="radio" name="theme" value="night" <?php echo $themenuit; ?>><span class="circle"></span><span class="check"></span>
					    	 Nuit
					    </label>
					</div>
					<div class="radio radio-primary" style="display: inline-block;">
					    <label>
					    	<input type="radio" name="theme" value="navbar" <?php echo $themenavbar; ?>><span class="circle"></span><span class="check"></span>
					    	 Navbar
					    </label>
					</div>
				</div>
				<br>
				<input type="submit" class="btn btn-outline-gray" name="validConf" value="Enregistrer">
			</form>
			<!-- FIN Autre -->
			<hr>
			<!-- Supprimer compte -->
			<h4 style="margin-top: 45px;margin-bottom: 25px;text-align: center;">Supprimer mon compte</h4>
			<div class="row centered">
				<div class="form-group col-xs-12 col-sm-6">
					<a type="button" class="btn btn-danger" href="?suppr=user" style="margin: 0;">Supprimer mon compte</a>
				</div>
				<div class="form-group col-xs-12 col-sm-6">
					<span><strong>La suppression du compte est définitive, le compte ne pourra être récupéré.</strong></span>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>