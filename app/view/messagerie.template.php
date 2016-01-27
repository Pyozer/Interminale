<a href="/messages/<?php echo $pseudo_usermsg; ?>" id="msgnum<?php echo $id_msg; ?>">
	<div class="row msg_list" style="margin-bottom: 0px;">
	    <div class="divmessage">
	        <div class="row">
	        	<div class="col-xs-9 col-sm-10">
	        		<div class="row">
	        			<div class="media col-xs-12">
			        		<div class="media-left media-middle">
			        			<img class="img-circle img-msg" src="<?php echo $imgprofil_usermsg; ?>">
			        		</div>
			        		<div class="media-body" style="vertical-align: middle;">
			        			<span><?php echo $name_user; ?></span>
					          	<div class="date_msg">
						        	<small><?php echo $date_envoi; ?></small>
						        </div>
		        			</div>
	        			</div>
	        		</div>
	        	</div>
	        	<div class="col-xs-3 col-sm-2">
		          	<div class="lu_or_not">
		          		<?php if($lu != 1) { ?>
			          		<p class="notread"><strong><?php echo $luornot; ?></strong></p>
			          	<?php } ?>
			        </div>
	        	</div>
	        </div>
	    </div>
	</div>
</a>