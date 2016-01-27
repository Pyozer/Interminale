<div class="col-xs-12 col-sm-6" id="demand<?php echo $friend_id; ?>">
	<div class="row" style="margin-bottom: 0px;">
	    <div style="background-color: #fff;padding: 10px 10px 10px 15px;">
	        <div class="header_contenu">
		        <div class="row">
		        	<div class="col-xs-12">
		        		<a class="no_underline" href="/profil/<?php echo $pseudo_exp; ?>">
			        		<img class="circle" src="<?php echo $hrefimgprofil; ?>" alt="icon" style="display: inline-block;width: 45px;height: 45px;border-radius: 50%;margin-right: 10px;">
				        </a>
				        <a class="link no_underline" href="/profil/<?php echo $pseudo_exp; ?>">
				          	<span style="display: inline-block;font-size: 18px;vertical-align: middle;">
				           		<?php echo $name_exp; ?>
				          	</span>
				        </a>
		        	</div>
		        	<div class="col-xs-12">
			          	<button class="btn btn-success btn-sm btn-block" id="acceptDemand" data-idinv="<?php echo $friend_id; ?>" data-pseudoexp="<?php echo $pseudo_exp; ?>" data-username="<?php echo $name_exp; ?>" style="margin: 5px 0 0 0;width: 48%;display: inline-block;">Accepter</button>
			          	<button class="btn btn-danger btn-sm btn-block" id="deniedDemand" data-idinv="<?php echo $friend_id; ?>" data-pseudoexp="<?php echo $pseudo_exp; ?>" style="margin: 5px 0 0 0;width: 48%;display: inline-block;">Refuser</button>
		        	</div>
		        </div>
	        </div>
	    </div>
	</div>
</div>