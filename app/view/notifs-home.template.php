<?php
if(isset($anniv)) { ?>
	<div class="alert alert-blue-gray alert-dismissible fade in" role="alert" style="font-size: 15px;">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close" onClick="avoidanniv();" style="font-weight: 500;font-size: 13px;text-shadow: none;filter: none;opacity: .5;"><span aria-hidden="true"><i class="mdi-action-visibility-off" title="Ne plus afficher"></i></span></button>
      <strong><i class="fa fa-birthday-cake" style="margin-right: 10px;"></i> C'est l'anniversaire de <?php echo $anniv; ?> !</strong>
    </div>
<?php }

if(isset($event)) { ?>
<div class="alert alert-blue-gray alert-dismissible fade in" role="alert" style="font-size: 15px;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close" onClick="avoidevent();" style="font-weight: 500;font-size: 13px;text-shadow: none;filter: none;opacity: .5;"><span aria-hidden="true"><i class="mdi-action-visibility-off" title="Ne plus afficher"></i></span></button>
	<?php for ($i = 0; $i < count($event); $i++) { ?>
	      <strong><i class="fa fa-calendar" style="margin-right: 10px;"></i> <?php echo $event[$i]['title_event']; ?> !</strong>
	      <?php if(!empty($event[$i]['desc_event'])) { ?>
		      <a role="button" data-toggle="collapse" href="#collapseEvent<?php echo $event[$i]['id_event']; ?>" aria-expanded="false" aria-controls="collapseEvent<?php echo $event[$i]['id_event']; ?>" style="margin-left: 5px;"><i class="fa fa-info-circle"></i></a>
		      <div class="collapse" id="collapseEvent<?php echo $event[$i]['id_event']; ?>">
				<blockquote class="blockquote-help event-blockquote">
					<p class="help-block text-justify" style="padding: 10px 0;color: #eaeaea;"><?php echo $event[$i]['desc_event']; ?></p>
				</blockquote>
			  </div>
		  <?php } ?>
		  <br />
	<?php } ?>
	</div>
<?php } ?>