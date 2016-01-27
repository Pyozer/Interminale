<?php
if(!isset($_SESSION['avoidanniv'])) {
	$checkanniv = $DB_con->prepare("SELECT prenom FROM users WHERE DATE_FORMAT(datedenaissance,'%m-%d') LIKE DATE_FORMAT(CURDATE(), '%m-%d') ORDER BY datedenaissance");
	$checkanniv->execute();
	if($checkanniv->rowCount() > 0) {
		$useranniv = array();
		while($resultanniv = $checkanniv->fetch()) {
			$useranniv[] = $resultanniv['prenom'];
		}
		if(count($useranniv) == 2) {
			$anniv = implode(' et ',$useranniv);
		} else {
			$anniv = implode(', ',$useranniv);
		}
	}
}

if(!isset($_SESSION['avoidevent'])) {
	$firstdate = date('Y-m-d H:i:s');
	$lastdate = date('Y-m-d H:i:s', strtotime('+6 days'));

	$checkevent = $DB_con->prepare("SELECT * FROM evenements WHERE (date_event BETWEEN :firstdate AND :lastdate) AND (classe=:classe OR public=:public) ORDER BY date_event ASC");
	$checkevent->execute(array(
		'firstdate' => $firstdate,
		'lastdate' => $lastdate,
		'classe' => $_SESSION['userclasse'],
		'public' => 1
	));
	if($checkevent->rowCount() > 0) {
		$event = array();
		while($resultevent = $checkevent->fetch()) {
			$id_event = $resultevent['id'];
			$nom_event = $resultevent['name_event'];
			$desc_event = $resultevent['desc_event'];
			$date_event = $resultevent['date_event'];
			$type_event = $resultevent['type_event'];

			$date_event = strtotime($date_event);
			$date_event = strftime('%A %d %B', $date_event);

			$date_today = strftime('%A %d %B', time());
			$date_tomorrow = strftime('%A %d %B', strtotime('+1 days'));
			if($date_event == $date_today) {
				$today = 2;
			} else if($date_event == $date_tomorrow) {
				$today = 1;
			} else {
				$today = 0;
			}

			if($type_event == "DS" || $type_event == "DM") {
				if($nom_event == "Histoire" || $nom_event == "Anglais" || $nom_event == "L.V.2" || $nom_event == "Histoire") {
					$dapos = "d'";
				} else {
					$dapos = "de ";
				}
				if($type_event == "DM") {
					$det = "pour";
				} else {
					$det = "";
				}
				if($today == 2) {
					$title_event = $type_event." ".$dapos.$nom_event." ".$det." aujourd'hui";
				} else if($today == 1) {
					$title_event = $type_event." ".$dapos.$nom_event." ".$det." demain";
				} else {
					$title_event = "Bientôt ".$type_event." ".$dapos.$nom_event." ".$det." le ".$date_event;
				}
			} else {
				if($today == 2) {
					$title_event = $nom_event." ajourd'hui";
				} else if($today == 1) {
					$title_event = $nom_event." demain";
				} else {
					$title_event = $nom_event." le ".$date_event;
				}
			}
			$event[] = array('id_event' => $id_event, 'title_event' => $title_event, 'nom_event' => $nom_event, 'desc_event' => $desc_event, 'date_event' => $date_event, 'type_event' => $type_event);
		}
	}
}

require $_SERVER['DOCUMENT_ROOT'].'/app/view/notifs-home.template.php';
?>