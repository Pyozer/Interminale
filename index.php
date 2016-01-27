<?php
$titre_page = "Accueil";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/home');
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row" id="beforePost">
		<div class="col-sm-1 col-md-3"></div>
		<div class="col-xs-12 col-sm-10 col-md-6" style="padding: 0;">
			<?php flash(); ?>

			<?php require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/notifs-home.php'; ?>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-1 col-md-3"></div>
    	<div class="col-xs-12 col-sm-10 col-md-6" id="all_post">
			<?php require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/post_friends.php'; ?>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>