<?php
/* ############################# */
/* ##     LIKE  ET  UNLIKE    ## */
/* ############################# */

require $_SERVER['DOCUMENT_ROOT'].'/include/init.php';

$erreur = '<div class="alert alert-danger alert-dismissible" role="alert" style="margin: 5px 0 0 0;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<strong>Une erreur est survenue, réessayez.</strong>
</div>';

if(isset($_POST['id']) && isset($_POST['addlike'])) {
	$id = htmlspecialchars($_POST['id']);

	if($like->addlike($id, $_SESSION['userid'])) {
		$post_like = $like->get_nbr_likes($id);

		$arr = array('status' => 1, 'nbrlike' => $post_like);
		echo json_encode($arr);
	} else {
		if($like->unlike($id, $_SESSION['userid'])) {
			$post_like = $like->get_nbr_likes($id);

			$arr = array('status' => 1, 'nbrlike' => $post_like);
			echo json_encode($arr);
		} else {
			$arr = array('status' => 0, 'err' => $erreur);
			echo json_encode($arr);
		}
	}
}

if(isset($_POST['idpost']) && isset($_POST['getuserlike'])) {
	$idpost = htmlspecialchars($_POST['idpost']);

	$getuserid = $DB_con->prepare("SELECT likes FROM posts WHERE id=:idpost");
	$getuserid->execute(array(
		'idpost' => $idpost
	));
	$nb_resultats = $getuserid->rowCount();
	$result = $getuserid->fetch();
	$resultusers = $result['likes'];

	if(empty($resultusers) || strlen($resultusers) == 0) {
		echo "Aucun like";
	} else if($nb_resultats > 0) {
		$alluser = explode(',', $resultusers);
		$lastusernull = array_pop($alluser);

		$getusername = $DB_con->prepare("SELECT prenomplusnom FROM users WHERE id REGEXP(:ids)");
		$getusername->execute(array(
			'ids' => implode('|',$alluser)
		));

		while($resultuser = $getusername->fetch()) {
			echo $resultuser['prenomplusnom'];
			echo "<br />";
		}
	} else {
		echo "Aucun like";
	}
}
?>