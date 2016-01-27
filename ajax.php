<?php
require $_SERVER['DOCUMENT_ROOT'].'/include/init.php';

$erreur = '<div class="alert alert-danger alert-dismissible" role="alert" style="margin: 5px 0 0 0;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<strong>Une erreur est survenue, réessayez.</strong>
</div>';

/* On vérifie si pas de nouvelle demande d'amis ou msg privé */
if(isset($_POST['updatenotif'])) {
	$nbrmsgnonlu = $message->get_nbr_non_lus($_SESSION['username']);

	if($nbrmsgnonlu > 0) {
    	$newmsg = $nbrmsgnonlu;
    } else {
    	$newmsg = 0;
    }

    $nbrdemandfriend = $amis->nbr_ask_demande($_SESSION['username']);
    if($nbrdemandfriend > 0) {
    	$newfriend = $nbrdemandfriend;
    } else {
    	$newfriend = 0;
    }

    $arr = array('status' => 1, 'nbrnewmsg' => $newmsg, 'nbrnewfriend' => $newfriend);

	echo json_encode($arr);
}

/* ############################# */
/* ##        RECHERCHES       ## */
/* ############################# */

/* Barre de Recherche  */
if(isset($_GET['search']) && isset($_GET['dosearch'])) {
	$search = htmlspecialchars($_GET['search']);

	$requetesearch = $DB_con->prepare("SELECT * FROM users WHERE allowFindSearch=:allowFindSearch AND ((nom LIKE :search) OR (prenom LIKE :search) OR (prenomplusnom LIKE :search))");
	$requetesearch->execute(array(
		"allowFindSearch" => "true",
		"search" => "%$search%"
	));
	$nb_resultats = $requetesearch->rowCount();
	$users = array();

	if($nb_resultats != 0) {
		while($donnees = $requetesearch->fetch(PDO::FETCH_ASSOC)) {
			$hrefimgprofil = $img->get_img_profil_post($donnees['pseudo']);
			$name = $donnees['prenom']." ".$donnees['nom'];
			$users[] = array('pseudo' => $name, 'imageprofil' => $hrefimgprofil, 'href' => $donnees['pseudo']);
		}

		echo json_encode($users);
	}
}

/* ############################# */
/* ##         AUTRES          ## */
/* ############################# */

/* On valide la présence de l'utilisateur */
if(isset($_POST['id_user']) && isset($_POST['validco'])) {
	$id_user = $_POST['id_user'];

	$user->valid_user_connected($id_user);

	$arr = array('status' => 1);
	echo json_encode($arr);
}

/* On valide la présence d'un utilisateur autre */
if(isset($_POST['id_user']) && isset($_POST['validco_other'])) {
	$id_user = $_POST['id_user'];

	if($user->user_connected($id_user)) {
		$result = 1;
	} else {
		$result = 0;
	}

	$arr = array('status' => $result);
	echo json_encode($arr);
}

/* On valide la présence d'un utilisateur autre */
if(isset($_POST['id_user']) && isset($_POST['checkuserco'])) {
	$id_user = $_POST['id_user'];

	$listuserco = $user->all_user_connected();
	echo '<ul class="list-group">';
	$nbrco = count($listuserco);
	if($nbrco > 1) { $pluriel = "s"; } else { $pluriel = ""; }
	echo '<li class="list-group-item active" style="border-radius: 0!important;">Utilisateur'.$pluriel.' connecté'.$pluriel.' <span class="badge">'.$nbrco.'</span></li>';
	if(!empty($listuserco)) {
		foreach ($listuserco as $key => $iduser) {
			$info_profiluser = $profil->get_profil_info($iduser);
			$nameuserco = $info_profiluser['prenomplusnom'];
			$pseudo_user = $info_profiluser['pseudo'];

			$msgreturn = '<a href="/profil/'.$pseudo_user.'" class="list-group-item">';
			$msgreturn .= '<i class="fa fa-circle" id="user_connected" title="Connecté" style="font-size: 10px;margin-bottom: 2px;margin-right: 5px;"></i> ';
			$msgreturn .= $nameuserco;
			$msgreturn .= '</a>';

		    echo $msgreturn;
		}
	} else {
		echo '<li class="list-group-item" style="border-radius: 0!important;">Aucun utilisateur de connecté</li>';
	}
	echo '</ul>';
}

/**
* On envoi les informations de l'utilisateur
**/
if(isset($_POST['getinfouser'])) {
	if(!isset($_SESSION['username'])) {
		$username = "null";
	} else {
		$username = $_SESSION['username'];
	}
	if(!isset($_SESSION['userid'])) {
		$userid = "null";
	} else {
		$userid = $_SESSION['userid'];
	}
	$arr = array('user' => $username, 'iduser' => $userid);
	echo json_encode($arr);
}
?>