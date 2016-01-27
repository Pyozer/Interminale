<?php
$titre_page = "Amis";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/home');
}

$listdesamis = $amis->get_friends($_SESSION['username']);
if(empty($listdesamis)) {
	$nbramis = "0";
} else {
	$nbramis = count($listdesamis);
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-2 col-md-3"></div>
		<div class="col-xs-12 col-sm-8 col-md-6" id="divclasseorfriend">
			<h1 class="page-header">Mes amis <span class="badge"><?php echo $nbramis; ?></span></h1>
				<?php if($amis->nbr_ask_demande($_SESSION['username'])) {
					require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/friend_ask_demand.php';
				}
				?>
			<div class="row">
				<div class="list-group" style="margin-bottom: 0;">
					<?php require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/friends_all.php'; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>