<?php
/*|--------------------------------------------------
  | TEMPLATE: Navbar
  |--------------------------------------------------
  */

/* On affiche message pour les cookie si pas déjà existant */
if(!isset($_COOKIE['acceptcookie'])) { ?>
  <div class="alert alert-default alert-dismissible centered" role="alert" style="background-color: #444;color: #DCDCDC;font-size: 18px;margin: 0;">
    En continuant sur le site, vous acceptez l'utilisation des cookies <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="float: none;color: #DCDCDC;top: 1px;"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
  </div>
<?php
  setcookie("acceptcookie", "1", strtotime('+12 month'));
}
?>
<!-- Menu responsive -->
<nav id="myNavmenu" class="navmenu navmenu-default navmenu-fixed-right offcanvas" role="navigation">
  <ul class="nav navmenu-nav" style="font-size: 20px;">
    <?php echo $ongletsmobile; ?>
  </ul>
  <?php echo $searchbar; ?>
</nav>

<!-- Navbar -->
<div class="navbar navbar-default navbar-fixed-top" id="navbar-custom">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#myNavmenu" data-canvas="body">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <div class="badge" id="notifresponsive" style="display: none;position: absolute;top: 0;right: 0;">0</div>
      </button>
      <?php
      if(isset($_SESSION['sexe'])) {
        if($_SESSION['sexe'] == "Homme") {
          if(isset($_COOKIE['theme']) && ($_COOKIE['theme'] == "navbar" || $_COOKIE['theme'] == "night")) {
            $bg = "/assets/img/logo/logo-bordered.png";
            $bgsmall = "/assets/img/logo/logo-small.png";
          } else {
            $bg = "/assets/img/logo/logo.png";
            $bgsmall = "/assets/img/logo/logo-small.png";
          }
        } else if($_SESSION['sexe'] == "Femme") {
          $bg = "/assets/img/logo/logo-f.png";
          $bgsmall = "/assets/img/logo/logo-f-small.png";
        } else {
          $bg = "/assets/img/logo/logo.png";
          $bgsmall = "/assets/img/logo/logo-small.png";
        }
      } else {
        $bg = "/assets/img/logo/logo.png";
        $bgsmall = "/assets/img/logo/logo-small.png";
      }
      ?>
      <a class="navbar-brand" id="navbar-brand-custom" href="/">
        <img src="<?php echo $bg; ?>" class="hidden-sm" alt="Interminale">
        <img src="<?php echo $bgsmall; ?>" class="visible-sm" alt="Interminale" style="width: 40px;">
      </a>
    </div>
    <div class="navbar-collapse collapse navbar-responsive-collapse">
      <ul class="nav navbar-nav navbar-right" style="margin-bottom: -2px;">
        <?php echo $onglets; ?>
      </ul>
      <?php echo $searchbar; ?>
    </div>
  </div>
</div>
<?php if(connect()) { ?>
<div class="visible-sm visible-md visible-lg" style="position: fixed;right: 0;top: 60px;">
  <button type="button" class="btn btn-flat" id="opentchat" data-toggle="offcanvas" data-target="#tchatmenu" data-canvas="body" title="Conversation instantané">
      <i class="mdi-navigation-chevron-left"></i>
  </button>
</div>
<?php } ?>
<!-- === Si inférieur ou = à IE9 === -->
<!--[if lte IE 9]> 
  <div class="row">
    <div class="alert alert-danger alert-dismissible" role="alert">
      <div class="container-fluid">
        <div class="col-xs-1 col-md-3"></div>
        <div class="col-xs-10 col-sm-10 col-md-6 centered">
          Attention, veuillez mettre à jour votre navigateur ou en utiliser un autre. <a href="https://browser-update.org/fr/update.html">En savoir plus</a>
        </div>
      </div>
    </div>
  </div>
<![endif]-->
<!-- === Si JS pas activé === -->
<noscript>
    <div class="row">
        <div class="alert alert-danger alert-dismissible" role="alert">
          <div class="container-fluid">
            <div class="col-xs-1 col-md-3"></div>
            <div class="col-xs-10 col-sm-10 col-md-6 centered">
              <strong>JavaScript semble être désactivé. Veuillez l'activer pour utiliser le site correctement.</strong><br />
              <a href="http://www.enable-javascript.com/fr/" target="_blank" style="font-style: italic;color: #fafafa;"><strong>Instructions pour activer le javascript.</strong></a>
            </div>
          </div>
        </div>
    </div>
</noscript>