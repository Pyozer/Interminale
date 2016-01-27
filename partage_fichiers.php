<?php
$titre_page = "Partage de fichiers";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/home');
}
$friendslist = $amis->get_friends($_SESSION['username']);
$classelist = $classe->get_user_classe_with_user($_SESSION['username']);

$listaffiche = array_merge($friendslist, $classelist);

$info_profil = $profil->get_profil_info($_SESSION['username']);
$classe = $info_profil['classe'];

if($classe == "terminale-s") {
	$classe_name = "Terminale S";
} else if($classe == "terminale-es") {
	$classe_name = "Terminale ES";
} else {
	$classe_name = "Terminale L";
}

/* Publication du fichier */
if(isset($_POST['descfile']) && isset($_POST['conf'])) {
    $descfile = htmlspecialchars($_POST['descfile']);
    $conf = htmlspecialchars($_POST['conf']);

    if($conf == "friends") {
      $conf = "0";
    } else if($conf == "public") {
      $conf = "1";
    } else {
      $conf = "0";
    }

    if(empty($descfile) || empty($_FILES['fileinput']['name'])) {
      $error = erreur('USER_NO_FIELDTEXT');
      setFlash($error, "danger");
    } else {
        if(strlen(utf8_decode($descfile)) <= 55) {
          if($result = $fichier->uploadfile('fileinput', '/uploads/sharefiles/', $descfile, $conf)) {
              if($result['status'] != 1) {
              	$error = $result['err'];
                setFlash($error, "danger");
              } else {
                  $success = "Le fichier à bien été partagé";
                  setFlash($success, "success");
                  $user->redirect('/partage_fichiers');
              }
          } else {
            $error = erreur('FAIL_UPLOAD_FILE');
            setFlash($error, "danger");
          }
        } else {
          $error = "Trop de caractères pour la description (50 max)";
          setFlash($error, "danger");
        }
    }
}

if(isset($_GET['id']) && isset($_GET['auteur']) && isset($_GET['date_fichier']) && isset($_GET['supprfile'])) {
	$ifexistingfiles = $DB_con->prepare("SELECT * FROM fichier WHERE id=:id AND auteur=:auteur AND date_fichier=:date_fichier LIMIT 1");
	$ifexistingfiles->execute(array(
		'id' => $_GET['id'],
		'auteur' => $_GET['auteur'],
		'date_fichier' => $_GET['date_fichier']
	));

	if($ifexistingfiles->rowCount() > 0) {
		$result = $ifexistingfiles->fetch();
		$chemin_fichier = $_SERVER['DOCUMENT_ROOT'].'/uploads/sharefiles/'.$result['nom'];
		$access_file = '/uploads/sharefiles/'.$result['nom'];
		if(file_exists($chemin_fichier)) {
				if(unlink($chemin_fichier)) {
					$deletefiles = $DB_con->prepare("DELETE FROM fichier WHERE id=:id AND auteur=:auteur AND date_fichier=:date_fichier LIMIT 1");
					$deletefiles->execute(array(
						'id' => $_GET['id'],
						'auteur' => $_GET['auteur'],
						'date_fichier' => $_GET['date_fichier']
					));
					setFlash('Le fichier a bien été supprimé', 'success');
					$user->redirect('/partage_fichiers');
				} else {
					setFlash('Une erreur est survenu lors de la suppression du fichier :/', 'danger');
					$user->redirect('/partage_fichiers');
				}
		} else {
			setFlash('Le fichier ne semble pas être présent...', 'danger');
			$user->redirect('/partage_fichiers');
		}
	} else {
		$user->redirect('/partage_fichiers');
	}
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
	<div class="row">
		<div class="col-sm-1 col-md-2"></div>
		<div class="col-xs-12 col-sm-10 col-md-8">
			<h1 class="page-header">Partage de fichiers <span style="float: right;"><button type="button" class="btn btn-outline btn-outline-gray" data-toggle="modal" data-target="#addfilemodal">Ajouter</button></span></h1>
			<?php flash();

			$requetefile = $DB_con->prepare("SELECT * FROM fichier WHERE auteur REGEXP(:auteurs) OR public=:public ORDER BY id DESC");
		    $requetefile->execute(array(
	        	'auteurs' => implode('|', $listaffiche),
	        	'public' => 1
	        ));

	        if($requetefile->rowCount() <= 0) {
	        	include $_SERVER['DOCUMENT_ROOT'].'/app/view/no-file.template.php';
	        } else {
			?>
			<div class="table-responsive">
				<table class="table table-striped table-material" data-toggle="table" data-pagination="true" data-side-pagination="client" data-page-list="[5, 10, 20, 50, 100]" data-page-size="20" data-search="true" style="box-shadow: -0px -0px 5px 1px rgba(0,0,0,0.1);background-color: #fafafa;">
			      <thead>
			        <tr>
			          <th data-field="titre" data-sortable="true" style="max-width: 140px;">Nom</th>
			          <th data-field="desc" data-sortable="true">Description</th>
			          <th data-field="auteur" data-sortable="true">Auteur</th>
			          <th data-field="date" data-sortable="true">Date</th>
			          <th data-field="action" data-sortable="false">Action</th>
			        </tr>
			      </thead>
			      <tbody>
			      <?php
					while($selectedFile = $requetefile->fetch()) {
						$id = $selectedFile['id'];
						$nom = $selectedFile['nom'];
						$description = $selectedFile['description'];
						$auteur = $selectedFile['auteur'];
						$timestamp_fichier = $selectedFile['date_fichier'];
						$public = $selectedFile['public'];

						if($auteur == $_SESSION['username']) {
							$supprok = '<a type="button" href="?id='.$id.'&auteur='.$auteur.'&date_fichier='.$timestamp_fichier.'&supprfile=ok" title="Supprimer le fichier" style="color: #f44336;"><i class="mdi-navigation-cancel"></i></a>';
						} else {
							$supprok = '';
						}

						$extensionget = new SplFileInfo($nom);
						$extension = $extensionget->getExtension();

						if($extension == "jpg" || $extension == "png" || $extension == "jpeg" || $extension == "gif" || $extension == "pdf") {
							$previewok = '<a type="button" href="/uploads/sharefiles/'.$nom.'" title="Voir en ligne" target="_blank" style="color: #444;"><i class="mdi-image-image"></i></a>';
						} else {
							$previewok = '';
						}

						$info_profil = $profil->get_profil_info($auteur);
						$auteur = $info_profil['prenomplusnom'];
						$date_fichier = strftime('%d %B %Y à %H:%M', $timestamp_fichier);
						?>
						<tr>
			          		<td><?php echo $nom; ?></td>
				          	<td><?php echo $description; ?></td>
				          	<td><?php echo $auteur; ?></td>
				          	<td><span class="hidden"><?php echo $timestamp_fichier; ?></span><?php echo $date_fichier; ?></td>
				          	<td>
				          		<div class="col-xs-4">
				          			<a href="/uploads/sharefiles/<?php echo $nom; ?>" download="<?php echo $nom; ?>" title="Télécharger le fichier" style="color: #444;"><i class="mdi-file-file-download"></i></a>
				          		</div>
				          		<div class="col-xs-4"><?php echo $previewok; ?></div>
				          		<div class="col-xs-4"><?php echo $supprok; ?></div>
				          	</td>
				        </tr>
						<?php
					}
			      ?>
			      </tbody>
			    </table>
			</div>
			<?php } ?>
		</div>

		<!-- Formulaire d'ajout -->
		<div class="modal fade" id="addfilemodal" tabindex="-1" role="dialog" aria-labelledby="addfilemodal">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h3 class="modal-title">Partager un fichier</h3>
		      </div>
		      <form method="post" class="form-horizontal" id="form-addfile" accept-charset="UTF-8" enctype="multipart/form-data">
			      <div class="modal-body">
		              <fieldset>
		              	  <br />
		              	  <span style="font-weight: 400;font-size: 18px;">Description courte:</span>
		              	  <div class="row">
	                        <div class="col-xs-12">
	                        	<input class="form-control" id="descfile" name="descfile" placeholder="Description courte (50 caractères max)" maxlength="50" style="max-width: 100%;" required>
	                        </div>
	                      </div>
	                      <br />
	                      <span style="font-weight: 400;font-size: 18px;">Le fichier à partager:</span>
	                      <div class="row">
	                          <div class="col-xs-12" style="margin-top: 25px;">
	                              <div class="fileinput fileinput-new input-group" data-provides="fileinput">
									  <span class="input-group-addon btn btn-file btn-success" style="padding: 10px 12px;">
									  		<span class="fileinput-new">Selectionner un fichier</span>
									  		<span class="fileinput-exists">Modifier</span>
									  		<input type="hidden" name="MAX_FILE_SIZE" value="20971520" />
									  		<input type="file" name="fileinput" id="fileinput">
									  </span>
									  <a href="#" class="input-group-addon btn fileinput-exists btn-flat" data-dismiss="fileinput" style="padding: 10px 12px;">Supprimer</a>
									  <div class="form-control" data-trigger="fileinput">
									  		<i class="fa fa-file fileinput-exists"></i>
									  		<span class="fileinput-filename"></span>
									  </div>
									</div>
	                          </div>
	                          <div class="col-xs-12">
	                              <span class="help-block" id="errorpost" style="color: red;font-weight: 500;"></span>
	                          </div>
	                      </div>
	                      <br />
	                      <span style="font-weight: 400;font-size: 18px;">Partager à:</span>
	                      <div class="form-group">
	                          <div class="radio radio-primary" style="display: inline-block;">
	                              <label>
	                                <input type="radio" class="inputConf" name="conf" value="friends" checked>
	                                Classe / Amis
	                              </label>
	                          </div>
	                          <div class="radio radio-success" style="margin-left: 20px;display: inline-block;">
	                              <label>
	                                <input type="radio" class="inputConf" name="conf" value="public">
	                                Public
	                              </label>
	                          </div>
	                      </div>
		              </fieldset>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
			        <button type="submit" class="btn btn-info" id="submitaddfile">Partager</button>
			      </div>
		      </form>
		    </div>
		  </div>
		</div>
	</div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>