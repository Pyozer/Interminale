<?php
$titre_page = "Utilisateurs inscrits";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/home');
}

$getListMembres = $user->all_user_register();
$nbrusers = $getListMembres->rowCount();

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-2 col-md-3"></div>
		<div class="col-xs-12 col-sm-8 col-md-6" id="divclasseorfriend">
			<h1 class="page-header">Utilisateurs inscrits <span class="badge"><?= $nbrusers; ?></span></h1>
			<div class="row">
				<div class="list-group" style="margin-bottom: 0;">
					<?php require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/all_users.php'; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>