<?php
$titre_page = "Modification profil";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user est connecté */
if(!connect()) {
    $user->redirect('/');
}

$user_id = $_SESSION['username'];

$info_profil = $profil->get_profil_info($user_id);
$id_user = $info_profil['id'];
$name = $info_profil['prenom']." ".$info_profil['nom'];
$username = $info_profil['pseudo'];
$bio = $info_profil['bio'];
$datedenaissance = $info_profil['datedenaissance'];

$datedenaissance = explode('-', $datedenaissance);
$annee = $datedenaissance['0'];
$mois = $datedenaissance['1'];
$jour = $datedenaissance['2'];

/* On update les info de l'utilisateur */
if(isset($_POST['valid'])) {
	$bio = trim($_POST['bio']);
	$bio = htmlspecialchars($bio, ENT_QUOTES);
	$jour = trim($_POST['jour']);
	$jour = htmlspecialchars($jour, ENT_QUOTES);
	$mois = trim($_POST['mois']);
	$mois = htmlspecialchars($mois, ENT_QUOTES);
	$annee = trim($_POST['annee']);
	$annee = htmlspecialchars($annee, ENT_QUOTES);
	$datedenaissance = $annee."-".$mois."-".$jour;
 
    if(empty($jour) || empty($mois) || empty($annee)) {
    	$error = erreur('USER_NO_DATEOFBIRTH');
    	setFlash($error,"danger");
   	} else if(strlen(utf8_decode($jour)) > 2 || strlen(utf8_decode($mois)) > 2 || strlen(utf8_decode($annee)) > 4) {
    	$error = erreur('USER_WRONG_DATEOFBIRTH');
    	setFlash($error,"danger");
   	} else if(!checkdate($mois,$jour,$annee)) {
   		$error = erreur('USER_WRONG_DATEOFBIRTH');
    	setFlash($error,"danger");
   	} else {
	    if($profil->change_profil_info($user_id, $bio, $datedenaissance)) {
	    	if(isset($_POST['supprimgprofil'])) {
				if($img->suppr_img_profil($user_id, $id_user)) {
					$error = erreur('USER_IMG_SUCCESS_DELETE');
					setFlash($error,"success");
				} else {
					$error = erreur('FAIL_DELETE_IMAGE');
					setFlash($error,"danger");
				}
			} else {
				$error = erreur('CHANGE_INFO_SUCCESS');
	    		setFlash($error,"success");
			}
		} else {
			$error = erreur('CHANGE_INFO_FAIL');
			setFlash($error,"danger");
		}
  	}
}

/* On update l'image de profil de l'utilisateur */
if(isset($_POST['validimgprofil'])) {
	unset($_SESSION['imgprofil']);
	if(isset($_POST['supprimgprofil'])) {
		if($img->suppr_img_profil($user_id, $id_user)) {
			$error = erreur('USER_IMG_SUCCESS_DELETE');
			setFlash($error,"success");
			$user->redirect('/profiledit');
		} else {
			$error = erreur('FAIL_DELETE_IMAGE');
			setFlash($error,"danger");
		}
	} else if(!empty($_FILES['imgprofil']['name'])) {
		if($img->upload_img('imgprofil', '/images/profil/')) {
			$error = erreur('CHANGE_INFO_SUCCESS');
			setFlash($error,"success");
			$user->redirect('/profiledit');
		} else {
			$error = "Erreur lors de l'upload";
			setFlash($error,"danger");
		}
	} else {
		$error = erreur('USER_NO_FILE_INPUT');
		setFlash($error,"danger");
	}
}

/* On update l'image de profil de l'utilisateur */
if(isset($_FILES['imgprofil'])) {
	unset($_SESSION['imgprofil']);
	if(!empty($_FILES['imgprofil']['name'])) {
		if($img->upload_img('imgprofil', '/images/profil/')) {
			$error = erreur('CHANGE_INFO_SUCCESS');
			setFlash($error,"success");
			$user->redirect('/profiledit');
		} else {
			$error = "Erreur lors de l'upload";
			setFlash($error,"danger");
		}
	} else {
		$error = erreur('USER_NO_FILE_INPUT');
		setFlash($error,"danger");
	}
}

if(!isset($_SESSION['imgprofil'])) {
	$hrefimgprofil = $img->get_img_profil($user_id, $id_user);
} else {
	$hrefimgprofil = $_SESSION['imgprofil'];
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<div class="header_edit_profil">
			  <div class="profile-pic">
				<img class="img-demi-circle" src="<?php echo $hrefimgprofil; ?>" alt="<?php echo $name; ?>">
			    <div class="edit">
			    	<form method="post" accept-charset="UTF-8" enctype="multipart/form-data" id="newimgprofil">
				    	<div class="image-upload">
						    <label for="file-input" style="cursor: pointer;">
						        <span>Modifier <i class="fa fa-pencil fa-lg" style="margin-left: 10px;"></i></span>
						    </label>
						    <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
						    <input type="file" id="file-input" name="imgprofil" style="display: none;">
						</div>
					</form>
			    </div>
			  </div>
			  <ul style="padding: 0;vertical-align: middle;list-style: none;">
				<li>
					<span style="display: inline-block;font-size: 25px;"><?php echo $name; ?></span><br/>
				</li>
			  </ul>
			</div>
		</div>
	</div>
	<?php if(empty($_GET['edit'])) { ?>
		<div class="row">
			<div class="col-sm-1 col-md-3"></div>
			<div class="col-xs-12 col-sm-10 col-md-6">
				<form method="post" accept-charset="UTF-8">
					<hr style="border-top: 1px solid #D8D8D8;">
					<?php flash(); ?>
					<div class="form-group">
					    <label class="control-label" for="inputBio">Biographie</label>
					    <textarea type="text" class="form-control input-lg" name="bio" id="inputBio" rows="3" maxlength="175" style="min-width: 100%;max-width: 100%;"><?php echo $bio; ?></textarea>
					    <span class="help-block" id="caracteres" style="text-align: right;">0 caractère / 175</span>
					</div>
					<div class="form-group">
					    <label class="control-label">Date de naissance</label>
					</div>
					<div class="form-group">
					    <div class="col-xs-4" style="margin-bottom: 30px;padding-left: 0;">
							<input type="number" class="form-control input-lg floating-label" name="jour" id="inputdobJour" step="1" placeholder="Jour" value="<?php echo $jour; ?>" min="1" max="31" requiered>
						</div>
						<div class="col-xs-4" style="margin-bottom: 30px;">
							<input type="number" class="form-control input-lg floating-label" name="mois" id="inputdobMois" step="1" placeholder="Mois" value="<?php echo $mois; ?>" min="1" max="12" requiered>
						</div>
						<div class="col-xs-4" style="margin-bottom: 30px;padding-right: 0;">
							<input type="number" class="form-control input-lg floating-label" name="annee" id="inputdobAnnee" step="1" placeholder="Année" value="<?php echo $annee; ?>" min="1950" max="2015" requiered>
						</div>
					</div>
					<button type="submit" class="btn btn-outline-primary" name="valid" id="valid" onClick="addloading('#valid');">Enregistrer</button>
				</form>
				<hr>
				<form method="post" accept-charset="UTF-8" enctype="multipart/form-data">
					<div class="form-group">
						<label class="control-label">Image de profil</label>
					</div>
					<div class="form-group">
						<div class="fileinput fileinput-new" data-provides="fileinput">
							<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 120px; height: 120px;display: inline-block;border-radius: 50%;margin-right: 25px;">
						  		<img src='<?php echo $hrefimgprofil; ?>'>
						  	</div>
						  	<div style="display: inline-block;vertical-align: middle;">
						    	<a type="button" href="/profiledit/imgprofil" class="btn btn-outline-gray" style="margin-bottom: 8px;">Modifier</a>
						    	<br />
						    	<div class="checkbox checkbox-primary">
								  <label>
									<input type="checkbox" name="supprimgprofil" value="true"> Supprimer mon image de profil
								  </label>
								</div>
						  	</div>
						</div>
					</div>
					<button type="submit" class="btn btn-outline-primary" name="validimgprofil" id="validimgprofil" onClick="addloading('#validimgprofil');">Enregistrer</button>
				</form>
			</div>
		</div>
	<?php
	}
	if(!empty($_GET['edit']) && $_GET['edit'] == "imgprofil") {
	?>
		<div class="row">
			<div class="col-sm-1 col-md-3"></div>
			<div class="col-xs-12 col-sm-10 col-md-6">
				<form method="post" accept-charset="UTF-8" enctype="multipart/form-data">
					<hr style="border-top: 1px solid #D8D8D8;">
					<?php flash(); ?>
					<div class="form-group centered">
						<label class="control-label">Sélectionnez votre nouvelle photo de profil</label>
					</div>
					<div class="form-group centered">
						<div class="fileinput fileinput-new" data-provides="fileinput">
						  	<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 120px; height: 120px;display: inline-block;border-radius: 50%;margin-right: 25px;">
						  		<img src='<?php echo $hrefimgprofil."?".filectime(".".$hrefimgprofil); ?>' alt="Image de profil">
						  	</div>
						  	<div style="display: inline-block;vertical-align: middle;">
						    	<span class="btn btn-outline-gray btn-file" style="margin-bottom: 8px;">
						    		<span class="fileinput-new">Choisir une image</span>
						    		<span class="fileinput-exists">En choisir une autre</span>
						    		<input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
						    		<input type="file" name="imgprofil">
						    	</span>
						    	<br>
						    	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Supprimer</a>
						  	</div>
						</div>
					</div>
					<hr>
					<button type="submit" class="btn btn-outline-primary" name="validimgprofil" id="validimgprofil" onClick="addloading('#validimgprofil');">Enregistrer</button>
					<a type="button" class="btn btn-flat" href="/profiledit" title="Retour">Retour</a>
				</form>
			</div>
		</div>
	<?php } ?>
</div>

<script type="text/javascript">
var maxChr = 175; // limite max fixée
function red(nbrChr){
	return Math.round(255*Math.pow(0.977,maxChr-nbrChr));
}
function countChr(){
    var len = $('#inputBio').val().length;

    if (maxChr < len) {
    	$('#inputBio').val( $('#inputBio').val().substring(0,maxChr) );
    	len = maxChr;
    }
    var htmltoput = '<span class="help-block" id="caracteres" style="text-align: right;color:rgb('+red(len)+',0,0)">' + len + ' caractère' + (1<len?'s':'') + ' / '+ maxChr + '</span>';
    $('#caracteres').html(htmltoput);
    if(len >= maxChr) {
    	$('#caracteres').addClass("bold");
    } else {
    	$('#caracteres').removeClass("bold");
    }
};
(function(){
	$('#inputBio').keyup(function() {
	  countChr();
	});
	countChr();
})();
</script>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>