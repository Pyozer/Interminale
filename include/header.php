<?php require $_SERVER['DOCUMENT_ROOT'].'/include/init.php'; ?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Interminale <?php if(isset($titre_page)) { echo "- ".$titre_page; } ?></title>
		<meta name="description" content="Interminale, le mini réseau social pour les Terminales. Partage, discute et échange avec ta classe ou amis." />
		<meta name="robots" content="noindex, nofollow, noarchive">
		<link type="text/plain" rel="author" href="/humans.txt">
		<!-- CSS -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css" rel="stylesheet">
		<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
		<!-- Material design -->
		<link href="/assets/css/material.min.css" rel="stylesheet">
		<link href="/assets/css/ripples.min.css" rel="stylesheet">
		<!-- Interminale CSS -->
		<link href="/assets/css/custom.min.css" rel="stylesheet">
		<?php if(isset($_COOKIE['theme'])) {
			if($_COOKIE['theme'] == "night") {
				echo '<link href="/assets/css/theme-nuit.css" rel="stylesheet">';
			} else if($_COOKIE['theme'] == "navbar") {
				echo '<link href="/assets/css/theme-navbar.css" rel="stylesheet">';
			}
		} ?>
		<!-- Favicon -->
		<link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
		
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	</head>
	<body>
		<!-- Google analytics -->
		<?php require_once($_SERVER['DOCUMENT_ROOT'].'/app/admin/analyticstracking.php'); ?>
		<!-- ====== Notifications ====== -->
		<div id="ohsnap"></div>
		<div id="page">
			<!-- ====== Utilisateur(s) connecté(s) ====== -->
			<div class="allusersconnect hidden-xs hidden-sm"></div>
			<div id="contenu">