<?php
//****************************************************
// Controller: Affichage Post user
//****************************************************
try {

	if($_SESSION['username'] == $user_id) {
		$requetepostsuser = $DB_con->prepare("SELECT * FROM posts WHERE auteur=:auteur ORDER BY id DESC");
		$requetepostsuser->execute(array(
	        'auteur' => $user_id
	    ));
	} else if($verif_friend) {
		$requetepostsuser = $DB_con->prepare("SELECT * FROM posts WHERE auteur=:auteur ORDER BY id DESC");
		$requetepostsuser->execute(array(
	        'auteur' => $user_id
	    ));
	} else {
		$requetepostsuser = $DB_con->prepare("SELECT * FROM posts WHERE auteur=:auteur AND public=:public ORDER BY id DESC");
		$requetepostsuser->execute(array(
			'public' => "1",
	        'auteur' => $user_id
	    ));

	}

	if($requetepostsuser->rowCount() > 0) {
		while($selectedPost = $requetepostsuser->fetch()) {
			$post_id = $selectedPost['id'];
			$post_user_name = $selectedPost['auteur'];

			$nameuser = $profil->get_profil_info_name($post_user_name);
			$post_name = $nameuser['prenomplusnom'];

			$post_message = $selectedPost['content'];
			$public = $selectedPost['public'];
			$timestamp = $selectedPost['date_post'];
			$date = AffDate($timestamp, "post");
			$datetime = date('c', $timestamp);
			$img_post = $selectedPost['img_post'];
			$post_like = $like->get_nbr_likes($post_id);
			$post_comments = $comment->get_nbr_comments($post_id);
			$hrefimgprofil = $img->get_img_profil_post($post_user_name);
			
			if($like->verif_if_like($post_id, $_SESSION['userid'])) {
					$likedornot = "liked";
			} else {
				$likedornot = "";
			}

			include $_SERVER['DOCUMENT_ROOT'].'/app/view/post.template.php';
		}
	} else {
		include $_SERVER['DOCUMENT_ROOT'].'/app/view/no-post.template.php';
	}
}
catch(PDOException $e)
{
   echo $e->getMessage();
}
?>