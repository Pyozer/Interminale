<?php
//****************************************************
// Controller: Affichage De la classe
//****************************************************
try {
	$requeteclasse = $DB_con->prepare("SELECT id,pseudo,prenomplusnom FROM users WHERE pseudo REGEXP(:pseudo) ORDER BY prenom ASC");
	$requeteclasse->execute(array(
        'pseudo' => implode('|',$listclasse)
    ));

	if($requeteclasse->rowCount() > 0) {
		while($selectedMembreClasse = $requeteclasse->fetch()) {
			$id_user = $selectedMembreClasse['id'];
			$pseudo_user = $selectedMembreClasse['pseudo'];
			$name_user = $selectedMembreClasse['prenomplusnom'];
			$hrefimgprofil = $img->get_img_profil_post($pseudo_user);

			include $_SERVER['DOCUMENT_ROOT'].'/app/view/friendslist.template.php';
		}
	} else {
		include $_SERVER['DOCUMENT_ROOT'].'/app/view/no-friends.template.php';
	}
}
catch(PDOException $e)
{
   echo $e->getMessage();
}
?>