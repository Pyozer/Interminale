<?php
$titre_page = "Erreur ".$_GET['err'];
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

switch($_GET['err'])
{
	case '400':
		$page_erreur = 'Échec de l\'analyse HTTP.';
		break;
	case '401':
		$page_erreur = 'Le pseudo ou le mot de passe n\'est pas correct !';
		break;
	case '402':
		$page_erreur = 'Le client doit reformuler sa demande avec les bonnes données de paiement.';
		break;
	case '403':
		$page_erreur = 'Requête interdite !';
		break;
	case '404':
		$page_erreur = 'La page n\'existe pas ou plus !';
		break;
	case '405':
		$page_erreur = 'Méthode non autorisée.';
		break;
	case '500':
		$page_erreur = 'Erreur interne au serveur ou serveur saturé.';
		break;
	case '501':
		$page_erreur = 'Le serveur ne supporte pas le service demandé.';
		break;
	case '502':
		$page_erreur = 'Mauvaise passerelle.';
		break;
	case '503':
		$page_erreur = 'Service indisponible.';
		break;
	case '504':
		$page_erreur = 'Trop de temps à la réponse.';
		break;
	case '505':
		$page_erreur = 'Version HTTP non supportée.';
		break;
	default:
		$page_erreur = 'Erreur !';
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="checkbacksoon">
		<p><span class="go3d"><?php if(empty($_GET['err'])) { echo "Erreur"; } else { echo $_GET['err']; } ?></span></p>
		
		<p class="error">
			<strong><?php echo $page_erreur; ?></strong><br>
			Alors calme toi narvalo, ça arrive à tout le monde.<br>
			Les liens au dessus t'aideront à te remettre sur le chemin.<br>
			Tu peux aussi cliquer sur le bouton pour revenir à l'accueil.
		</p>
		
		<div class="row centered">
			<div class="col-md-3"></div>
			<div class="col-xs-12 col-md-6">
				<a type="button" class="btn btn-outline-gray" href="/" title="Retour à l'accueil">Retour au site</a>					
			</div>
		</div>   
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>