<?php
$titre_page = "Publications";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/home');
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-2 col-md-3"></div>
		<div class="col-xs-12 col-sm-8 col-md-6">
			<h2 class="page-header">Toutes les publications</h2>
		</div>
	</div>
	<br />
	<div class="row" id="beforePostPublic">
	</div>
	<div class="row">
		<div class="col-sm-1 col-md-3"></div>
    	<div class="col-xs-12 col-sm-10 col-md-6">
			<?php require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/post_all.php'; ?>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>