<?php
$titre_page = "Chat";
require $_SERVER['DOCUMENT_ROOT'].'/include/header.php';

/* On vérifie si l'user n'est pas déjà connecté */
if(!connect()) {
    $user->redirect('/home');
}

require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/navbar.php';
?>
<div class="container" id="firstcontainer">
  <div class="row">
    <div class="col-sm-2 col-md-3"></div>
    <div class="col-xs-12 col-sm-8 col-md-6" id="divclasseorfriend">
      <h2 class="page-header">Chat instantanée <small>Public</small></h2>
      <div class="row">
        <div class="col-xs-12">
          <div class="chatmobile" id="tchat" style="max-height: 500px;overflow-y: auto;"></div>
          <div class="row" style="width: 100%;vertical-align:bottom;">
            <hr>
            <div class="col-md-1"></div>
            <div class="col-xs-12 col-md-10">
              <form method="post" id="chatForm">
                <input type="text" id="messageinput" class="form-control" rows="3" placeholder="Votre message"><br>  
                <button type="submit" class="btn btn-success btn-block" id="submittchat">Envoyer</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/footer.php';
require $_SERVER['DOCUMENT_ROOT'].'/include/footer.php';
?>