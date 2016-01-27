<?php
/*|-------------------------------------------------
  | Class pour regrouper toutes les erreurs
  | et pouvoir les appeller sur une page.
  |-------------------------------------------------
*/
function erreur($errorname){
    switch($errorname){

        case 'SIGNUP_FAIL':
            $msgerror = 'Erreur lors de l\'inscription, réessayez';
            break;

        case 'SIGNIN_FAIL':
            $msgerror = 'Erreur lors de la connexion, réessayez';
            break;

        case 'USER_NO_FIELDTEXT':
            $msgerror = 'Veuillez saisir tous les champs de texte !';
            break;

        case 'INPUT_ONLY_LETTERS':
            $msgerror = 'Veuillez mettre que des lettres dans Prénom, Nom';
            break;

        case 'USER_NO_CHECK':
            $msgerror = 'Veuillez cocher la case !';
            break;

        case 'USER_ID_ERROR':
            $msgerror = 'Identifiants incorrect !';
            break;

        case 'USER_VALID_EMAIL':
            $msgerror = 'Merci de saisir une adresse mail valide !';
            break;

        case 'USER_SAME_PASSWORD':
            $msgerror = 'La confirmation de votre mot de passe ne convient pas !';
            break;

        case 'USER_ACTUAL_PASSWORD_FAIL':
            $msgerror = 'Le mot de passe actuelle ne convient pas ! Réessayez.';
            break;

        case 'USER_PASSWORD_CARACT':
            $msgerror = 'Votre mot de passe doit contenir au moins 6 caractères !';
            break;

        case 'USER_EMAIL_TAKE':
            $msgerror = 'Désolé, cette adresse mail est déjà prise.';
            break;

        case 'USER_NAME_TAKE':
            $msgerror = 'Désolé, votre prénom et nom a déjà été utilisé !';
            break;
        
        case 'USER_NO_DATEOFBIRTH':
            $msgerror = 'Veuillez saisir votre date de naissance !';
            break;

        case 'USER_WRONG_DATEOFBIRTH':
            $msgerror = 'Veuillez saisir une date de naissance valide !';
            break;

        case 'CHANGE_INFO_FAIL':
            $msgerror = 'Un erreur est survenue lors de la modifications, réessayez.';
            break;

        case 'CHANGE_INFO_SUCCESS':
            $msgerror = 'Les modifications ont bien été prises en compte';
            break;

        case 'CHANGE_PASSWORD_SUCCESS':
            $msgerror = 'Votre mot de passe à bien été modifié, reconnectez vous.';
            break;

        case 'CHANGE_EMAIL_SUCCESS':
            $msgerror = 'Votre adresse mail à bien été modifié.';
            break;

        case 'CHANGE_INFO_BIO_FAIL':
            $msgerror = 'Votre bio doit faire 175 caractères maximum !';
            break;

        case 'FAIL_DELETE_IMAGE':
            $msgerror = 'Problème lors de la suppresion de votre image de profil.';
            break;

        case 'FAIL_UPLOAD_IMAGE':
            $msgerror = 'L\'upload de votre image a échoué, réessayez.';
            break;

        case 'FAIL_UPLOAD_FORMAT_IMAGE':
            $msgerror = 'Veuillez upload une image au format jpg, jpeg ou png';
            break;

        case 'FAIL_UPLOAD_BIG_IMAGE':
            $msgerror = 'Veuillez upload une image moins lourde (Moins de 5 Mo)';
            break;

        case 'USER_NO_FILE_INPUT':
            $msgerror = 'Veuillez sélectionnez votre image de profil';
            break;

        case 'USER_IMG_SUCCESS_DELETE':
            $msgerror = 'Votre image de profil a bien été supprimé';
            break;

        case 'ACCOUNT_SUCCESS_DELETE':
            $msgerror = 'Votre compte a bien été supprimé';
            break;

        case 'ACCOUNT_FAIL_DELETE':
            $msgerror = 'Votre compte n\'a pas pu être supprimé, réessayez';
            break;

        case 'TOO_MANY_CARACT_POST':
            $msgerror = 'Il y a trop de caractères ! (700 MAX)';
            break;

        case 'TOO_MANY_CARACT_MSG':
            $msgerror = 'Il y a trop de caractères ! (800 MAX)';
            break;

        case 'TOO_MANY_CARACT_COMMENT':
            $msgerror = 'Il y a trop de caractères ! (450 MAX)';
            break;

        case 'TOO_MANY_CARACT_BIO':
            $msgerror = 'Il y a trop de caractères ! (175 MAX)';
            break;

        case 'TOO_MANY_LINES':
            $msgerror = 'Vous avez sauté trop de lignes ! (20 MAX)';
            break;

        case 'TOO_MANY_LINES_MSG':
            $msgerror = 'Vous avez sauté trop de lignes ! (25 MAX)';
            break;

        case 'FAIL_UPLOAD_FILE':
            $msgerror = 'Le fichier n\'a pu être uploader, réessayez';
            break;

        case 'FAIL_ON_POST':
            $msgerror = 'Une erreur est survenu lors de l\'envoi du post :/';
            break;

        case 'SUCCESS_POST':
            $msgerror = 'La publication a bien été effectué';
            break;

        // Erreur générée par PHP
        default:
            $msgerror = 'Erreur: raison inconnu.';
            break;
    }

    return $msgerror;
}
?>