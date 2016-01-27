<?php
$titre_page = "Messages";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/home');
}

/* Si message à envoyé, a un utilisateur inexistant */
if(isset($_GET['usermsg'])) {
	$info_profil_userdest = $profil->get_profil_info($_GET['usermsg']);
	if(empty($info_profil_userdest)) {
		$user->redirect('/erreur/404');
	}
} 

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-2 col-md-3"></div>
		<div class="col-xs-12 col-sm-8 col-md-6">
			<?php
			if(isset($_GET['usermsg'])) {
				$name_dest = $info_profil_userdest['prenomplusnom'];
				?>
				<h2 class="page-header">Message privé <small><?php echo $name_dest; ?></small><a type="button" class="btn btn-flat btn-sm pull-right hidden-xs" href="/messages">Retour</a></h2>
				<div id="messagepost">
					<div class="messages-prives" id="messages-prives">
						<?php
							require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/messageprive.php';
							if($lu != 1) {
								$message->msg_put_read($id_conv);
							}
						?>
					</div>
					<hr>
					<div class="row">
						<form method="post" id="send_msgprive">
							<div class="col-xs-8 col-md-9">
								<input type="text" class="form-control" name="inputMsg" id="inputMsg" placeholder="Votre message..." data-conv="<?php echo $id_conv; ?>" style="max-width: 100%;max-height: 150px;" required>
								<span class="help-block" id="errorMsg" style="color: red;font-weight: 500;"></span>
							</div>
							<div class="col-xs-4 col-md-3">
								<button type="submit" class="btn btn-outline btn-outline-primary btn-sm" id="submitSend" data-usermsg="<?php echo $_GET['usermsg']; ?>" data-idconv="<?php echo $id_conv; ?>">Envoyer <i class="fa fa-paper-plane"></i></button>
							</div>
						</form>
					</div>
					<a type="button" class="btn btn-flat btn-sm pull-left visible-xs" href="/messages">Retour</a>
				</div>
			<?php
			} else { ?>
				<h1 class="page-header">Messages privés</h1>
				<br />
				<div class="row" id="beforeMessages">
				</div>
				<div id="msglist">
					<?php require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/messagerie.php'; ?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>