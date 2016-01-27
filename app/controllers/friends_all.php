<?php
//****************************************************
// Controller: Affichage Des amis
//****************************************************
try {
	if(empty($listdesamis)) {
		$listdesamis = "null";
	}
	
	$requetefriends = $DB_con->prepare("SELECT * FROM users WHERE pseudo REGEXP(:pseudo) ORDER BY prenom ASC");
	$requetefriends->execute(array(
        'pseudo' => implode('|',$listdesamis)
    ));

	if($requetefriends->rowCount() > 0) {
		while($selectedFriend = $requetefriends->fetch()) {
			$id_user = $selectedFriend['id'];
			$pseudo_user = $selectedFriend['pseudo'];
			$name_user = $selectedFriend['prenomplusnom'];
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