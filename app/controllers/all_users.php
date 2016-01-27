<?php
//****************************************************
// Controller: Affichage Des utilisateurs connectés
//****************************************************
$listusers = array();

if($nbrusers > 0) {
	while($selectedMembre = $getListMembres->fetch()) {
        $id_user = $selectedMembre['id'];
		$pseudo_user = $selectedMembre['pseudo'];
		$name_user = $selectedMembre['prenomplusnom'];
		$hrefimgprofil = $img->get_img_profil_post($pseudo_user);

		include $_SERVER['DOCUMENT_ROOT'].'/app/view/friendslist.template.php';
	}
} else {
	include $_SERVER['DOCUMENT_ROOT'].'/app/view/no-users.template.php';
}
?>