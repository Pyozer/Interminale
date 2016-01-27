<?php
/* ############################# */
/* ##        EVENEMENTS       ## */
/* ############################# */

require $_SERVER['DOCUMENT_ROOT'].'/include/init.php';

$erreur = '<div class="alert alert-danger alert-dismissible" role="alert" style="margin: 5px 0 0 0;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<strong>Une erreur est survenue, réessayez.</strong>
</div>';

/* Ajouter un evenement */
if(isset($_POST['name_event']) && isset($_POST['desc_event']) && isset($_POST['date_event']) && isset($_POST['type_event']) && isset($_POST['public_event']) && isset($_POST['addevent'])) {
	$name_event = htmlspecialchars($_POST['name_event']);
	$desc_event = htmlspecialchars($_POST['desc_event']);
	$date_event = htmlspecialchars($_POST['date_event']);
	$type_event = htmlspecialchars($_POST['type_event']);
	$public_event = htmlspecialchars($_POST['public_event']);

	if(empty($name_event) || empty($date_event) || empty($type_event) || empty($public_event)) {
		$error = erreur('USER_NO_FIELDTEXT');
		$arr = array('status' => 0, 'err' => $error);
		echo json_encode($arr);
	} else {
		if(strlen(utf8_decode($name_event)) <= 60 && strlen(utf8_decode($desc_event)) <= 100) {
			$date_event = explode('/', $date_event);
			$date_event = $date_event[2]."-".$date_event[1]."-".$date_event[0];

			if($type_event == "DS") {
				$date_event = $date_event." 13:50:00";
			} else if($type_event == "DM") {
				$date_event = $date_event." 08:10:00";
			} else {
				$date_event = $date_event." 17:30:00";
			}

			if($public_event == "friends") {
				$public_event = "0";
			} else if($public_event == "public") {
				$public_event = "1";
			} else {
				$public_event = "0";
			}
	
			if($classe->addevent($name_event, $desc_event, $date_event, $type_event, $_SESSION['userclasse'], $public_event)) {
				$arr = array('status' => 1);
				echo json_encode($arr);
			} else {
				$arr = array('status' => 0, 'err' => $erreur);
				echo json_encode($arr);
			}
		} else {
			$error = erreur('TOO_MANY_CARACT_POST');
			$arr = array('status' => 0, 'err' => $error);
			echo json_encode($arr);
		}
	}
}

/* Ne plus afficher l'anniversaire */
if(isset($_POST['dontseeanniv'])) {
	$_SESSION['avoidanniv'] = true;
}

/* Ne plus afficher l'evenement */
if(isset($_POST['dontseeevent'])) {
	$_SESSION['avoidevent'] = true;
}
?>