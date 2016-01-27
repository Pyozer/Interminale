<?php
/* ############################# */
/* ##       COMMENTAIRES      ## */
/* ############################# */

require $_SERVER['DOCUMENT_ROOT'].'/include/init.php';

$erreur = '<div class="alert alert-danger alert-dismissible" role="alert" style="margin: 5px 0 0 0;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<strong>Une erreur est survenue, réessayez.</strong>
</div>';

/* Ajouter un commentaire */
if(isset($_POST['post_id']) && isset($_POST['comment']) && isset($_POST['addcomment'])) {
	$id = htmlspecialchars($_POST['post_id']);
	$commentaire = htmlspecialchars($_POST['comment']);
	$commentaire = nl2br($commentaire);
	if(empty($commentaire)) {
		$error = erreur('USER_NO_FIELDTEXT');
		$arr = array('status' => 0, 'err' => $error);
		echo json_encode($arr);
	} else {
		if(strlen($commentaire) <= 450) {
			if($comment->addcomment($id, $_SESSION['username'], $commentaire)) {
				$post_id = $id;
				$comment_auteur = $_SESSION['username'];

				$nameuser = $profil->get_profil_info_name($comment_auteur);
				$comment_name = $nameuser['prenomplusnom'];
				
				$comment_message = $commentaire;
				$timestamp = time();
				$comment_date = AffDate($timestamp, "comment");
				$datetimecomment = date('c', $timestamp);
				$hrefimgprofil = $img->get_img_profil_post($comment_auteur);

				$nbrcomments = $comment->get_nbr_comments($post_id);

				$last_comment = $comment->get_last_comment_user($post_id, $comment_auteur);
				$comment_id = $last_comment['id'];
				$numcomment = $comment_id;

				ob_start();
				include $_SERVER['DOCUMENT_ROOT'].'/app/view/comment.template.php';
				$view = ob_get_clean();
				ob_end_flush();

				$arr = array('status' => 1, 'view' => $view, 'nbrcomment' => $nbrcomments);
				echo json_encode($arr);
			} else {
				$arr = array('status' => 0, 'err' => $erreur);
				echo json_encode($arr);
			}
		} else {
			$error = erreur('TOO_MANY_CARACT_COMMENT');
			$arr = array('status' => 0, 'err' => $error);
			echo json_encode($arr);

		}
	}
}

/* Afficher plus */
if(isset($_POST['post_id']) && isset($_POST['lastcomment']) && isset($_POST['loadmore'])) {
	$id = htmlspecialchars($_POST['post_id']);
	$lastcomment = (int) htmlspecialchars($_POST['lastcomment']);
	$nbrcomments = $comment->get_nbr_comments($id);

	$requeteoldcomments = $DB_con->prepare("SELECT * FROM comments WHERE id_post=:id_post ORDER BY id DESC LIMIT ".$lastcomment.", 5");
	$requeteoldcomments->execute(array(
        'id_post' => $id
    ));
    $numcomment = $lastcomment;
	while($selectedComment = $requeteoldcomments->fetch()) {
		$post_id = $id;
		$comment_id = $selectedComment['id'];
		$comment_auteur = $selectedComment['auteur'];
		$nameuser = $profil->get_profil_info_name($comment_auteur);
		$comment_name = $nameuser['prenomplusnom'];
		$comment_message = $selectedComment['content'];
		$comment_date = $selectedComment['date_comment'];
		/*$comment_date = strftime('%d %B %Y à %H:%M', $comment_date);*/
		$comment_date = AffDate($comment_date, "comment");
		$hrefimgprofil = $img->get_img_profil_post($comment_auteur);
		$numcomment = $numcomment + 1;
		include $_SERVER['DOCUMENT_ROOT'].'/app/view/comment.template.php';
	}
	if($nbrcomments > $numcomment) { ?>
		<div id="btnloadmore<?php echo $id; ?>" class="centered">
			<button type="button" class="btn btn-outline-primary btn-sm" id="loadmore" style="border-radius: 25px;" onClick="afficher_plus(<?php echo $id; ?>);">Afficher plus</button>
		</div>
	<?php }
}

/* Supprimer un commentaire */
if(isset($_POST['comment_id']) && isset($_POST['post_id']) && isset($_POST['supprcomment'])) {
	$id_comment = htmlspecialchars($_POST['comment_id']);
	$id_post = htmlspecialchars($_POST['post_id']);
	if($comment->supprcomment($id_comment, $_SESSION['username'])) {
		$nbrcomments = $comment->get_nbr_comments($id_post);

		$arr = array('status' => 1, 'nbrcomment' => $nbrcomments);
		echo json_encode($arr);
	} else {
		$arr = array('status' => 0, 'err' => $erreur);
		echo json_encode($arr);
	}
}
?>