<?php
/* ############################# */
/* ##       PUBLICATIONS      ## */
/* ############################# */

require $_SERVER['DOCUMENT_ROOT'].'/include/init.php';

$erreur = '<div class="alert alert-danger alert-dismissible" role="alert" style="margin: 5px 0 0 0;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<strong>Une erreur est survenue, réessayez.</strong>
</div>';

/* Ajouter un post */
if(isset($_POST['postajax']) && isset($_POST['confajax']) && isset($_POST['addpost'])) {
	$post_content = htmlspecialchars($_POST['postajax']);
	$post_content = nl2br($post_content);
	$conf = htmlspecialchars($_POST['confajax']);

	if($conf == "friends") {
		$conf = "0";
	} else if($conf == "public") {
		$conf = "1";
	} else {
		$conf = "0";
	}
	$nb_lignes = substr_count($post_content, "\n");

	if(empty($post_content)) {
		$error = erreur('USER_NO_FIELDTEXT');
		$arr = array('status' => 0, 'err' => $error);
		echo json_encode($arr);
	} else {
		if($nb_lignes <= 20) {
			if(strlen($post_content) <= 700) {
				if($post->addpost($_SESSION['username'], $post_content, $conf)) {
					$post_info = $post->get_post_from_user($_SESSION['username']);

					$post_id = $post_info['id'];
					$post_user_name = $post_info['auteur'];

					$nameuser = $profil->get_profil_info_name($post_user_name);
					$post_name = $nameuser['prenom']." ".$nameuser['nom'];

					$post_message = $post_info['content'];
					$timestamp = $post_info['date_post'];
					$date = AffDate($timestamp, "post");
					$datetime = date('c', $timestamp);
					$public = $post_info['public'];
					$post_like = "0";
					$post_comments = "0";
					$likedornot = "";
					$hrefimgprofil = $img->get_img_profil_post($post_user_name);

					ob_start();
					include $_SERVER['DOCUMENT_ROOT'].'/app/view/post.template.php';
					$view = ob_get_clean();
					ob_end_flush();

					$arr = array('status' => 1, 'view' => $view);
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
		} else {
			$error = erreur('TOO_MANY_LINES');
			$arr = array('status' => 0, 'err' => $error);
			echo json_encode($arr);
		}
	}
}

/* Supprimer un post */
if(isset($_POST['post_id']) && isset($_POST['supprpost'])) {
	$id_post = htmlspecialchars($_POST['post_id']);
	if($post->supprpost($id_post, $_SESSION['username'])) {
		if($comment->suppr_all_comments($id_post)) {
			$arr = array('status' => 1);
			echo json_encode($arr);
		} else {
			$arr = array('status' => 0, 'err' => $erreur);
			echo json_encode($arr);
		}
	} else {
		$arr = array('status' => 0, 'err' => $erreur);
		echo json_encode($arr);
	}
}

/* Vérifie si pas de nouveau post */
if(isset($_POST['updateposts']) && isset($_POST['lastidpost'])) {
	$lastid = htmlspecialchars($_POST['lastidpost']);
	/* On stock le tabeau dans une variable */
	$friends = $amis->get_friends_with_user($_SESSION['username']);
	$classelist = $classe->get_user_classe($_SESSION['username']);

	$listaffiche = array_merge($friends, $classelist);

	$requeteposts = $DB_con->prepare("SELECT * FROM posts WHERE (auteur REGEXP(:auteurs) OR public=:public) AND id > :lastid ORDER BY id DESC");
	$requeteposts->execute(array(
        'auteurs' => implode('|',$listaffiche),
        'public' => 1,
        'lastid' => $lastid
    ));

	if($requeteposts->rowCount() > 0) {
		while($selectedPost = $requeteposts->fetch()) {
			$post_id = $selectedPost['id'];
			$post_user_name = $selectedPost['auteur'];

			$nameuser = $profil->get_profil_info_name($post_user_name);
			$post_name = $nameuser['prenomplusnom'];

			$post_message = $selectedPost['content'];
			$public = $selectedPost['public'];
			$date = $selectedPost['date_post'];
			$date = AffDate($date, "post");
			$img_post = $selectedPost['img_post'];
			$post_like = $like->get_nbr_likes($post_id);
			$post_comments = $comment->get_nbr_comments($post_id);
			$hrefimgprofil = $img->get_img_profil_post($post_user_name);
			if($like->verif_if_like($post_id, $_SESSION['userid'])) {
				$likedornot = "liked";
			} else {
				$likedornot = "";
			}

			ob_start();
			include $_SERVER['DOCUMENT_ROOT'].'/app/view/post.template.php';
			$view = ob_get_clean();
			ob_end_flush();

			$arr = array('status' => 1, 'view' => $view, 'auteur' => $post_name);
			echo json_encode($arr);
		}
	}
}
?>