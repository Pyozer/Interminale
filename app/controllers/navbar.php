<?php
//****************************************************
// Controller: Navbar
//****************************************************

if(connect()) {

	if(!isset($_SESSION['sexe'])) {
		$info_profil = $profil->get_profil_info($_SESSION['username']);
    	$_SESSION['sexe'] = $info_profil['sexe'];
	}

    if(!isset($_SESSION['imgprofil'])) {
        $_SESSION['imgprofil'] = $img->get_img_profil_post($_SESSION['username']);
    }
    
    $dropdown = '
    <li class="dropdown">
        <a href="javascript:void(0)" data-target="#" class="dropdown-toggle homeuser" data-toggle="dropdown">
            <img class="img-circle" src="'.$_SESSION['imgprofil'].'" style="width: 26px;height: 26px;margin-top: -9px;"> <i class="mdi-navigation-expand-more"></i>
        </a>
        <ul class="dropdown-menu" style="border-radius: 0 0 3px 3px;text-align: center;">
            <li><a href="/profil" title="Votre profil">Profil</a></li>
            <li><a href="/partage_fichiers" title="Partage de fichiers">Partage de fichiers</a></li>
            <li><a href="/parametres" title="Paramètres">Paramètres</a></li>
            <li role="separator" class="divider" style="background-color: rgb(212, 212, 212);height: 2px;margin: 0;"></li>
            <li><a href="/deconnexion?logout=true" title="Déconnexion">Déconnexion</a></li>
        </ul>
    </li>';

    if($_SERVER['REQUEST_URI'] == "/") { $activehome = 'class="active"'; } else { $activehome = ""; }
    if($_SERVER['REQUEST_URI'] == "/classe") { $activeclasse = 'class="active"'; } else { $activeclasse = ""; }
    if($_SERVER['REQUEST_URI'] == "/amis") { $activeamis = 'class="active"'; } else { $activeamis = ""; }
    if($_SERVER['REQUEST_URI'] == "/publications") { $activeposts = 'class="active"'; } else { $activeposts = ""; }
    if($_SERVER['REQUEST_URI'] == "/messages") { $activemessages = 'class="active"'; } else { $activemessages = ""; }
    if($_SERVER['REQUEST_URI'] == "/utilisateurs") { $activeusers = 'class="active"'; } else { $activeusers = ""; }
    if($_SERVER['REQUEST_URI'] == "/chat") { $activechat = 'class="active"'; } else { $activechat = ""; }
    $listonglets = '
    <li '.$activehome.'><a href="/" title="Accueil">Accueil</a></li>
    <li '.$activeclasse.'><a href="/classe" title="Ma classe">Classe</a></li>
    <li '.$activeamis.'><a href="/amis" title="Mes amis" id="liamis">Amis</a></li>
    <li '.$activeusers.'><a href="/utilisateurs" title="Utilisateurs inscrits">Utilisateurs inscrits</a></li>
    <li '.$activemessages.'><a href="/messages" title="Messages" id="limessage">Messages</a></li>
    <li '.$activechat.' class="visible-xs"><a href="/chat" title="Chat">Chat</a></li>';

    $onglets = $listonglets.$dropdown;

    $dropdownmobile = '
    <li><a href="/profil" title="Votre profil">Profil</a></li>
    <li><a href="/partage_fichiers" title="Partage de fichiers">Partage de fichiers</a></li>
    <li><a href="/parametres" title="Paramètres">Paramètres</a></li>
    <li><a href="/deconnexion?logout=true" title="Se déconnecter">Déconnexion</a></li>';

    $ongletsmobile = $listonglets.$dropdownmobile;

    $searchbar = '
    <form method="get" class="navbar-form visible-xs visible-lg" id="searchbarit" action="/recherche.php">
        <div id="custom-templates">
            <input class="typeahead form-control input-lg" type="text" id="searchinputnav" name="search" placeholder="Recherche" autocomplete="off">
            <input type="submit" style="display: none;" value="Submit">
        </div>
    </form>';
} else {
$onglets = '
    <li class="dropdown">
        <a href="javascript:void(0)" data-target="#" class="dropdown-toggle homeuser" data-toggle="dropdown"><i class="mdi-action-account-circle"></i><i class="mdi-navigation-arrow-drop-down"></i></a>
        <ul class="dropdown-menu" style="border-radius: 0 0 3px 3px;text-align: center;">
            <li><a href="/inscription">S\'inscrire</a></li>
            <li><a href="/connexion">Se connecter</a></li>
        </ul>
    </li>';

    $ongletsmobile = '
    <li><a href="/inscription">S\'inscrire</a></li>
    <li><a href="/connexion">Se connecter</a></li>';

    $searchbar = '';
}

require $_SERVER['DOCUMENT_ROOT'].'/app/view/navbar.template.php';
?>