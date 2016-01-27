<?php
//****************************************************
// Controller: Affichage des messages privés avec un utilisateur
//****************************************************
try {
	$requetemsg = $DB_con->prepare("SELECT * FROM (SELECT * FROM messages WHERE (pseudo_exp=:me AND pseudo_dest=:usermsg) OR (pseudo_exp=:usermsg AND pseudo_dest=:me) ORDER BY id DESC LIMIT 45) T1 ORDER BY id ASC");
	$requetemsg->execute(array(
        'me' => $_SESSION['username'],
        'usermsg' => $_GET['usermsg']
    ));
	if($requetemsg->rowCount() > 0) {
		$pseudo_me = $_SESSION['username'];
		if(!isset($_SESSION['prenomplusnom'])) {
			$info_profil_me = $profil->get_profil_info($pseudo_me);
			$_SESSION['prenomplusnom'] = $info_profil_me['prenomplusnom'];
		}
		if(!isset($_SESSION['imgprofil'])) {
			$_SESSION['imgprofil'] = $img->get_img_profil_post($pseudo_me);
		}
		$name_me = $_SESSION['prenomplusnom'];
		$imgprofil_me = $_SESSION['imgprofil'];

		$pseudo_usermsg = $_GET['usermsg'];
		$info_profil_pseudo_usermsg = $profil->get_profil_info($pseudo_usermsg);
		$name_pseudo_usermsg = $info_profil_pseudo_usermsg['prenomplusnom'];
		$imgprofil_pseudo_usermsg = $img->get_img_profil_post($pseudo_usermsg);

		while($selectedMsg = $requetemsg->fetch()) {
			$id_msg = $selectedMsg['id'];
			$id_conv = $selectedMsg['id_conv'];
			$msg = $selectedMsg['message'];
			$date = $selectedMsg['date_msg'];
			$date = AffDate($date, "message");

			if($selectedMsg['pseudo_dest'] == $pseudo_me) {
				$message->msg_put_read($id_msg);
			}
			if($selectedMsg['pseudo_exp'] == $pseudo_me) {
				include $_SERVER['DOCUMENT_ROOT'].'/app/view/message_me.template.php';
			} else if($selectedMsg['pseudo_exp'] == $pseudo_usermsg) {
				include $_SERVER['DOCUMENT_ROOT'].'/app/view/message_to_me.template.php';
			}
		}
	} else {
		$getlastconv = $DB_con->prepare("SELECT max(id_conv) AS id_conv FROM messages");
		$getlastconv->execute();
		$info = $getlastconv->fetch();
		$id_conv = (int) $info['id_conv'];
		$id_conv = $id_conv + 1;

		include $_SERVER['DOCUMENT_ROOT'].'/app/view/no-msgprive.template.php';
	}
}
catch(PDOException $e)
{
   echo $e->getMessage();
}
?>