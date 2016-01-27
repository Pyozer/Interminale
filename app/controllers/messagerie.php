<?php
//****************************************************
// Controller: Affichage conversation messages privés
//****************************************************
try {

	/* On récupère tous les messages concernant l'utilisateur */
	$requetemsg_list = $DB_con->prepare("SELECT * FROM messages WHERE (pseudo_dest=:me OR pseudo_exp=:me) ORDER BY date_msg DESC");
	$requetemsg_list->execute(array(
        'me' => $_SESSION['username']
    ));

	if($requetemsg_list->rowCount() > 0) {
		$lastid_conv = array();
		while($selectedMsg = $requetemsg_list->fetch()) {
			$id_msg = $selectedMsg['id'];
			$id_conv = $selectedMsg['id_conv'];

			$lastdate = $selectedMsg['date_msg'];
			$date_envoi = AffDate($lastdate, "message");

			$pseudo_dest = $selectedMsg['pseudo_dest'];
			$pseudo_exp = $selectedMsg['pseudo_exp'];

			if($pseudo_dest == $_SESSION['username']) {
				$pseudo_usermsg = $pseudo_exp;
			} else {
				$pseudo_usermsg = $pseudo_dest;
			}

			$checklu = $DB_con->prepare("SELECT lu FROM messages WHERE pseudo_dest=:me AND pseudo_exp=:usermsg AND id_conv=:id_conv AND lu=:lu ORDER BY date_msg DESC");
			$checklu->execute(array(
		        'me' => $_SESSION['username'],
		        'usermsg' => $pseudo_usermsg,
		        'id_conv' => $id_conv,
		        'lu' => 0
		    ));

			if($checklu->rowCount() > 0) {
				$lu = 0;
			} else {
				$lu = 1;
			}

			if($lu == 1) {
				$luornot = "";
			} else {
				$luornot = "Non lu";
			}

			if(!in_array($id_conv, $lastid_conv)) {
				$info_profil_usermsg = $profil->get_profil_info($pseudo_usermsg);
				if(empty($info_profil_usermsg)) {
					$name_user = "Utilisateur introuvable";
				} else {
					$name_user = $info_profil_usermsg['prenomplusnom'];
				}

				$imgprofil_usermsg = $img->get_img_profil_post($pseudo_usermsg);

				include $_SERVER['DOCUMENT_ROOT'].'/app/view/messagerie.template.php';
			}
			$lastid_conv[] = $id_conv;
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