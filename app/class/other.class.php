<?php
/*|-------------------------------------------------
  | Class en rapport avec la connexion d'un
  | utilisateur sur son compte
  |-------------------------------------------------
*/

/* | On vérifie si il est connecté ou pas | */
function connect() {
    if(!isset($_SESSION['session']) && !isset($_SESSION['userid']) && !isset($_SESSION['userpseudo']) && !isset($_SESSION['username']) && !isset($_SESSION['userclasse'])) {
        return false;
    } else {
      if(!empty($_SESSION['session']) && !empty($_SESSION['userid']) && !empty($_SESSION['userpseudo']) && !empty($_SESSION['username']) && !empty($_SESSION['userclasse'])) {
        return true;
      } else {
        return false;
      }
    }
}

/* | On hash le mot de passe | */
function passwordhash($passwordtohash)
{
    $options = [
        'cost' => 9,
    ];
    $passwordhashed = password_hash($passwordtohash, PASSWORD_BCRYPT, $options);
    return $passwordhashed;
}

/* | On supprime les accents et met en minuscule et espace en tiret| */
function suppr_accents($str, $encoding='utf-8')
{
    $str = str_replace (' ','-', $str);
    $str = str_replace ('\'','-', $str);
    $str = str_replace ('"','-', $str);
    $str = mb_strtolower($str, 'UTF-8');
    // transformer les caractères accentués en entités HTML
    $str = htmlentities($str, ENT_NOQUOTES, $encoding);
 
    // remplacer les entités HTML pour avoir juste le premier caractères non accentués
    // Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
    $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
 
    // Remplacer les ligatures tel que : Œ, Æ ...
    // Exemple "Å“" => "oe"
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    // Supprimer tout le reste
    $str = preg_replace('#&[^;]+;#', '', $str);
 
    return $str;
}

/* | Met en majuscule seulement la première lettre du prenom / nom | */
function ucname($string) {
    $string = ucwords(strtolower($string));

    foreach (array('-', '\'') as $delimiter) {
      if (strpos($string, $delimiter) !== false) {
        $string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
      }
    }
    return $string;
}

function multidimensional_search($parents, $searched) {
  if (empty($searched) || empty($parents)) {
    return false;
  }

  foreach ($parents as $key => $value) {
    $exists = true;
    foreach ($searched as $skey => $svalue) {
      $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
    }
    if($exists){ return $key; }
  }
  return false; 
}

function only_letters($str) {
  preg_match('#[^0-9]{1,25}$#',$str,$tab5);
  return (count($tab5)!=0);
}

function AffDate($date, $type = "default"){
  if(!ctype_digit($date)) {
    $date = strtotime($date);
  }

  if($type == "default") {
    if(date('Ymd', $date) == date('Ymd')){
      $diff = time() - $date;

      if($diff < 60) {/* moins de 60 secondes */
        /* return 'Il y a '.$diff.' sec'; */
        return 'à '.date('H:i', $date);
      } else if($diff < 3600) {/* moins d'une heure */
        /* return 'Il y a '.round($diff/60, 0).' min'; */
        return 'à '.date('H:i', $date);
      } else if($diff < 36000) {/* moins de 3 heures */
        if(round($diff/3600, 0) > 1) {
          $pluriel = "s";
        } else {
          $pluriel = "";
        }
        return 'Il y a '.round($diff/3600, 0).' heure'.$pluriel;
      } else {/*  plus de 3 heures ont affiche ajourd'hui à HH:MM:SS */
        return 'Aujourd\'hui à '.date('H:i', $date);
      }
    } else if(date('Ymd', $date) == date('Ymd', strtotime('- 1 DAY'))) {
        return 'Hier à '.date('H:i', $date);
    } else if(date('Ymd', $date) == date('Ymd', strtotime('- 2 DAY'))) {
        return 'Il y a 2 jours, '.date('H:i', $date);
    } else {
        return date('d/m à H:i', $date);
    }
  } else if($type == "post" || $type == "comment" || $type == "message") {
    if(date('Ymd', $date) == date('Ymd')){
      $diff = time() - $date;

      if($diff < 20) { /* moins de 20 secondes */
        return 'A l\'instant';
      } else if($diff < 60) { /* moins de 60 secondes */
        if($diff > 1) {
          $pluriel = "s";
        } else {
          $pluriel = "";
        }
        return 'Il y a '.$diff.' seconde'.$pluriel;
      } else if($diff < 3600) { /* moins d'une heure */
        if(round($diff/60, 0) > 1) {
          $pluriel = "s";
        } else {
          $pluriel = "";
        }
        return 'Il y a '.round($diff/60, 0).' minute'.$pluriel;
      } else if($diff < 36000) { /* moins de 3 heures */
        if(round($diff/3600, 0) > 1) {
          $pluriel = "s";
        } else {
          $pluriel = "";
        }
        return 'Il y a '.round($diff/3600, 0).' heure'.$pluriel;
      } else { /*  plus de 3 heures ont affiche ajourd'hui à HH:MM */
        return 'Aujourd\'hui à '.date('H:i', $date);
      }
    } else if(date('Ymd', $date) == date('Ymd', strtotime('- 1 DAY'))) {
        return 'Hier à '.date('H:i', $date);
    } else if(date('Ymd', $date) == date('Ymd', strtotime('- 2 DAY'))) {
        return 'Il y a 2 jours, '.date('H:i', $date);
    } else {
        return 'Le '.strftime('%e %B, %H:%M', $date);
    }
  }
}

/* Vérifie une date */
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
?>