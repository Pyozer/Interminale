<?php
$titre_page = "Déconnexion";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie que l'utilisateur est connecté */
if(connect()) {
    if(isset($_SESSION['sexe'])) {
        if($_SESSION['sexe'] == "Homme") {
            $bg = "/assets/img/logo4.png";
        } else if($_SESSION['sexe'] == "Femme") {
            $bg = "/assets/img/logo5.png";
        } else {
            $bg = "/assets/img/logo4.png";
        }
    } else {
        $bg = "/assets/img/logo4.png";
    }
    if(isset($_GET['logout']) && $_GET['logout'] == "true") {
        if(isset($_GET['edit']) && $_GET['edit'] == "password") {
            if($user->remove_user_connected($_SESSION['userid'])) {
                if($user->logout()) {
                    setFlash('Votre mot de passe a bien été modifié', 'success');
                    $user->redirect('/connexion');
                } else {
                    $user->redirect('/');
                }
            } else {
                $user->redirect('/');
            }
        } else {
            if($user->remove_user_connected($_SESSION['userid'])) {
                if($user->logout()) {
                    echo '<META http-equiv="refresh" content="1; URL=/home">';
                } else {
                    $user->redirect('/');
                }
            } else {
                $user->redirect('/');
            }
        }
    } else {
        $user->redirect('/home');
    }
} else {
    $user->redirect('/home');
}
?>
<div class="container" id="decocontainer" style="padding-top: 15%;">
    <div class="row centered">
        <div class="col-sm-1"></div>
        <div class="col-xs-12 col-sm-10">
            <div class="col-xs-12 hidden-xs" style="margin-bottom: 25px;">
                <img src="<?php echo $bg; ?>" style="width: 65%;margin-bottom: 20px;">
            </div>
            <div class="col-xs-12 visible-xs">
                <img src="<?php echo $bg; ?>" style="width: 99%;margin-bottom: 20px;">
            </div>
        </div>
    </div>
    <div class="row centered">
        <div class="col-xs-1"></div>
        <div class="col-xs-10">
            <h2 style="margin-bottom: 50px;">Déconnexion en cours...</h2>
            <div class="loader">
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>