/**
* JAVASCRIPT "Interminale"
* Version:    2015
* Author:     Jean-Charles Moussé
* Website:    http://www.interminale.fr.nf
**/

/* Variable cache pour like */
var cachedData = Array();

/* Ajouter un like */
$(document).on('click','.likebutton', function(event) {
	event.preventDefault();
	var id = $(this).attr('data-postid');
	var idliked = "#addlike" + id;
	var nbrlike = "#nbrlike" + id;
	var description = "#description" + id;
	delete cachedData[id];
	$.ajax({
		type:'POST',
		url:'/app/ajax/like-request.php',
		data:'id=' + id + '&addlike=true',
		dataType: "json",
		success:function(msg){
			if(msg.status == 1) {
				if($(idliked).hasClass("liked")) {
					$(nbrlike).html(msg.nbrlike);
					$(idliked).removeClass('liked');
				} else {
					$(nbrlike).html(msg.nbrlike);
					$(idliked).addClass('liked');
				}
			} else {
				$(msg.err).insertBefore(description).slideDown();
			}
		}
	});
});

function getuserliked() {
	var id = $(this).attr('data-postid');
	var element = $(this);

	if(id in cachedData){
        return cachedData[id];
    }
    var localData = "Erreur";
	$.ajax({
		type: 'POST',
		url: '/app/ajax/like-request.php',
		data: 'idpost=' + id + '&getuserlike=true',
		dataType: "html",
		async: false,
		success:function(newtitle){
			localData = newtitle;
		}
	});
	cachedData[id] = localData;
	return localData;
}

/* Ajouter un commentaire */
$(document).on('submit','form.formaddcomment', function(event) {
	event.preventDefault();
	/* ID du nbr de commentaires */ 
	var id = $(this).attr('id');
	var idnbrcomment = "#nbrcomment" + id;
	var idcomment = "#commentinput" + id;
	var addcomment = "#addcomment" + id;
	var comment = $.trim($(idcomment).val());
	comment = encodeURIComponent(comment);
	var dataString = 'comment=' + comment + '&post_id=' + id + '&addcomment=true';

	$('#submitnewcomment').attr("disabled", true);
	$('#submitnewcomment').html('Envoi <i class="fa fa-spinner fa-spin"></i>');
	if(comment == "") {
		$('#addcomment'+id+' #errorcomment').html('Veuillez saisir tous les champs').slideDown();
		$('#submitnewcomment').removeAttr("disabled");
		$('#submitnewcomment').html('Valider');
	} else {
		$.ajax({
			type: 'POST',
			url: '/app/ajax/comment-request.php',
			data: dataString,
			dataType: "json",
			success: function(msg) {
				if(msg.status == 1) {
					$('#addcomment'+id+' #errorcomment').hide().html('');
					$(msg.view).hide().insertAfter(addcomment).slideDown();
					$('#submitnewcomment').removeAttr("disabled");
					$('#submitnewcomment').html('Valider');
					$(idcomment).val('');
					$(idnbrcomment).html(msg.nbrcomment);
					ohSnap('Votre commentaire a été ajouté', 'green');
					emojify.run();
					linktohref();
					$("time.timeago").timeago();
				} else {
					$('#addcomment'+id+' #errorcomment').html(msg.err).slideDown();
				}
			}
		});
	}
});
/* Supprimer un commentaire */
$(document).on('click','.supprcomment', function(event) {
	event.preventDefault();

	var id = $(this).attr('id');
	var post = $(this).attr('data-postid');

	var idnbrcomment = "#nbrcomment" + post;
	var commentairenum = "#commentairenum" + id;
	var dataString = 'supprcomment=true' + '&comment_id=' + id + '&post_id=' + post;
	$.ajax({
		type: 'POST',
		url: '/app/ajax/comment-request.php',
		data: dataString,
		dataType: "json",
		success: function(msg) {
			if(msg.status == 1) {
				$(commentairenum).fadeOut(400).hide(function(){
					$(commentairenum).remove();
				});
				$(idnbrcomment).html(msg.nbrcomment);
				ohSnap('Votre commentaire a été supprimé', 'green');
			} else {
				$(msg.err).hide().insertBefore(commentairenum).slideDown();
			}
		}
	});
});
/* Afficher le reste des commentaires */
function afficher_plus(id){
	event.preventDefault();
	var divbutton = "#btnloadmore" + id;
	var lastcomment = $('#comment' + id + ' .comment').length;
	var dataString = 'post_id=' + id + '&lastcomment=' + lastcomment + '&loadmore=true';
	$.ajax({
		type: 'POST',
		url: '/app/ajax/comment-request.php',
		data: dataString,
		success: function(msg) {
			$(msg).insertBefore(divbutton).slideDown();
			$(divbutton).remove();
			emojify.run();
			linktohref();
			$("time.timeago").timeago();
		}
	});
}

/* Ajouter un post */
$(document).on('click','#submitaddpost', function(event) {
	event.preventDefault();
	var post = $.trim($('#inputPost').val());
	post = encodeURIComponent(post);
	var conf = $('.inputConf:checked').val();
	var dataString = 'postajax=' + post + '&confajax=' + conf + '&addpost=true';

	$('#submitaddpost').attr("disabled", true);
	$('#submitaddpost').html('Publication <i class="fa fa-spinner fa-spin"></i>');

	if(post == "") {
		$('#errorpost').hide().html("Veuillez saisir tous les champs de texte !").slideDown();
		$('#submitaddpost').html('Publier');
		$('#submitaddpost').removeAttr("disabled");
	} else {
		if($('#fichierjoint').val() != "") {
			/* Envoyé image profil après sa selection */
			$('#form-addpost').submit();
		} else {
			$.ajax({
				type: 'POST',
				url: '/app/ajax/post-request.php',
				data: dataString,
				dataType: "json",
				success: function(msg) {
					if(msg.status == 1) {
						$('#inputPost').val('');
						$('#errorpost').hide().html('');
						$('#addPostModal').modal('hide');
						$('#submitaddpost').html('Publier');
						$('#submitaddpost').removeAttr("disabled");

						/* Si sur la page index */
						if ($('#beforePost').length) {
							if ($('#nopost').length) {
								$('#nopost').remove();
							}
							$(msg.view).hide().insertBefore('.post').first().slideDown();
							ohSnap('Votre post a été ajouté', 'green');
							emojify.run();
							linktohref();
							$("time.timeago").timeago();
						}
						/* Si sur la page publication */
						if ($('#beforePostPublic').length) {
							if(conf == "public") {
								if ($('#nopost').length) {
									$('#nopost').remove();
								}
								$(msg.view).hide().insertAfter('#beforePostPublic').first().slideDown();
								ohSnap('Votre post a été ajouté', 'green');
								emojify.run();
								linktohref();
								$("time.timeago").timeago();
							}
						}
						/* Si sur profil */
						if ($('#userbeforePost').length) {
							if ($('#nopost').length) {
								$('#nopost').remove();
							}
							$(msg.view).hide().insertAfter('#userbeforePost').first().slideDown();
							ohSnap('Votre post a été ajouté', 'green');
							emojify.run();
							linktohref();
							$("time.timeago").timeago();
						}
					} else {
						$('#errorpost').hide().html(msg.err).slideDown();
						$('#submitaddpost').html('Publier');
						$('#submitaddpost').removeAttr("disabled");
					}
				}
			});
		}
	}
});
/* Supprimer un post */
$(document).on('click','.supprpost', function(event) {
	event.preventDefault();

	var id = $(this).attr('data-postid');
	var post = "#postnum" + id;
	var dataString = 'post_id=' + id + '&supprpost=true';
	$.ajax({
		type: 'POST',
		url: '/app/ajax/post-request.php',
		data: dataString,
		dataType: "json",
		success: function(msg) {
			if(msg.status == 1) {
				$(post).slideUp().hide(function(){
					$(post).remove();
				});
				ohSnap('Votre post a été supprimé', 'green');
			} else {
				$(msg.err).hide().insertBefore('#postcontent').first().slideDown();
			}
		}
	});
});

/* Vérifie si pas de nouveau post */
function update_posts() {
	var getlastpost = $('.post:first-child').attr("id");
  	var splitChaine = getlastpost.split('postnum');
  	var lastpost = splitChaine[1];

	var dataString = 'lastidpost=' + lastpost + '&updateposts=true';
	$.ajax({
		type: 'POST',
		url: '/app/ajax/post-request.php',
		data: dataString,
		dataType: "json",
		success: function(data) {
			if(data.status == 1) {
				/* Si sur la page index */
				if ($('#beforePost').length) {
					if ($('#nopost').length) {
						$('#nopost').remove();
					}
					$(data.view).hide().insertBefore('.post').first().slideDown();
					ohSnap('Nouvelle publication de ' + data.auteur + '', 'green');
					emojify.run();
					linktohref();
					$("time.timeago").timeago();
				}
			}
		}
	});
	setTimeout('update_posts()', 15000);
}
/* Ajouter smiley au champs de texte */
$('#listEmoji').on('click','img',function(){
	var valeurinput = $('#inputPost').val();
	var smiley = $( this ).attr('title');
  	$('#inputPost').val(valeurinput + ' ' + smiley);
});
/* Mise à jour des notifications (msg et amis) */
function update_msg_friend() {
    var dataString = 'updatenotif=true';
	$.ajax({
		type: 'POST',
		url: '/ajax.php',
		data: dataString,
		dataType: "json",
		success: function(msg) {
			if(msg.status == 1) {
				/* Message privé */
				var suplengthmsg = $("li #limessage sup").length;
				var valueactualmsg = $("li #limessage sup span").html();
				/* Demande d'amis */
				var suplengthamis = $("li #liamis sup").length;
				var valueactualamis = $("li #liamis sup span").html();
				/* MESSAGE PRIVE */
				/* On met le nbr de msg privé si > 0 sinon on efface le nbr */
				if($('#messages-prives').length == 0) {
					if(msg.nbrnewmsg > 0 && valueactualmsg != msg.nbrnewmsg) {
						$("li #limessage").html('Messages <sup><span class="badge badge-primary">' + msg.nbrnewmsg + '</span></sup>');
						if(msg.nbrnewmsg > valueactualmsg) {
							var diff = msg.nbrnewmsg - valueactualmsg;
							if(diff > 1) {
								ohSnap('Vous avez ' + diff + ' nouveaux messages privé', 'green');
							} else {
								ohSnap('Nouveau message privé reçu', 'green');
							}
						}
					} else if(msg.nbrnewmsg == 0 && suplengthmsg != 0) {
						$("li #limessage").html('Messages <sup style="display: none;"><span class="badge badge-primary">' + msg.nbrnewmsg + '</span></sup>');
					}
				}
				/* AMIS */
				/* On met le nbr de demande d'amis si > 0 sinon on efface le nbr */
				if(msg.nbrnewfriend > 0 && valueactualamis != msg.nbrnewfriend) {
					$("li #liamis").html('Amis <sup><span class="badge badge-primary">' + msg.nbrnewfriend + '</span></sup>');
					if(msg.nbrnewfriend > valueactualamis) {
						var diff = msg.nbrnewfriend - valueactualamis;
						if(diff > 1) {
							ohSnap('Vous avez ' + diff + ' nouvelles demandes d\'ami', 'green');
						} else {
							ohSnap('Nouvelle demande d\'ami reçu', 'green');
						}
					}
				} else if(msg.nbrnewfriend == 0 && suplengthamis != 0) {
					$("li #liamis").html('Amis <sup style="display: none;"><span class="badge badge-primary">' + msg.nbrnewfriend + '</span></sup>');
				}
			}
		}
	});
}
/* Accepter une demande d'amis */
$(document).on('click','#acceptDemand', function(event) {
	event.preventDefault();

	var idinvitation = $(this).attr('data-idinv');
	var pseudo_exp = $(this).attr('data-pseudoexp');
	var user_name = $(this).attr('data-username');

	var demanddiv = "#demand" + idinvitation;
	var dataString = 'idinvitation=' + idinvitation + '&pseudo_exp=' + pseudo_exp + '&acceptfriend=true';
	$.ajax({
		type: 'POST',
		url: '/app/ajax/friends-request.php',
		data: dataString,
		dataType: "json",
		success: function(msg) {
			if(msg.status == 1) {
				$(demanddiv).slideUp().hide(function(){
					$(demanddiv).remove();
				});
				ohSnap(user_name + ' est maintenant votre ami(e)', 'green');

				var valueactualamis = $("li #liamis sup span").html();
				var newnbramis = valueactualamis - 1;
				if(newnbramis > 0) {
					$("li #liamis").html('Amis <sup><span class="badge badge-primary">' + newnbramis + '</span></sup>');
				} else {
					$("li #liamis").html('Amis <sup style="display: none;"><span class="badge badge-primary">' + newnbramis + '</span></sup>');
				}
			} else {
				ohSnap('Une erreur est survenue :/', 'red');
			}
		}
	});
});
/* Refuser une demande d'amis */
$(document).on('click','#deniedDemand', function(event) {
	event.preventDefault();

	var idinvitation = $(this).attr('data-idinv');
	var pseudo_exp = $(this).attr('data-pseudoexp');

	var demanddiv = "#demand" + idinvitation;
	var dataString = 'idinvitation=' + idinvitation + '&pseudo_exp=' + pseudo_exp + '&deniedfriend=true';
	$.ajax({
		type: 'POST',
		url: '/app/ajax/friends-request.php',
		data: dataString,
		dataType: "json",
		success: function(msg) {
			if(msg.status == 1) {
				$(demanddiv).slideUp().hide(function(){
					$(demanddiv).remove();
				});
				ohSnap('La demande a bien été refusée', 'green');
				var valueactualamis = $("li #liamis sup span").html();
				var newnbramis = valueactualamis - 1;
				if(newnbramis > 0) {
					$("li #liamis").html('Amis <sup><span class="badge badge-primary">' + newnbramis + '</span></sup>');
				} else {
					$("li #liamis").html('Amis <sup style="display: none;"><span class="badge badge-primary">' + newnbramis + '</span></sup>');
				}
			} else {
				ohSnap('Une erreur est survenue :/', 'red');
			}
		}
	});
});
/* Transformer lien en href */
function linktohref() {
    // http://fr.wikipedia.org/wiki/Sch%C3%A9ma_d%27URI
    $('p').each(function() {
        var rgx = new RegExp('(?![^<]*>)([a-z0-9+\.-]+://[^\\s|<]+)','ig');
        $(this).html($(this).html().replace(rgx,"<a href='$1' target='_blank'>$1</a>"));
    });
}
$(function() {
    linktohref();
});

/* Recherche ajax */
var searchuser = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('pseudo'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: {
    url: '/ajax.php?dosearch=true&search=%QUERY%',
    wildcard: '%QUERY%'
  }
});
$('#custom-templates .typeahead').typeahead({
  hint: true,
  highlight: true,
  minLength: 1
},
{
  name: 'searchuser',
  display: 'pseudo',
  source: searchuser,
  limit: 15,
  templates: {
    empty: [
      '<div class="empty-message">',
        'Aucun résultat n\'a été trouvé',
      '</div>'
    ].join('\n'),
  	suggestion: function (data) {
        return '<div><a class="no_underline" href="/profil/' + data.href + '" title="' + data.pseudo + '"><img class="img-circle img_result_search" src="' + data.imageprofil + '" alt="' + data.pseudo + '" style="width: 45px;height: 45px;"><h4 class="title_user_search" style="font-size: 18px;">' + data.pseudo + '</h4></a></div>';
    }
  }
});
/* Envoyé image profil après sa selection */
$('#file-input').change(function() {
	$('#newimgprofil').submit();
});
/* Mettre scroll div en bas */
function gotobottom(idelement) {
	element = document.getElementById(idelement);
	element.scrollTop = element.scrollHeight;
}
/* Switcher la fleche pour ouvrir chat */
$('#opentchat').click(function() {
  	setTimeout(function() {
		gotobottom('tchat');
	}, 300);
	setTimeout(function() {
		if($("#tchatmenu").hasClass('canvas-slid') == false) {
			$("#opentchat").html('<i class="fa fa-chevron-left"></i>');
		} else {
			$("#opentchat").html('<i class="fa fa-chevron-right"></i>');
		}
	}, 700);
});
/* Jouer un son */
function playSound(soundname) {
	/* var e = document.createElement('audio');
	e.setAttribute('src', soundname);
	e.play(); */
	$('#soundtoplay')[0].play();
}
/* Récuperer tous les messages */
function get_all_msg() {
    var dataString = 'getmsgchat=true';
	$.ajax({
		type: 'POST',
		url: '/app/ajax/chat-request.php',
		data: dataString,
		dataType: "html",
		success: function(msg) {
			$("#tchat").html(msg);
			emojify.run();
			linktohref();
			$("time.timeago").timeago();
		}
	});
}
/* Ajouter un fichier */
$(document).on('click','#submitaddfile', function(event) {
	event.preventDefault();

	var desc = $.trim($('#descfile').val());
	desc = encodeURIComponent(desc);
	var fichier = $('#fileinput').val();
	var conf = $('.inputConf:checked').val();

	$('#submitaddfile').attr("disabled", true);
	$('#submitaddfile').html('Chargement <i class="fa fa-spinner fa-spin"></i>');

	if(fichier == "" || desc == "") {
		$('#errorpost').html('Veuillez saisir tous les champs !');
		$('#submitaddfile').html('Publier');
		$('#submitaddfile').removeAttr("disabled");
	} else {
		$('#form-addfile').submit();
	}
});
/* Ne plus afficher le(s) anniversaire(s) */
function avoidanniv() {
	var dataString = 'dontseeanniv=true';
	$.ajax({
		type: 'POST',
		url: '/app/ajax/events-request.php',
		data: dataString,
		success: function(msg) {}
	});

}
/* Ne plus afficher le(s) evenements */
function avoidevent() {
	var dataString = 'dontseeevent=true';
	$.ajax({
		type: 'POST',
		url: '/app/ajax/events-request.php',
		data: dataString,
		success: function(msg) {}
	});

}
/* Ajouter un loading sur un btn */
function addloading(element) {
	var value = $(element).html();
	$(element).addClass('disabled');
	$(element).html(value + '<i class="fa fa-spinner fa-spin" style="margin-left: 5px;"></i>');
}
/* Ajouter un évenement */
$(document).on('click','#submit_event', function(event) {
	event.preventDefault();

	var name_event = $.trim($('#name_event').val());
	var name_event = encodeURIComponent(name_event);

	var desc_event = $.trim($('#desc_event').val());
	var desc_event = encodeURIComponent(desc_event);

	var date_event = $.trim($('#date_event').val());
	var date_event = encodeURIComponent(date_event);

	var type_event = $.trim($('#type_event').val());
	var type_event = encodeURIComponent(type_event);

	var public_event = $('.public_event:checked').val();
	var dataString = 'name_event=' + name_event + '&desc_event=' + desc_event + '&date_event=' + date_event + '&type_event=' + type_event + '&public_event=' + public_event + '&addevent=true';

	$('#submit_event').attr("disabled", true);
	$('#submit_event').html('Ajout <i class="fa fa-spinner fa-spin"></i>');

	if(name_event == "" || date_event == "" || type_event == "" || public_event == "") {
		$('#errorpost').hide().html("Veuillez saisir tous les champs de texte !").slideDown();
		$('#submit_event').html('Publier');
		$('#submit_event').removeAttr("disabled");
	} else {
		$.ajax({
			type: 'POST',
			url: '/app/ajax/events-request.php',
			data: dataString,
			dataType: "json",
			success: function(msg) {
				if(msg.status == 1) {
					$('#name_event').val('');
					$('#desc_event').val('');
					$('#date_event').val('');
					$('#errorpost').hide().html('');
					$('#addevent').modal('hide');
					$('#submit_event').html('Ajouter');
					$('#submit_event').removeAttr("disabled");
					ohSnap('L\'évenement à été ajouté', 'green');
				} else {
					$('#errorpost').hide().html(msg.err).slideDown();
					$('#submit_event').html('Publier');
					$('#submit_event').removeAttr("disabled");
				}
			}
		});
	}
});

/* Switcher la fleche pour ouvrir chat */
$('#btnlistemoji').click(function() {
	$("i",this).toggleClass("fa fa-close");
});

/* On valide la présence de l'utilisateur */
function valid_user_connected(id_user){
	var dataString = 'id_user=' + id_user + '&validco=true';
	$.ajax({
		type: 'POST',
		url: '/ajax.php',
		data: dataString,
		dataType: "json",
		success: function(msg) {}
	});
	setTimeout('valid_user_connected(' + id_user + ')', 20000);
}

/* On valide la présence de l'utilisateur */
function verif_user_connected(id_user){
	var dataString = 'id_user=' + id_user + '&validco_other=true';
	$.ajax({
		type: 'POST',
		url: '/ajax.php',
		data: dataString,
		dataType: "json",
		success: function(msg) {
			if(msg.status == 1) {
				/* Si le span username existe et que la logo connexion n'existe pas */
				if($('#userspan').length && $('#userspan i:first').length < 1) {
					$('#userspan').prepend('<i class="fa fa-circle" id="user_connected" title="Connecté"></i> ');
				}
			} else {
				/* Si le span username existe et que la logo connexion existe */
				if($('#userspan').length && $('#userspan i:first').length > 0) {
					$('#userspan i:first').remove();
				}
			}
		}
	});
	setTimeout('verif_user_connected(' + id_user + ')', 20000);
}
/* On valide la présence de l'utilisateur */
function check_alluser_connected(id_user){
	var dataString = 'id_user=' + id_user + '&checkuserco=true';
	$.ajax({
		type: 'POST',
		url: '/ajax.php',
		data: dataString,
		dataType: "html",
		success: function(msg) {
			$('.allusersconnect').html(msg);
		}
	});
	setTimeout('check_alluser_connected(' + id_user + ')', 20000);
}