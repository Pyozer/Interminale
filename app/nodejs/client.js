(function($) {
	var socket = io.connect('http://www.interminale.fr.nf:3000');

	var dataRequest = 'getinfouser=true';
	$.ajax({
		type: 'POST',
		url: '/ajax.php',
		data: dataRequest,
		dataType: "JSON",
		success: function(msg) {
			var username = msg.user;
			var iduser = msg.iduser;

			if(username != "null" || iduser != "null") {
				socket.emit('userinfo', { 'username': username, 'iduser': iduser } );
			}
		}
	});
	/**
	* Fonnction pour check si demande d'amis
	**/
	function checkfriendask(notif) {
		var dataString = 'updatenotif=true';
		$.ajax({
			type: 'POST',
			url: '/ajax.php',
			data: dataString,
			dataType: "JSON",
			success: function(msg) {
				if(msg.status == 1) {
					/* Demande d'amis */
					var suplengthamis = $("li #liamis sup").length;
					if(suplengthamis < 1) {
						var valueactualamis = "0";
					} else {
						var valueactualamis = $("li #limessage sup span").html();
					}
					/* AMIS */
					/* On met le nbr de demande d'amis si > 0 sinon on efface le nbr */
					if(msg.nbrnewfriend > 0 && valueactualamis != msg.nbrnewfriend) {
						$("li #liamis").html('Amis <sup><span class="badge badge-primary">' + msg.nbrnewfriend + '</span></sup>');
						if(notif == "true") {
							ohSnap('Nouvelle demande d\'ami reçu', 'green');
							playSound('/assets/sound/pop-chat.mp3');
						}
					} else if(msg.nbrnewfriend == 0) {
						$("li #liamis").html('Amis');
					}
				}
			}
		});
	}
	/**
	* Envoi d'un message dans le chat
	**/
	$("#chatForm").submit(function(event){
		event.preventDefault();
		var message = $.trim($("#messageinput").val());
		messageencoded = encodeURIComponent(message);

		$('#submittchat').attr("disabled", true);
		$('#submittchat').html('Envoi <i class="fa fa-spinner fa-spin"></i>');

		if(message == "") {
			ohSnap('Veuillez remplir le champs de texte', 'red');
			$('#submittchat').html('Envoyer');
			$('#submittchat').removeAttr("disabled");
		} else {
			/* On ajoute le message dans BDD */
			var dataString = 'message=' + messageencoded + '&sendchatmsg=true';
			$.ajax({
				type: 'POST',
				url: '/app/ajax/chat-request.php',
				data: dataString,
				dataType: "JSON",
				success: function(reponse) {
					$('#messageinput').val('');
					$('#submittchat').html('Envoyer');
					$('#submittchat').removeAttr("disabled");

					var usermsg = reponse.usermsg;
					var nameuser = reponse.nameuser;
					var msg = reponse.message;
					var date = reponse.date;

					socket.emit('newmessagechat', { 'usermsg': usermsg, 'nameuser': nameuser, 'msg': msg, 'date': date } );
				}
			});
		}
		return false;
	});
	/**
	* A la reception d'un message dans le chat
	**/
	socket.on('newmessagechat', function(data) {
	    var newMsgContent = '<div id="message" style="padding: 20px 5px 0px 20px;"><a href="/profil/' + data.usermsg + '" class="no_underline" id="auteurmsg"><strong>' + data.nameuser + '</strong></a> <small style="font-style: italic;">' + data.date + '</small><p id="' + data.idmsg + '" style="word-wrap: break-word;">' + data.msg + '</p></div>';

	    if(('#tchat').length > 0) {
	    	$("#tchat").append(newMsgContent);
	    	
		    emojify.run();
			if($("#tchatmenu").hasClass('canvas-slid') == false && $('.chatmobile').length == 0) {
				playSound('/assets/sound/pop-chat.mp3');
				ohSnap('Nouveau message dans le chat', 'green');
				$("#opentchat").html('<i class="fa fa-chevron-left"></i> <sup><i class="fa fa-plus-circle" style="color: #0F9F28;"></i></sup>');
			}

			$('#tchat').animate({scrollTop : $('#tchat').prop('scrollHeight') }, 1000);
			linktohref();
		    $("time.timeago").timeago();
	    }
	});
	/**
	* Envoi d'un message privé
	**/
	$("#send_msgprive").submit(function(event){
		event.preventDefault();

		var msg_content = $.trim($('#inputMsg').val());
		msg_content = encodeURIComponent(msg_content);
		var userid = $('#submitSend').attr('data-usermsg');
		var id_conv = $('#submitSend').attr('data-idconv');

		var dataString = 'msg_content=' + msg_content + '&pseudo_dest=' + userid + '&id_conv=' + id_conv + '&addmsg=true';

		$('#submitSend').attr("disabled", true);
		$('#submitSend').html('Envoi <i class="fa fa-spinner fa-spin"></i>');
		if(msg_content == "") {
			$('#errorMsg').html('Veuillez remplir le champs de texte').slideDown();
			$('#submitSend').html('Envoyer <i class="fa fa-paper-plane"></i>');
			$('#submitSend').removeAttr("disabled");
		} else {
			$.ajax({
				type: 'POST',
				url: '/app/ajax/msgprive-request.php',
				data: dataString,
				dataType: "JSON",
				success: function(msg) {
					if(msg.status == 1) {
						if($('#nomsg').length != 0) {
							$( "#nomsg" ).slideUp( "fast", function() {
								$('#nomsg').remove();
							});
						}
						$('#inputMsg').val('');
						$('#errorMsg').hide().html('');
						$('#submitSend').html('Envoyer <i class="fa fa-paper-plane"></i>');
						$('#submitSend').removeAttr("disabled");

						socket.emit('newmessageprive', { 'pseudo_dest' : userid, 'pseudo_exp' : msg.pseudo_exp } );
					} else {
						$('#errorMsg').html(msg.err).slideDown();
						$('#submitSend').html('Envoyer <i class="fa fa-paper-plane"></i>');
						$('#submitSend').removeAttr("disabled");
					}
				}
			});
		}
		return false;
	});
	/**
	* A la reception d'un message privé
	**/
	socket.on('newmessageprive', function(reponse) {
		var dataRequest = 'getinfouser=true';
		$.ajax({
			type: 'POST',
			url: '/ajax.php',
			data: dataRequest,
			dataType: "JSON",
			success: function(msg) {
				var user = msg.user;

				if((user == reponse.pseudo_dest || user == reponse.pseudo_exp)) {
					if($('#messages-prives').length == 0 && user == reponse.pseudo_dest) {
						var dataString = 'updatenotif=true';
						$.ajax({
							type: 'POST',
							url: '/ajax.php',
							data: dataString,
							dataType: "JSON",
							success: function(msg) {
								if(msg.status == 1) {
									/* Message privé */
									var suplengthmsg = $("li #limessage sup").length;
									if(suplengthmsg < 1) {
										var valueactualmsg = "0";
									} else {
										var valueactualmsg = $("li #limessage sup span").html();
									}
									/* MESSAGE PRIVE */
									/* On met le nbr de msg privé si > 0 sinon on efface le nbr */
									if($('#messages-prives').length == 0) {
										if(msg.nbrnewmsg > 0 && valueactualmsg != msg.nbrnewmsg) {
											$("li #limessage").html('Messages <sup><span class="badge badge-primary">' + msg.nbrnewmsg + '</span></sup>');
											ohSnap('Nouveau message privé reçu', 'green');
											playSound('/assets/sound/pop-chat.mp3');
											linktohref();
		    								$("time.timeago").timeago();
										} else if(msg.nbrnewmsg == 0) {
											$("li #limessage").html('Messages');
										}
									}
								}
							}
						});
					} else {
						var lastid = $('#messages-prives .msg:last').attr('id');
						var id_conv = $('#messagepost #inputMsg').attr('data-conv');
						if(lastid == "") {
							lastid = $('#messages-prives .msg').length;
						}
					    var dataString = 'lastmsgid=' + lastid + '&id_conv=' + id_conv + '&update_msg=true';
						$.ajax({
							type: 'POST',
							url: '/app/ajax/msgprive-request.php',
							data: dataString,
							dataType: "JSON",
							success: function(msg) {
								if(msg.status == 1) {
									$("#messages-prives").append(msg.view);
									emojify.run();
									$('#messages-prives').animate({scrollTop : $('#messages-prives').prop('scrollHeight') }, 1000);
								}
							}
						});
					}
				}
			}
		});
	});
	/**
	* Envoi d'une demande d'amis
	**/
	$('#form-content-friend').on('click','#addfriend',function(event) {
		event.preventDefault();
		var usertoadd = $("#form-content-friend #addfriend").attr('data-user');
		var usertoaddencoded = encodeURIComponent(usertoadd);

		$('#form-content-friend #addfriend').attr("disabled", true);
		$('#form-content-friend #addfriend').html('<i class="fa fa-user-plus"></i> Ajouter en ami <i class="fa fa-spinner fa-spin"></i>');

		if(usertoadd == "") {
			ohSnap('Erreur, actualisez', 'red');
			$('#form-content-friend #addfriend').html('<i class="fa fa-user-plus"></i> Ajouter en ami');
			$('#form-content-friend #addfriend').removeAttr("disabled");
		} else {
			var dataRequest = 'usertoadd=' + usertoaddencoded + '&addfriend=true';
			$.ajax({
				type: 'POST',
				url: '/app/ajax/friends-request.php',
				data: dataRequest,
				dataType: "JSON",
				success: function(reponse) {
					$('#form-content-friend').html('');
					$('#form-content-friend').html(reponse.view);

					var pseudo_exp = reponse.pseudo_exp;
					var pseudo_dest = reponse.pseudo_dest;

					socket.emit('newaskfriend', { 'pseudo_exp': pseudo_exp, 'pseudo_dest': pseudo_dest } );
				}
			});
		}
	});
	/**
	* Annuler demande d'amis
	**/
	$('#form-content-friend').on('click','#annuldemand',function(event) {
		event.preventDefault();

		var usertodelete = $("#form-content-friend #annuldemand").attr('data-user');
		var usertodeleteencoded = encodeURIComponent(usertodelete);

		$('#form-content-friend #annuldemand').attr("disabled", true);
		$('#form-content-friend #annuldemand').html('Annuler la demande <i class="fa fa-spinner fa-spin"></i>');

		if(usertodelete == "") {
			ohSnap('Erreur, actualisez', 'red');
			$('#form-content-friend #annuldemand').html('Annuler la demande');
			$('#form-content-friend #annuldemand').removeAttr("disabled");
		} else {
			var dataRequest = 'usertodelete=' + usertodeleteencoded + '&annulfriend=true';
			$.ajax({
				type: 'POST',
				url: '/app/ajax/friends-request.php',
				data: dataRequest,
				dataType: "JSON",
				success: function(reponse) {
					$('#form-content-friend').html('');
					$('#form-content-friend').html(reponse.view);
				}
			});
		}
		return false;
	});
	/**
	* Supprimer d'amis
	**/
	$('#form-content-friend').on('click','#supprfriend',function(event) {
		event.preventDefault();

		var usertodelete = $('#form-content-friend #supprfriend').attr('data-user');
		var usertodeleteencoded = encodeURIComponent(usertodelete);

		$('#form-content-friend #supprfriend').attr("disabled", true);
		$('#form-content-friend #supprfriend').html('Annuler la demande <i class="fa fa-spinner fa-spin"></i>');

		if(usertodelete == "") {
			ohSnap('Erreur, actualisez', 'red');
			$('#form-content-friend #supprfriend').html('Annuler la demande');
			$('#form-content-friend #supprfriend').removeAttr("disabled");
		} else {
			var dataRequest = 'usertodelete=' + usertodeleteencoded + '&supprfriend=true';
			$.ajax({
				type: 'POST',
				url: '/app/ajax/friends-request.php',
				data: dataRequest,
				dataType: "JSON",
				success: function(reponse) {
					$('#form-content-friend').html('');
					$('#form-content-friend').html(reponse.view);
				}
			});
		}
	});
	/**
	* A la reception d'une demande d'amis
	**/
	socket.on('newaskfriend', function(reponse) {
		var dataRequest = 'getinfouser=true';
		$.ajax({
			type: 'POST',
			url: '/ajax.php',
			data: dataRequest,
			dataType: "JSON",
			success: function(msg) {
				var user = msg.user;

				if(user == reponse.pseudo_dest) {
					checkfriendask("true");
				}
			}
		});
	});
})(jQuery);