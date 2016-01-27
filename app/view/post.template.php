<div class="row post" style="margin-bottom: 45px;" id="postnum<?php echo $post_id; ?>">
    <div class="col-xs-12" id="postdiv">
        <div class="header_contenu">
          <img class="img-circle" src="<?php echo $hrefimgprofil; ?>" alt="<?php echo $post_user_name; ?>">
          <span id="auteur_post">
            <a href="/profil/<?php echo $post_user_name; ?>" style="color: inherit;"><?php echo $post_name; ?></a>
            <span style="font-size: 13px;">
              <?php if($public == 1) { echo '<i class="fa fa-globe" title="Public"></i>'; } else { echo '<i class="fa fa-graduation-cap" title="Classe/Amis"></i>'; } ?>
            </span>
          </span>
          <?php if($_SESSION['username'] == $post_user_name) { ?>
            <div class="dropup" style="display: inline-block;">
              <a class="btn btn-flat btn-xs no_underline dropdown-toggle" id="dropdownMenu<?php echo $post_id; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="margin: 0;padding: 3px 8px;">
                <i class="fa fa-angle-up"></i>
              </a>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenu<?php echo $post_id; ?>" style="padding: 2px;border: 1px solid rgba(0,0,0,.15);">
                <li><a href="#" id="supprpost<?php echo $post_id; ?>" class="supprpost" data-postid="<?php echo $post_id; ?>" title="Supprimer la publication">Supprimer</a></li>
              </ul>
            </div>
          <?php } ?>
        </div>
        <div class="contenu" style="max-height: none;">
            <p><?php echo $post_message; ?></p>
            <?php if(isset($img_post)) { 
              if(!empty($img_post)) { ?>
                <img src="<?php echo $img_post; ?>" style="overflow: hidden;max-width: 100%;max-height: 400px;margin-bottom: 10px;" id="imgpost" data-lightbox="img<?php echo $post_id; ?>">
            <?php }
            }?>
        </div>
        <!-- Date du post !-->
        <div class="date_post" style="text-align: right;margin: 0 10px 0 0;">
          <small><time class="timeago" datetime="<?php echo $datetime; ?>"><?php echo $date; ?></time></small>
        </div>
        <!-- Caption -->
        <div class="description" id="description<?php echo $post_id; ?>">
          <div class="content_desc">
              <span id="nbrlike<?php echo $post_id; ?>"><?php echo $post_like; ?></span> <button type="button" class="likebutton <?php echo $likedornot; ?>" id="addlike<?php echo $post_id; ?>" data-toggle="tooltip" data-placement="top" data-postid="<?php echo $post_id; ?>" style="margin-right: 10px;"><i class="mdi-action-favorite"></i></button>

              <span id="nbrcomment<?php echo $post_id; ?>"><?php echo $post_comments; ?></span> <button type="button" class="commentbutton" data-toggle="collapse" data-target="#comment<?php echo $post_id; ?>" aria-expanded="true" aria-controls="comment<?php echo $post_id; ?>"><i class="mdi-communication-forum
"></i></button>
          </div>
        </div>
        <div class="collapse" id="comment<?php echo $post_id; ?>">
            <hr>
            <?php require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/comment.php'; ?>
        </div>
    </div>
</div>