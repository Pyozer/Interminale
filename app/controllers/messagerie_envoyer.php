<?php
//****************************************************
// Controller: Affichage message envoyés
//****************************************************
try {
	$requetemsg_sent = $DB_con->prepare("SELECT * FROM messages WHERE pseudo_exp=:pseudo_exp ORDER BY id DESC LIMIT 15");
	$requetemsg_sent->execute(array(
        'pseudo_exp' => $_SESSION['username']
    ));

	if($requetemsg_sent->rowCount() > 0) {
		while($selectedMsg = $requetemsg_sent->fetch()) {
			$id_msg = $selectedMsg['id'];
			$pseudo_dest = $selectedMsg['pseudo_dest'];
			$info_profil_dest = $profil->get_profil_info($pseudo_dest);
			$name_dest = $info_profil_dest['prenomplusnom'];

			$date_envoi = $selectedMsg['date_msg'];
			$date_envoi = AffDate($date_envoi, "message");

			$imgprofil_dest = $img->get_img_profil_post($pseudo_dest);

			include $_SERVER['DOCUMENT_ROOT'].'/app/view/messagerie_envoyer.template.php';
		}
	} else {
		include $_SERVER['DOCUMENT_ROOT'].'/app/view/no-msg.template.php';
	}
}
catch(PDOException $e)
{
   echo $e->getMessage();
}
?>