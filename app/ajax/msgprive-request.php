<?php
/* ############################# */
/* ##      MESSAGES PRIVE     ## */
/* ############################# */

require $_SERVER['DOCUMENT_ROOT'].'/include/init.php';

$erreur = '<div class="alert alert-danger alert-dismissible" role="alert" style="margin: 5px 0 0 0;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<strong>Une erreur est survenue, réessayez.</strong>
</div>';

/* Envoyer un message privé */
if(isset($_POST['msg_content']) && isset($_POST['pseudo_dest']) && isset($_POST['id_conv']) && isset($_POST['addmsg'])) {
	$msg_content = htmlspecialchars($_POST['msg_content']);
	$msg_content = nl2br($msg_content);
	$pseudo_dest = htmlspecialchars($_POST['pseudo_dest']);
	$id_conv = htmlspecialchars($_POST['id_conv']);

	$nb_lignes = substr_count($msg_content, "\n");
	if(empty($msg_content)) {
		$error = erreur('USER_NO_FIELDTEXT');
		$arr = array('status' => 0, 'err' => $error);
		echo json_encode($arr);
	} else {
		if($nb_lignes <= 25) {
			if(strlen($msg_content) <= 800) {
				if($message->envoi_msg($_SESSION['username'], $pseudo_dest, $msg_content, $id_conv)) {
					/* Info profil du destinataire */
					$info_profil_dest = $profil->get_profil_info($pseudo_dest);
					$id_user_dest = $info_profil_dest['id'];
				    $name_dest = $info_profil_dest['prenomplusnom'];
				    $mail_dest = $info_profil_dest['email'];
				    $sendmailok = $info_profil_dest['notifMailPrive'];

				    $pseudo_exp = $_SESSION['username'];

					if($user->user_connected($id_user_dest)) {
						$arr = array('status' => 1, 'pseudo_dest' => $pseudo_dest, 'pseudo_exp' => $pseudo_exp);
						echo json_encode($arr);
					} else {
					    if($sendmailok == "true") {
					    	$name_exp = $_SESSION['userpseudo'];

					    	require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/phpmailer/PHPMailerAutoload.php';

					    	// Retrieve the email template required
							$msg_html = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/view/email-msgprive.template.html');

							// Replace the % with the actual information
							$msg_html = str_replace('%name_exp%', $name_exp, $msg_html);
							$msg_html = str_replace('%name_dest%', $name_dest, $msg_html);
							$msg_html = str_replace('%msg_content%', $msg_content, $msg_html);
							$msg_html = str_replace('%pseudo_exp%', $pseudo_exp, $msg_html);

				            $mail = new PHPMailer;
				            $mail->isSMTP();
				            $mail->SMTPDebug = 0;
				            $mail->Debugoutput = 'html';
				            $mail->Host = 'smtp.gmail.com';
				            $mail->Port = 587;
				            $mail->SMTPSecure = 'tls';
				            $mail->SMTPAuth = true;
				            $mail->Username = "enjoycraftfr@gmail.com";
				            $mail->Password = "~Py+Ai3j(O5g8!";
				            $mail->isHTML(true);
				            $mail->CharSet = 'UTF-8';
				            $mail->setFrom('no-reply@interminale.fr', 'Interminale');
				            $mail->addReplyTo($mail_dest);
				            $mail->addAddress($mail_dest);
				            $mail->Subject = 'Message privé de '.$name_exp.' - Interminale';
				            $mail->Body = $msg_html;

				            $mail->AltBody = strip_tags($msg_html);

				            if (!$mail->send()) {
				                $arr = array('status' => 1, 'pseudo_dest' => $pseudo_dest, 'pseudo_exp' => $pseudo_exp);
								echo json_encode($arr);
				            } else {
				                $arr = array('status' => 1, 'pseudo_dest' => $pseudo_dest, 'pseudo_exp' => $pseudo_exp);
								echo json_encode($arr);
				            }
					    } else {
							$arr = array('status' => 1, 'pseudo_dest' => $pseudo_dest, 'pseudo_exp' => $pseudo_exp);
							echo json_encode($arr);
						}
					}
				} else {
					$arr = array('status' => 0, 'err' => "Une erreur est survenue");
					echo json_encode($arr);
				}
			} else {
				$error = erreur('TOO_MANY_CARACT_MSG');
				$arr = array('status' => 0, 'err' => $error);
				echo json_encode($arr);
			}
		} else {
			$error = erreur('TOO_MANY_LINES_MSG');
			$arr = array('status' => 0, 'err' => $error);
			echo json_encode($arr);
		}
	}
}

/* Récupérer les nouveaux msg privés  */
if(isset($_POST['lastmsgid']) && isset($_POST['id_conv']) && isset($_POST['update_msg'])) {
	$lastid = $_POST['lastmsgid'];
	$id_conv = $_POST['id_conv'];

	$updatemsg = $DB_con->prepare("SELECT * FROM messages WHERE id_conv=:id_conv AND id > :id ORDER BY id ASC");
	$updatemsg->execute(array(
		'id_conv' => $id_conv,
		'id' => $lastid
	));

	$nb_resultats = $updatemsg->rowCount();
	if($nb_resultats > 0) {
		$donnees = array();
		while($resultline = $updatemsg->fetch(PDO::FETCH_ASSOC)) {
			$donnees[] = $resultline;
		}
		for($i = count($donnees)-1; $i >= 0; $i--) {
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

			$pseudo_usermsg = $donnees[$i]['pseudo_exp'];
			$info_profil_pseudo_usermsg = $profil->get_profil_info($pseudo_usermsg);
			$name_pseudo_usermsg = $info_profil_pseudo_usermsg['prenomplusnom'];
			$imgprofil_pseudo_usermsg = $img->get_img_profil_post($pseudo_usermsg);

			$id_msg = $donnees[$i]['id'];
			$id_conv = $donnees[$i]['id_conv'];
			$msg = $donnees[$i]['message'];
			$date = $donnees[$i]['date_msg'];
			$date = AffDate($date, "message");

			if($donnees[$i]['pseudo_dest'] == $pseudo_me) {
				$message->msg_put_read($id_conv);
			}
			if($donnees[$i]['pseudo_exp'] == $pseudo_me) {
				ob_start();
				include $_SERVER['DOCUMENT_ROOT'].'/app/view/message_me.template.php';
				$view = ob_get_clean();
				ob_end_flush();
			} else {
				ob_start();
				include $_SERVER['DOCUMENT_ROOT'].'/app/view/message_to_me.template.php';
				$view = ob_get_clean();
				ob_end_flush();
			}

			$arr = array('status' => 1, 'view' => $view);
			echo json_encode($arr);
		}
	} else {
		$arr = array('status' => 0);
		echo json_encode($arr);
	}
}
?>