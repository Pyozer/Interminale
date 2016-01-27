<?php
session_start();
header("Content-Type: text/html; charset=utf-8");
ini_set('default_charset', 'utf-8');
/* ini_set('allow_url_include', 'on'); */

date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR.utf8','fra');

require_once $_SERVER['DOCUMENT_ROOT'].'/app/admin/id_bdd.php';
/*|--------------------------------------------------
  | Connexion à la base de donnée
  |--------------------------------------------------
*/
try
{
    $DB_con = new PDO('mysql:host='.$DB_host.';dbname='.$DB_name.';', ''.$DB_user.'', ''.$DB_pass.'');
    $DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $DB_con->exec("SET CHARACTER SET utf8");
}
catch(PDOException $e)
{
  die('<h1>ERREUR LORS DE LA CONNEXION A LA BASE DE DONNEE. <br />REESAYEZ ULTERIEUREMENT</h1>');
}

/*|--------------------------------------------------
  | Initialisation des class etc..
  |--------------------------------------------------
*/
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/other.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/flash.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/erreur.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/user.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/profil.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/img.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/friends.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/posts.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/like.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/comment.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/messagerie.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/classe.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/app/class/fichier.class.php';

$user = new USER($DB_con);
$profil = new PROFIL($DB_con);
$img = new IMG($DB_con);
$amis = new AMIS($DB_con);
$post = new POST($DB_con);
$like = new LIKE($DB_con);
$comment = new COMMENT($DB_con);
$message = new MESSAGE($DB_con);
$classe = new CLASSE($DB_con);
$fichier = new FICHIER($DB_con);

/* Publication d'une image avec un post */
if(isset($_POST['post']) && isset($_POST['conf'])) {
    $post_content = htmlspecialchars($_POST['post']);
    $post_content = nl2br($post_content);
    $conf = htmlspecialchars($_POST['conf']);

    if($conf == "friends") {
      $conf = "0";
    } else if($conf == "public") {
      $conf = "1";
    } else {
      $conf = "0";
    }
    $nb_lignes = substr_count($post_content, "\n");

    if(empty($post_content)) {
      $error = erreur('USER_NO_FIELDTEXT');
      setFlash($error, "danger");
    } else {
      if($nb_lignes <= 20) {
        if(strlen($post_content) <= 700) {
          if($result = $img->upload_img('fichierjoint', '/uploads/posts/', "off")) {
              if($result['status'] != 1) {
                setFlash($result['err'], "danger");
              } else {
                /* Nom de l'image */
                $nomImage = $result['nomimg'];
                if($post->addpost($_SESSION['username'], $post_content, $conf, '/uploads/posts/'.$nomImage)) {
                  $error = erreur('SUCCESS_POST');
                  setFlash($error, "success");
                  $user->redirect($_SERVER['REQUEST_URI']);

                } else {
                  $error = erreur('FAIL_ON_POST');
                  setFlash($error, "danger");
                }
              }
          } else {
            $error = erreur('FAIL_UPLOAD_FILE');
            setFlash($error, "danger");
          }
        } else {
          $error = erreur('TOO_MANY_CARACT_POST');
          setFlash($error, "danger");
        }
      } else {
        $error = erreur('TOO_MANY_LINES');
        setFlash($error, "danger");
      }
    }
}

/* Connexion automatique si cookie */
if(isset($_COOKIE['auth']) && !connect()) {
  $auth = $_COOKIE['auth'];
  $auth = explode('===', $auth);
  $usercheck = $DB_con->prepare("SELECT session,id,prenom,nom,pseudo,email,classe FROM users WHERE id=:id LIMIT 1");
  $usercheck->execute(array(
      'id' => $auth[0]
  ));
  $userRow = $usercheck->fetch();
  $key = sha1($userRow['email'].$_SERVER['REMOTE_ADDR']);
  if($key == $auth[1]) {
      $contentcook = $userRow['id']."===".sha1($userRow['email'].$_SERVER['REMOTE_ADDR']);
      setcookie('auth', $contentcook, time() + 3600 * 24 * 7, '/', 'www.interminale.fr.nf', false, true);
      $session = md5(rand());
      $lastco = strftime('%d %B %Y à %H:%M');
      $updateMembre = $DB_con->prepare('UPDATE users SET session=:session, lastco=:lastco WHERE email=:email AND id=:id');
      $updateMembre->execute(array(
        'email' => $userRow['email'],
        'id' => $auth[0],
        'session' => $session,
        'lastco' => $lastco
      ));
      $_SESSION['session'] = $session;
      $_SESSION['userid'] = $userRow['id'];
      $_SESSION['userpseudo'] = $userRow['prenom']." ".$userRow['nom'];
      $_SESSION['username'] = $userRow['pseudo'];
      $_SESSION['userclasse'] = $userRow['classe'];
      $connectauto = true;
      $uri = $_SERVER['REQUEST_URI'];
      if($uri == "/home" || $uri == "/connexion" || $uri == "/inscription" || $uri == "/forget_password" || $uri == "/deconnexion") {
	      $user->redirect('/');
	  }
  } else {
      setcookie('auth', '', time() - 3600, '/', 'www.interminale.fr.nf', false, true);
  }
}
?>