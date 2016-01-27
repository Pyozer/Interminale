<?php
//****************************************************
// Controller: Affichage commentaires
//****************************************************

if(connect()) { ?>
    <div class="row" id="addcomment<?php echo $post_id; ?>">
	    <div class="col-xs-12 div-comment">
	      	<form method="post" class="formaddcomment" id="<?php echo $post_id; ?>">
		      	<div class="col-xs-12 col-sm-9">
					<input type="text" class="form-control" id="commentinput<?php echo $post_id; ?>" placeholder="Ajouter votre commentaire">
					<span class="help-block" id="errorcomment" style="color: red;font-weight: 500;"></span>
		      	</div>
		      	<div class="col-xs-12 col-sm-3">
					<button type="submit" class="btn btn-outline btn-outline-gray btn-sm submitnewcomment" data-postid="<?php echo $post_id; ?>">Valider</button>
		      	</div>
		    </form>
		</div>
    </div>
<?php
}
try {
	$nbrcomment = $comment->get_nbr_comments($post_id);

	if($nbrcomment < 5) {
		$requetecomment = $DB_con->prepare("SELECT * FROM comments WHERE id_post=:id_post ORDER BY id DESC");
		$requetecomment->execute(array(
	        'id_post' => $post_id
	    ));
	} else {
		$requetecomment = $DB_con->prepare("SELECT * FROM comments WHERE id_post=:id_post ORDER BY id DESC LIMIT 5");
		$requetecomment->execute(array(
	        'id_post' => $post_id
	    ));
	}
	$numcomment = 0;
	while($selectedComment = $requetecomment->fetch()) {
		$comment_id = $selectedComment['id'];
		$comment_auteur = $selectedComment['auteur'];

		$nameuser = $profil->get_profil_info_name($comment_auteur);
		$comment_name = $nameuser['prenomplusnom'];

		$comment_message = $selectedComment['content'];
		$timestamp = $selectedComment['date_comment'];
		$comment_date = AffDate($timestamp, "comment");
		$datetimecomment = date('c', $timestamp);
		$hrefimgprofil = $img->get_img_profil_post($comment_auteur);
		$numcomment = $numcomment + 1;
		include $_SERVER['DOCUMENT_ROOT'].'/app/view/comment.template.php';
	}
}
catch(PDOException $e)
{
   echo $e->getMessage();
}
if($nbrcomment > 5) { ?>
	<div id="btnloadmore<?php echo $post_id; ?>" class="centered">
		<button type="button" class="btn btn-outline-primary btn-sm" id="loadmore" style="border-radius: 25px;" onClick="afficher_plus(<?php echo $post_id; ?>);">Afficher plus</button>
	</div>
<?php } ?>