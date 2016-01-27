<?php
/*|-------------------------------------------------
  | Class pour gérer les images
  | Upload, 
  |-------------------------------------------------
*/
  
function imagealea($directory) {
    // Ouvre un dossier bien connu, et liste tous les fichiers grace à l'argument
    // $directory = 'nom_du_dossier';
    // Définition d'$image comme tableau
    $image = array();
    //on vérifie s’il s’agit bien d’un répertoire
    if (is_dir($directory)) {
        //on ouvre le repertoire
        if ($dh = opendir($directory)) {
            //Lit une entrée du dossier et readdir retourne le nom du fichier
            while (($file = readdir($dh)) !== false) {
                // Vérifie de ne pas prendre en compte les dossier ...
                if ($file != '...' && $file != '..' && $file != '.') {
                    // On ajoute le nom du fichier dans le tableau
                    $image[] = $file;
                }
            }
            //On ferme le repertoire
            closedir($dh);
            // On récupère le nombre d'image total
            $total = count($image)-1;
            // On prend une valeur au hasard entre 1 et le nombre total d'images
            $aleatoire = rand(0, $total);
            // On récupère le nom de l'image avec le chiffre hasard
            $image_afficher = "$image[$aleatoire]";
            // Affiche l'image du hasard :p
            $imgfinal = $directory."/".$image_afficher;
            return $imgfinal;
        }
    } else {
        return "erreur dossier";
    }
}

class IMG
{
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

    public function upload_img($namefile, $dir, $name = "sha1") {

        // Constantes
        define('TARGET', '/var/www'.$dir);    // Repertoire cible
        define('MAX_SIZE', 5242880);    // Taille max en octets du fichier

        // Tableaux de donnees
        $tabExt = array('jpg','gif','png','jpeg'); //Extension autorisé

        // Variables
        $extension = '';
        $message = '';
        $nomImage = '';

        /************************************************************
        * Script d'upload
        *************************************************************/
        // On verifie si le champ est rempli
        if(!empty($_FILES[$namefile]['name'])) {
          // Recuperation de l'extension du fichier
          $extension  = pathinfo($_FILES[$namefile]['name'], PATHINFO_EXTENSION);

          // On verifie l'extension du fichier
          if(in_array(strtolower($extension),$tabExt)) {
            // On verifie le type de l'image
            if(getimagesize($_FILES[$namefile]['tmp_name'])) {
              // On verifie la taille du fichier
              if((filesize($_FILES[$namefile]['tmp_name']) <= MAX_SIZE)) {
                // Parcours du tableau d'erreurs
                if(isset($_FILES[$namefile]['error']) && UPLOAD_ERR_OK === $_FILES[$namefile]['error']) {
                  // On renomme le fichier
                  if($name == "off") {
                        /* $filename = $_FILES[$namefile]['name']; */
                        $filename = sha1(uniqid().$_SESSION['userid']."=imgpost=".$_SESSION['username']);
                        $nomImage = $filename.".".strtolower($extension);
                  } else {
                      $filename = sha1($_SESSION['userid']."=imgprofil=".$_SESSION['username'].uniqid());
                      $nomImage = $filename.".".strtolower($extension);
                  }
                  // Si c'est OK, on teste l'upload
                  if(move_uploaded_file($_FILES[$namefile]['tmp_name'], TARGET.$nomImage)) {
                        if($dir == "/images/profil/") {
                            $stmt = $this->db->prepare("UPDATE users SET imageprofil=:imageprofil WHERE pseudo=:pseudo AND id=:id");
                            $stmt->execute(array(
                                'imageprofil' => $nomImage,
                                'pseudo' => $_SESSION['username'],
                                'id'=> $_SESSION['userid']
                            ));
                        }
                        $returnarray = array('status' => 1, 'nomimg' => $nomImage);
                        return $returnarray;
                  } else {
                    // Sinon on affiche une erreur systeme
                    $message = 'Problème lors de l\'upload ! Réessayez.';
                    $returnarray = array('status' => 0, 'err' => $message);
                    return $returnarray;
                  }
                } else {
                  $message = 'Une erreur interne a empêché l\'upload de l\'image :/';
                  $returnarray = array('status' => 0, 'err' => $message);
                  return $returnarray;
                }
              } else {
                // Sinon erreur sur les dimensions et taille de l'image
                $message = 'L\'image ne doit pas faire plus de 5 Mo !';
                $returnarray = array('status' => 0, 'err' => $message);
                return $returnarray;
              }
            } else {
              // Sinon erreur sur les dimensions et taille de l'image
              $message = 'Erreur dans les dimensions de l\'image !';
              $returnarray = array('status' => 0, 'err' => $message);
              return $returnarray;
            }
          } else {
            // Sinon on affiche une erreur pour l'extension
            $message = 'Le fichier n\'est pas une image (png, jpg, jpeg ou gif) !';
            $returnarray = array('status' => 0, 'err' => $message);
            return $returnarray;
          }
        }
    }

    public function get_img_profil($username, $id)
    {
        $imgdir = "/images/profil/";

        $imgdefault = "/assets/img/profil_default.png";
        $imglink = $imgdefault;

        try
        {
            $stmt = $this->db->prepare("SELECT imageprofil FROM users WHERE pseudo=:pseudo AND id=:id LIMIT 1");
            $stmt->execute(array(
                'pseudo' => $username,
                'id'=>$id
            ));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!empty($userRow['imageprofil']))
            {
                $imgname = $userRow['imageprofil'];
                $imghref = $imgdir.$imgname;
                $filename = $_SERVER['DOCUMENT_ROOT'].$imghref;
                if(file_exists($filename)) {
                    $imglink = $imghref;
                } else {
                    $imglink = $imgdefault;
                }
            } else {
                $imglink = $imgdefault;
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }

        return $imglink;
    }

    public function get_img_profil_post($username)
    {
        $imgdir = "/images/profil/";

        $imgdefault = "/assets/img/profil_default.png";
        $imglink = $imgdefault;

        try
        {
            $stmt = $this->db->prepare("SELECT imageprofil FROM users WHERE pseudo=:pseudo LIMIT 1");
            $stmt->execute(array(
                'pseudo' => $username
            ));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!empty($userRow['imageprofil']))
            {
                $imgname = $userRow['imageprofil'];
                $imghref = $imgdir.$imgname;
                $filename = $_SERVER['DOCUMENT_ROOT'].$imghref;
                if(file_exists($filename)) {
                    $imglink = $imghref;
                } else {
                    $imglink = $imgdefault;
                }
            } else {
                $imglink = $imgdefault;
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }

        return $imglink;
    }
    public function suppr_img_profil($username, $id)
    {
        $imgdir = "/images/profil/";

        try
        {
            /* On récupère le lien de l'image de profil */
            $getimg = $this->db->prepare("SELECT imageprofil FROM users WHERE pseudo=:pseudo AND id=:id LIMIT 1");
            $getimg->execute(array(
                'pseudo' => $username,
                'id'=>$id
            ));
            $userImg = $getimg->fetch(PDO::FETCH_ASSOC);

            $image = $userImg['imageprofil'];

            /* Si il n'y a pas d'image de profil on ne supprime pas le fichier / ni SQL */
            if(empty($image)) {
                return true;
            } else {
                /* On supprime le lien de l'image de profil */
                $stmt = $this->db->prepare("UPDATE users SET imageprofil=:imageprofil WHERE pseudo=:pseudo AND id=:id");
                $stmt->execute(array(
                    'imageprofil' => '',
                    'pseudo' => $username,
                    'id'=>$id
                ));

                $filename = $_SERVER['DOCUMENT_ROOT'].$imgdir.$image;
                /* On supprime l'image de profil */
                if(unlink($filename)) {
                    return true;
                } else {
                    return false;
                }
            }
            
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }
}
?>