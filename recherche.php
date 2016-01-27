<?php
$titre_page = "Recherche";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/home');
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
	<div class="container" id="firstcontainer">
		<div class="row">
			<div class="col-xs-1 col-sm-2 col-md-3"></div>
			<div class="col-xs-10 col-sm-8 col-md-6">
				<?php
				if(isset($_GET['search']) && $_GET['search'] != NULL) // on vérifie d'abord l'existence du POST et aussi si la requete n'est pas vide.
				{
					$requete = htmlspecialchars($_GET['search']); // on crée une variable $requete pour faciliter l'écriture de la requete SQL, mais aussi pour empecher les éventuels malins qui utiliseraient du PHP ou du JS, avec la fonction htmlspecialchars().
					$requete = str_replace ('%20',' ', $requete);
					$requete = str_replace ('+',' ', $requete);
					$query = $DB_con->prepare("SELECT * FROM users WHERE (nom LIKE :search) OR (prenom LIKE :search) OR (prenomplusnom LIKE :search)");
					$query->execute(array(
						"search" => "%$requete%"
					));
					$nb_resultats = $query->rowCount(); // on utilise la fonction mysql_num_rows pour compter les résultats pour vérifier par après
					?>
					<h1 class="page-header" style="font-weight: 400;word-wrap: break-word;font-size: 30px;">Résultat<?php if($nb_resultats > 1) { echo 's'; } ?> pour "<?php echo $requete; ?>":</h1>
					<div class="row">
						<div class="col-xs-12">
							<?php if($nb_resultats != 0) { ?>
								<p>Nous avons trouvé <?php echo $nb_resultats; if($nb_resultats > 1) { echo ' résultats.'; } else { echo ' résultat.'; } ?></p>
								<br/>
								<?php while($donnees = $query->fetch()) {
									$hrefimgprofil = $img->get_img_profil_post($donnees['pseudo']);
									$user_name = $donnees['prenom']." ".$donnees['nom'];
								?>
								<div class="result_user_search">
									<a class="no_underline" href="/profil/<?php echo $donnees['pseudo']; ?>" title="<?php echo $donnees['pseudo']; ?>">
										<img class="img-circle img_result_search" src="<?php echo $hrefimgprofil; ?>" alt="<?php echo $donnees['pseudo']; ?>">
										<h4 class="title_user_search"><?php echo $user_name; ?></h4>
									</a>
								</div>
								<?php } ?>
								<br/>
								<br/>
								<hr>
							<?php } else { ?>
								<h3>Pas de résultat</h3>
								<p>Nous n'avons trouvé aucun résultat pour votre requête "<? echo $_GET['search']; ?>".</p>
							<?php }
				} ?>
					</div>
				</div>
			</div>
		<br />
		</div>
	</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>