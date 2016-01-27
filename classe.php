<?php
$titre_page = "Classe";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/home');
}

$listclasse = $classe->get_user_classe($_SESSION['username']);
$nbrclasse = count($listclasse);

if(!isset($_SESSION['userclasse'])) {
	$info_profil = $profil->get_profil_info($_SESSION['username']);
	$classe = $info_profil['classe'];
} else {
	$classe = $_SESSION['userclasse'];
}

/* On récupère evenements */
$firstdate = date('Y-m-d H:i:s');
$lastdate = date('Y-m-d H:i:s', strtotime('+3 month'));

$checkevent = $DB_con->prepare("SELECT * FROM evenements WHERE (date_event BETWEEN :firstdate AND :lastdate) AND (classe=:classe OR public=:public) ORDER BY date_event ASC");
$checkevent->execute(array(
	'firstdate' => $firstdate,
	'lastdate' => $lastdate,
	'classe' => $_SESSION['userclasse'],
	'public' => 1
));
if($checkevent->rowCount() > 0) {
	$event = array();
	while($resultevent = $checkevent->fetch()) {
		$id_event = $resultevent['id'];
		$nom_event = $resultevent['name_event'];
		$desc_event = $resultevent['desc_event'];
		$date_event = $resultevent['date_event'];
		$type_event = $resultevent['type_event'];

		$date_event = strtotime($date_event);
		$date_event = strftime('%A %d %B', $date_event);

		if($type_event == "DS") {
			if($nom_event == "Histoire" || $nom_event == "Anglais" || $nom_event == "L.V.2" || $nom_event == "SVT") {
				$dapos = "d'";
			} else {
				$dapos = "de ";
			}
			$title_event = "DS ".$dapos.$nom_event." le ".$date_event;
		} else if($type_event == "DM") {
			if($nom_event == "Histoire" || $nom_event == "Anglais" || $nom_event == "L.V.2" || $nom_event == "SVT") {
				$dapos = "d'";
			} else {
				$dapos = "de ";
			}
			$title_event = "DM ".$dapos.$nom_event." pour le ".$date_event;
		} else {
			$title_event = $nom_event." le ".$date_event;
		}
		$event[] = array('id_event' => $id_event, 'title_event' => $title_event, 'nom_event' => $nom_event, 'desc_event' => $desc_event, 'date_event' => $date_event, 'type_event' => $type_event);
	}
}

$classe = explode("-", $classe);
$classe = $classe[0]." ".$classe[1];
$classe = ucwords($classe);
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-2 col-md-3"></div>
		<div class="col-xs-12 col-sm-8 col-md-6" id="divclasseorfriend">
			<h1 class="page-header">Ma classe <span class="badge"><?php echo $nbrclasse; ?></span> <small><?php echo $classe; ?></small></h1>
			<div class="row centered" style="margin-bottom: 15px;">
				<button class="btn btn-outline btn-outline-gray" data-toggle="modal" data-target="#addevent">Ajouter un évènement</button>
				<a role="button" class="btn btn-outline btn-outline-primary" data-toggle="collapse" href="#collapseEvent" aria-expanded="false" aria-controls="collapseEvent">Evenements à venir</a>
				<div class="collapse" id="collapseEvent">
					<?php
					if(!isset($event)) {
						echo '<p style="padding: 10px 0;"><strong>Aucun évenement de prévu</strong></p>';
					} else { ?>
					<div class="table-responsive">
						<table class="table text-left">
						<caption>Liste des évenements pour les 3 mois à venir</caption>
					      <tbody>
							<?php for ($i = 0; $i < count($event); $i++) { ?>
							  <tr>
							  	<td>
							      <strong><i class="fa fa-calendar" style="margin-right: 10px;"></i> <?php echo $event[$i]['title_event']; ?> !
							      <?php if(!empty($event[$i]['desc_event'])) { ?>
							      	<a role="button" data-toggle="collapse" href="#collapseEvent<?php echo $event[$i]['id_event']; ?>" aria-expanded="false" aria-controls="collapseEvent<?php echo $event[$i]['id_event']; ?>" style="margin-left: 5px;"><i class="fa fa-info-circle"></i></a></strong>
							        <div class="collapse" id="collapseEvent<?php echo $event[$i]['id_event']; ?>">
										<blockquote class="blockquote-help event-blockquote">
											<p class="help-block" style="padding: 15px 0 15px 0;"><?php echo $event[$i]['desc_event']; ?></p>
										</blockquote>
								    </div>
								  <?php } ?>
								</td>
							  </tr>
							<?php } ?>
						  </tbody>
					    </table>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="row">
				<div class="list-group" style="margin-bottom: 0;">
					<?php require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/classe_all.php'; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Ajouter un event -->
	<div id="addevent" class="modal fade" tabindex="-1">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	        <h3 class="modal-title">Ajouter un évenement</h3>
	      </div>
	      <form method="post">
	      <div class="modal-body">
              <fieldset>
              	  <div class="row">
						<div class="col-xs-12">
                  			<span class="help-block" id="errorpost" style="text-align: center;color: red;font-weight: 500;"></span>
                   		</div>
              	  </div>
              	  <div class="row">
                    <div class="col-xs-12 col-md-6" style="margin-bottom: 25px;">
              	  		<span style="font-size: 18px;">Nom</span>
                    	<input type="text" class="form-control" id="name_event" name="name_event" placeholder="Nom (ex: Anglais)" maxlength="50" style="max-width: 100%;" required>
                    </div>
                    <div class="col-xs-12 col-md-6" style="margin-bottom: 25px;">
              	  		<span style="font-size: 18px;">Date</span>
                    	<input type="text" class="form-control" id="date_event" name="date_event" placeholder="jj/mm/aaaa" data-dtp="dtp_tuUYg" style="max-width: 100%;" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-12" style="margin-bottom: 25px;">
                  		<span style="font-size: 18px;">Informations supplémentaires</span>
                    	<input type="text" class="form-control" id="desc_event" name="desc_event" placeholder="Informations suplémentaires (facultatif)" maxlength="110" style="max-width: 100%;">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-12" style="margin-bottom: 25px;">
                  		<span style="font-size: 18px;">Type d'évenement</span>
                    	<select class="form-control" name="type_event" id="type_event">
		                  <option value="DS">Devoir surveillé</option>
		                  <option value="DM">Devoir Maison</option>
		                  <option value="OTHER">Autre</option>
		                </select>
                    </div>
                  </div>
                  <div class="row">
                  	<div class="col-xs-12">
	                  <div class="form-group">
                          <div class="radio radio-primary" style="display: inline-block;">
                              <label>
                                <input type="radio" class="public_event" name="public_event" value="friends" checked>
                                Classe
                              </label>
                          </div>
                          <div class="radio radio-success" style="margin-left: 20px;display: inline-block;">
                              <label>
                                <input type="radio" class="public_event" name="public_event" value="public">
                                Public
                              </label>
                          </div>
                      </div>
                    </div>
                  </div>
              </fieldset>
	      </div>
	      <div class="modal-footer">
	        <button class="btn btn-flat" data-dismiss="modal">Annuler</button>
	        <button type="submit" class="btn btn-outline btn-outline-primary" id="submit_event">Ajouter</button>
	      </div>
	      </form>
	    </div>
	  </div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>