<?php
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

if(connect()) {
    $user->redirect('/');
}

?>
<div class="container"  id="homecontainer" style="padding-top: 16%;text-align: center;">
    <div class="accueil">
        <div class="row">
            <div class="col-xs-12 visible-sm visible-md visible-lg">
                <img src="/assets/img/logo4.png" alt="Interminale" style="width: 65%;margin-bottom: 20px;">
            </div>
            <div class="col-xs-12 visible-xs">
                <img src="/assets/img/logo4.png" alt="Interminale" style="width: 90%;margin-bottom: 20px;">
            </div>     
        </div>
        <div class="row">
            <div class="col-xs-12">
                <p style="font-size: 22px;"><!-- Slogan à trouvé, si toi t'en a un ba dit le moi ;) #CommentTaVuça #CodeSource !--></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-xs-12 col-sm-6 col-md-3">
                <a class="btn btn-outline btn-outline-gray" href="/connexion" title="Connexion">Connexion</a>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3">
                <a class="btn btn-outline btn-outline-gray btn-disabled" title="Inscription fermé" disabled>Inscription</a>
            </div>
        </div>
    </div>
</div>

<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>