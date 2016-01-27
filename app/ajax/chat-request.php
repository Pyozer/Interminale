<?php
/* ############################# */
/* ##           CHAT          ## */
/* ############################# */

require $_SERVER['DOCUMENT_ROOT'].'/include/init.php';

/* Récuperer tous les msg du chat  */
if(isset($_POST['getmsgchat'])) {
	$getallmsg = $DB_con->prepare("SELECT * FROM chat ORDER BY id DESC LIMIT 60");
	$getallmsg->execute();

	$nb_resultats = $getallmsg->rowCount();
	if($nb_resultats != 0) {
		$donnees = array();
		while($resultline = $getallmsg->fetch(PDO::FETCH_ASSOC)) {
			$donnees[] = $resultline;
		}
		for($i = count($donnees)-1; $i >= 0; $i--) {
			$info_profil = $profil->get_profil_info($donnees[$i]['auteur']);
			$name = $info_profil['prenomplusnom'];
			$id = $donnees[$i]['id'];
			$msg = $donnees[$i]['message'];
			$auteur = $donnees[$i]['auteur'];
			$datemsgorigin = $donnees[$i]['date_msg'];
			$datemsg = AffDate($datemsgorigin);
			$datetimemsg = date('c', $datemsgorigin);

			include $_SERVER['DOCUMENT_ROOT'].'/app/view/chatmsg.template.php';
		}
	} else {
		echo '<div id="message" style="padding: 20px 5px 0 20px;font-size: 20px;"><p><strong>Aucun message :/</strong></p></div>';
	}
}

/**
* On enregistre message envoyé dans le chat et renvoi info user
**/
if(isset($_POST['sendchatmsg']) && isset($_POST['message'])) {
	$message = htmlspecialchars($_POST['message']);

	$envoimsg = $DB_con->prepare("INSERT INTO chat(message, auteur, date_msg) VALUES(:message, :auteur, :date_msg)");
	$envoimsg->execute(array(
		'message' => $message,
		'auteur' => $_SESSION['username'],
		'date_msg' => time()
	));

	$usermsg = $_SESSION['username'];
	if(!isset($_SESSION['prenomplusnom'])) {
		$info_profil_me = $profil->get_profil_info($usermsg);
		$_SESSION['prenomplusnom'] = $info_profil_me['prenomplusnom'];
	}
	$nameuser = $_SESSION['prenomplusnom'];
	$msg = $message;
	$dateactuel = time();
	$date = AffDate($dateactuel);

	$reponse = array('usermsg' => $usermsg, 'nameuser' => $nameuser, 'message' => $msg, 'date' => $date);
	echo json_encode($reponse);
}