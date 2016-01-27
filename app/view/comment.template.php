<?php
/*|--------------------------------------------------
  | TEMPLATE: Affichage Commentaire
  |--------------------------------------------------
*/
?>
<div class="row comment" id="commentairenum<?php echo $comment_id; ?>">
	<div class="media col-xs-12 div-comment" id="<?php echo $numcomment; ?>">
		<div class="media-left">
			<a href="/profil/<?php echo $comment_auteur; ?>" title="<?php echo $comment_auteur; ?>">
				<img class="img-circle img-comment media-object" src="<?php echo $hrefimgprofil; ?>" alt="<?php echo $comment_auteur; ?>">
			</a>
		</div>
		<div class="media-body">
		  	<blockquote class="blockquote-comment">
			    <p style="word-wrap: break-word;"><?php echo $comment_message; ?></p>
			        <small><cite><?php echo $comment_name; ?></cite>, <time class="timeago" datetime="<?php echo $datetimecomment; ?>"><?php echo $comment_date; ?></time><?php if($_SESSION['username'] == $comment_auteur) { ?>
						<div class="dropup" style="display: inline-block;">
							<a class="btn btn-flat btn-xs no_underline dropdown-toggle" type="button" id="dropdownMenu<?php echo $comment_id; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="margin: 0;padding: 3px 8px;">
								<i class="fa fa-angle-up"></i>
							</a>
							<ul class="dropdown-menu" aria-labelledby="dropdownMenu<?php echo $comment_id; ?>" style="padding: 2px;border: 1px solid rgba(0,0,0,.15);">
								<li><a href="#" id="<?php echo $comment_id; ?>" class="supprcomment" data-postid="<?php echo $post_id; ?>" title="Supprimer ce commentaire">Supprimer</a></li>
							</ul>
						</div>
			    <?php } ?></small>
		    </blockquote>
		</div>
	</div>
</div>