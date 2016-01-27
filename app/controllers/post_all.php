<?php
//****************************************************
// Controller: Affichage Post
//****************************************************
try {
	$requeteposts = $DB_con->prepare("SELECT * FROM posts WHERE public=:public ORDER BY id DESC");
	$requeteposts->execute(array(
        'public' => 1
    ));

	if($requeteposts->rowCount() > 0) {
		while($selectedPost = $requeteposts->fetch()) {
			$post_id = $selectedPost['id'];
			$post_user_name = $selectedPost['auteur'];

			$nameuser = $profil->get_profil_info_name($post_user_name);
			$post_name = $nameuser['prenomplusnom'];
			if(empty($post_name)) {
				$post_name = "Utilisateur introuvable";
			}

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