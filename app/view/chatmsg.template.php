<div id="message" style="padding: 20px 5px 0px 20px;">
	<a href="/profil/<?php echo $auteur; ?>" class="no_underline" id="auteurmsg">
		<strong><?php echo $name; ?></strong>
	</a>
	<small style="font-style: italic;"><time class="timeago" datetime="<?php echo $datetimemsg; ?>"><?php echo $datemsg; ?></time></small>

	<p id="<?php echo $id; ?>" style="word-wrap: break-word;"><?php echo $msg; ?></p>
</div>