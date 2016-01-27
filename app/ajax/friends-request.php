<?php
/* ############################# */
/* ##         Friends         ## */
/* ############################# */

require $_SERVER['DOCUMENT_ROOT'].'/include/init.php';

/* Demande d'amis à quelqu'un */
if(isset($_POST['usertoadd']) && isset($_POST['addfriend'])) {
	$user_to_add = htmlspecialchars($_POST['usertoadd']);
	$me = $_SESSION['username'];
	if($user_to_add != $me) {
		if($result = $amis->add_friend($me, $user_to_add)) {
			if($result['status'] == 1) {
				if($result['alreadyask'] == 1) {
					$result = '<button type="button" class="btn btn-default btn-flat friendbtn" name="supprfriend" id="supprfriend" data-user="'.$user_to_add.'" style="text-transform: none;">Supprimer des amis</button>';
				} else {
					$result = '<button type="button" class="btn btn-success" style="margin: 10px 1px 0 1px;">Demande envoyée</button><br/><button type="button" class="btn btn-default btn-flat btn-sm friendbtn" name="annuldemand" id="annuldemand" data-user="'.$user_to_add.'" style="text-transform: none;">Annuler la demande</button>';
				}
				/* Info profil du destinataire */
				$info_profil_dest = $profil->get_profil_info($user_to_add);
				$id_user_dest = $info_profil_dest['id'];
			    $name_dest = $info_profil_dest['prenomplusnom'];
			    $mail_dest = $info_profil_dest['email'];
			    $sendmailok = $info_profil_dest['notifMailPrive'];

			    $pseudo_exp = $me;

				if($user->user_connected($id_user_dest)) {
					$arr = array('status' => 1, 'pseudo_exp' => $me, 'pseudo_dest' => $user_to_add, 'view' => $result);
					echo json_encode($arr);
				} else {
				    if($sendmailok == "true") {
				    	$name_exp = $_SESSION['userpseudo'];

				    	require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/phpmailer/PHPMailerAutoload.php';

				    	// Retrieve the email template required
						$msg_html = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/view/email-friendask.template.html');

						// Replace the % with the actual information
						$msg_html = str_replace('%name_exp%', $name_exp, $msg_html);
						$msg_html = str_replace('%name_dest%', $name_dest, $msg_html);
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
			            $mail->Subject = "Demande d'ami de ".$name_exp." - Interminale";
			            $mail->Body = $msg_html;

			            $mail->AltBody = strip_tags($msg_html);

			            if (!$mail->send()) {
			                $arr = array('status' => 1, 'pseudo_exp' => $me, 'pseudo_dest' => $user_to_add, 'view' => $result);
							echo json_encode($arr);
			            } else {
			                $arr = array('status' => 1, 'pseudo_exp' => $me, 'pseudo_dest' => $user_to_add, 'view' => $result);
							echo json_encode($arr);
			            }
				    } else {
						$arr = array('status' => 1, 'pseudo_exp' => $me, 'pseudo_dest' => $user_to_add, 'view' => $result);
						echo json_encode($arr);
					}
				}
			} else {
				$arr = array('status' => 0);
				echo json_encode($arr);
			}
		} else {
			$arr = array('status' => 0);
			echo json_encode($arr);
		}
	} else {
		$arr = array('status' => 0);
		echo json_encode($arr);
	}
}

/* Annuler demande d'amis à quelqu'un */
if(isset($_POST['usertodelete']) && isset($_POST['annulfriend'])) {
	$usertodelete = htmlspecialchars($_POST['usertodelete']);
	$me = $_SESSION['username'];
	if($usertodelete != $me) {
		if($amis->delete_friend($me, $usertodelete)) {
			
			$result = '<button type="button" class="btn btn-outline btn-outline-primary friendbtn" name="addfriend" id="addfriend" data-user="'.$usertodelete.'"><i class="fa fa-user-plus"></i> Ajouter en ami</button>';
			
			$arr = array('status' => 1, 'view' => $result);
			echo json_encode($arr);
		} else {
			$arr = array('status' => 0);
			echo json_encode($arr);
		}
	} else {
		$arr = array('status' => 0);
		echo json_encode($arr);
	}
}

/* Supprimer de ses amis quelqu'un */
if(isset($_POST['usertodelete']) && isset($_POST['supprfriend'])) {
	$usertodelete = htmlspecialchars($_POST['usertodelete']);
	$me = $_SESSION['username'];
	if($usertodelete != $me) {
		if($amis->delete_friend($me, $usertodelete)) {

			$result = '<button type="button" class="btn btn-outline btn-outline-primary friendbtn" name="addfriend" id="addfriend" data-user="'.$usertodelete.'"><i class="fa fa-user-plus"></i> Ajouter en ami</button>';
			
			$arr = array('status' => 1, 'view' => $result);
			echo json_encode($arr);
		} else {
			$arr = array('status' => 0);
			echo json_encode($arr);
		}
	} else {
		$arr = array('status' => 0);
		echo json_encode($arr);
	}
}

/* Accepter amis */
if(isset($_POST['idinvitation']) && isset($_POST['pseudo_exp']) && isset($_POST['acceptfriend'])) {
	$idinvitation = htmlspecialchars($_POST['idinvitation']);
	$friendtoaccept = htmlspecialchars($_POST['pseudo_exp']);
	if($amis->accept_demand($idinvitation, $friendtoaccept)) {
		$arr = array('status' => 1);
		echo json_encode($arr);
	} else {
		$arr = array('status' => 0);
		echo json_encode($arr);
	}
}

/* Refuser amis */
if(isset($_POST['idinvitation']) && isset($_POST['pseudo_exp']) && isset($_POST['deniedfriend'])) {
	$idinvitation = htmlspecialchars($_POST['idinvitation']);
	$friendtoaccept = htmlspecialchars($_POST['pseudo_exp']);
	if($amis->denied_demand($idinvitation, $friendtoaccept)) {
		$arr = array('status' => 1);
		echo json_encode($arr);
	} else {
		$arr = array('status' => 0);
		echo json_encode($arr);
	}
}
?>