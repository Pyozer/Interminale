<?php
class FICHIER
{
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

    public function uploadfile($namefile, $dir, $desc, $public) {
      // Constantes
      define('TARGET', '/var/www'.$dir);    // Repertoire cible
      define('MAX_SIZE', 20971520);    // Taille max en octets du fichier
       
      // Tableaux de donnees
      $tabExt = array('exe','php','js','py'); //Extension non autorisé
       
      // Variables
      $extension = '';
      $message = '';
      $nomFichier = '';
       
      /************************************************************
       * Script d'upload
       *************************************************************/
        // On verifie si le champ est rempli
        if(!empty($_FILES[$namefile]['name'])) {
          // Recuperation de l'extension du fichier
          $extension  = pathinfo($_FILES[$namefile]['name'], PATHINFO_EXTENSION);
       
          // On verifie l'extension du fichier
          if(!in_array(strtolower($extension),$tabExt)) {
              // On verifie la taille du fichier
              if((filesize($_FILES[$namefile]['tmp_name']) <= MAX_SIZE)) {
                // Parcours du tableau d'erreurs
                if(isset($_FILES[$namefile]['error']) && UPLOAD_ERR_OK === $_FILES[$namefile]['error']) {
                  // On renomme le fichier
                  $nomdufile = basename($_FILES[$namefile]['name'], ".".$extension);
                  $nomFichier = $nomdufile.'-'.substr(uniqid(), -4).".".$extension;
                  // Si c'est OK, on teste l'upload
                  if(move_uploaded_file($_FILES[$namefile]['tmp_name'], TARGET.$nomFichier)) {

                    $ajoutfichier = $this->db->prepare("INSERT INTO fichier(nom,description,auteur,date_fichier,public) VALUES(:nom, :description, :auteur, :date_fichier, :public)");
                    $ajoutfichier->execute(array(
                        'nom' => $nomFichier,
                        'description' => $desc,
                        'auteur' => $_SESSION['username'],
                        'date_fichier' => time(),
                        'public' => $public
                    ));
                    $returnarray = array('status' => 1, 'nomfichier' => $nomFichier);
                    return $returnarray;
                  } else {
                    // Sinon on affiche une erreur systeme
                    $message = 'Problème lors de l\'upload !';
                    $returnarray = array('status' => 0, 'err' => $message);
                    return $returnarray;
                  }
                } else {
                  $message = 'Une erreur interne a empêché l\'upload du fichier '.$_FILES[$namefile]['error'];
                  $returnarray = array('status' => 0, 'err' => $message);
                  return $returnarray;
                }
              } else {
                // Sinon erreur sur les dimensions et taille de l'image
                $message = 'Le fichier doit faire moins de 20 Mo !';
                $returnarray = array('status' => 0, 'err' => $message);
                return $returnarray;
              }
          } else {
            // Sinon on affiche une erreur pour l'extension
            $message = 'L\'extension du fichier est interdite !';
            $returnarray = array('status' => 0, 'err' => $message);
            return $returnarray;;
          }
      }
    }
}
?>