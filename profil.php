<?php
$titre_page = "Profil";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

if(!connect()) {
	$user->redirect('/home');
}

if(!empty($_GET['user'])) {
	$user_id = $_GET['user'];
} else {
	$user_id = $_SESSION['username'];
}
/* On récupère donné du profil */
$info_profil = $profil->get_profil_info($user_id);

/* On vérifie que l'utilisateur existe */
if(empty($info_profil)) {
	$user->redirect('/erreur/404');
} else {
	$id_user = $info_profil['id'];
	$username = $info_profil['pseudo'];
	$name = $info_profil['prenom']." ".$info_profil['nom'];
	$bio = $info_profil['bio'];
	$comptePrive = $info_profil['comptePrive'];
	$sexe = $info_profil['sexe'];
	$classe = $info_profil['classe'];

	if($classe == "terminale-s") { $classe_name = "Terminale S"; $classe_abbre = "T°S"; }
	else if($classe == "terminale-es") { $classe_name = "Terminale ES"; $classe_abbre = "T°ES"; }
	else { $classe_name = "Terminale L"; $classe_abbre = "T°L"; }

	if($user_id != $_SESSION['username']) {
		$hrefimgprofil = $img->get_img_profil($user_id, $id_user);
	} else {
		if(!isset($_SESSION['imgprofil'])) {
			$hrefimgprofil = $img->get_img_profil($user_id, $id_user);
		} else {
			$hrefimgprofil = $_SESSION['imgprofil'];
		}
	}

	$verif_ask_demande = $amis->verif_ask_demande($_SESSION['username'], $username);
	$verif_friend = $amis->verif_friend($_SESSION['username'], $username);

	if($user_id == $_SESSION['username']) {
		$user_connected = '';
	} else {
		if($user->user_connected($id_user)) {
			$user_connected = '<i class="fa fa-circle" id="user_connected" title="Connecté"></i> ';
		} else {
			$user_connected = '';
		}
	}
}
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-xs-12" style="text-align: center;">
			<div class="header_profil">
			  <img class="img-demi-circle" src="<?php echo $hrefimgprofil; ?>" alt="<?php echo $name; ?>">
			  <ul class="info_profil">
				<li>
					<span style="display: inline-block;font-size: 25px;"><span style="text-transform: capitalize;" id="userspan"><?php echo $user_connected.$name; ?> <sup><span class="badge" title="<?php echo $classe_name; ?>"><?php echo $classe_abbre; ?></span></span>
						<?php
						if(($verif_friend || $_SESSION['userclasse'] == $classe) && $_SESSION['username'] != $username) { ?>
							<a href="/messages/<?php echo $username; ?>" class="link" title="Envoyer un message privé à <?php echo $name; ?>" style="font-size: 20px;">
								<i class="fa fa-pencil-square"></i>
							</a>
						<?php } ?>
					</span>
				</li>
				<?php if($_SESSION['username'] == $username && $_SESSION['userid'] == $id_user) { ?>
					<li>
						<span style="display: inline-block;font-size: 20px;"><a type="button" class="btn btn-outline btn-outline-gray" href="/profiledit">Modifier mon profil</a></span>
					</li>
				<?php } else { ?>
					<li>
						<span style="display: inline-block;font-size: 20px;" id="form-content-friend">
							<?php if($verif_ask_demande) { ?>
								<button type="button" class="btn btn-success" style="margin: 10px 1px 0 1px;">Demande envoyée</button><br/>
								<button type="button" class="btn btn-default btn-flat btn-sm friendbtn" name="annuldemand" id="annuldemand" data-user="<?php echo $user_id; ?>" style="text-transform: none;">Annuler la demande</button>
							<?php } else if($verif_friend) { ?>
								<button type="button" class="btn btn-default btn-flat friendbtn" name="supprfriend" id="supprfriend" data-user="<?php echo $user_id; ?>" style="text-transform: none;">Supprimer des amis</button>
							<?php } else { ?>
								<button type="button" class="btn btn-outline btn-outline-primary friendbtn" name="addfriend" id="addfriend" data-user="<?php echo $user_id; ?>"><i class="fa fa-user-plus"></i> Ajouter en ami</button>
							<?php } ?>
						</span>
					</li>
				<?php } ?>
			  </ul>
			</div>
			<?php if(!empty($bio)) { ?>
			<div class="row" style="margin-top: 25px;">
				<div class="col-xs-1 col-md-2"></div>
				<div class="col-xs-10 col-sm-10 col-md-8">
					<p style="font-size: 18px;"><?php echo $bio; ?></p>
				</div>
			</div>
			<?php }
			$nbrpost = $post->get_nbr_post_from_user($user_id);
			$nbrcomment = $comment->get_nbr_comments_user($user_id);
			$nbrlike = $like->get_nbr_likes_user($id_user);
			?>
			<div class="footer_profil" style="margin-top: 15px;">
				<ul style="vertical-align: middle;list-style: none;font-size: 18px;padding: 0;">
					<li style="display: inline-block;margin: 10px 35px 10px 35px;">
						<span><strong><?php echo $nbrpost; ?></strong> <i class="fa fa-pencil" title="publication<?php if($nbrpost > 1) { echo "s"; } ?>"></i></span>
					</li>
					<li style="display: inline-block;margin: 10px 35px 10px 35px;">
						<span><strong><?php echo $nbrcomment; ?></strong> <i class="fa fa-comment" title="commentaire<?php if($nbrcomment > 1) { echo "s"; } ?>"></i></span>
					</li>
					<li style="display: inline-block;margin: 10px 35px 10px 35px;">
						<span><strong><?php echo $nbrlike; ?></strong> <i class="fa fa-heart" title="like<?php if($nbrlike > 1) { echo "s"; } ?>"></i></span>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<hr style="border-top: 1px solid #D8D8D8;">
	<?php
	if($_SESSION['username'] == $username) {
		$idbeforepost = "userbeforePost";
	} else {
		$idbeforepost = "beforePostOther";
	}
	?>
	<div class="row" id="<?php echo $idbeforepost; ?>">
	</div>
	<div class="row">
		<div class="col-sm-1 col-md-3"></div>
    	<div class="col-xs-12 col-sm-10 col-md-6">
			<?php
			if($comptePrive == "true" && $_SESSION['username'] != $username) {
				if($verif_friend) {
					require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/post_user.php';
				} else { ?>
					<div class="centered">
						<h2><small style="font-weight: 300;font-size: 25px;">Ce compte est privé ! Ajoutez le en ami.</small></h2>
					</div>
			<?php }
			} else if($comptePrive != "true" && $_SESSION['username'] != $username) {
				require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/post_user.php';
			} else if($_SESSION['username'] == $username) {
				require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/post_user.php';
			}
			?>
		</div>
	</div>
</div>

<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>