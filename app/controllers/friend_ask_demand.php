<div class="row" style="margin-bottom: 25px;">
	<div class="list-group" style="margin-bottom: 0;">
		<?php
		try {
			$requeteamisask = $DB_con->prepare("SELECT * FROM amis WHERE pseudo_dest=:pseudo_dest AND active=:active ORDER BY id_invitation DESC");
			$requeteamisask->execute(array(
				'pseudo_dest' => $_SESSION['username'],
				'active' => "0"
			));

			$nbrask = $requeteamisask->rowCount();
			if($nbrask > 0) {
				while($selectedFriend = $requeteamisask->fetch()) {
					$friend_id = $selectedFriend['id_invitation'];
					$pseudo_exp = $selectedFriend['pseudo_exp'];
					
					$nameuser = $profil->get_profil_info_name($pseudo_exp);
					$name_exp = $nameuser['prenomplusnom'];

					$pseudo_dest = $selectedFriend['pseudo_dest'];
					$hrefimgprofil = $img->get_img_profil_post($pseudo_exp);

					include $_SERVER['DOCUMENT_ROOT'].'/app/view/friend_ask.template.php';
				}
			}
		}
		catch(PDOException $e)
		{
		   echo $e->getMessage();
		}
		?>
	</div>
</div>