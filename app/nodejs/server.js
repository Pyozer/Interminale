var http = require('http');

httpServer = http.createServer(function(req, res) {
	console.log('Hello World');
});

httpServer.listen(3000);

var allnumbers = 0;
var users = {};

var io = require('socket.io').listen(httpServer);

io.sockets.on('connection', function(socket) {
    console.log("Utilisateur connecté");

    socket.on('userinfo', function(user) {
        pseudo = user.username;
        user_id = user.iduser;
        socket.pseudo = pseudo;
        socket.user_id = user_id;
        socket.join(pseudo);
    });
    /**
    * On reçois message chat
    **/
    socket.on('newmessagechat', function(data) {
    	console.log("Message dans le chat par " + data.nameuser);
        io.sockets.emit('newmessagechat', { 'usermsg' : data.usermsg, 'nameuser' : data.nameuser, 'msg' : data.msg, 'date' : data.date } );
    });

    /**
    * On reçois message privé
    **/
    socket.on('newmessageprive', function(data) {
    	console.log("Message privé à " + data.pseudo_dest + " par " + data.pseudo_exp);
        /* io.sockets.emit('newmessageprive', { 'pseudo_dest' : data.pseudo_dest, 'pseudo_exp' : data.pseudo_exp } ); */
        io.sockets.in(data.pseudo_exp).emit('newmessageprive', { 'pseudo_dest' : data.pseudo_dest, 'pseudo_exp' : data.pseudo_exp } );
        io.sockets.in(data.pseudo_dest).emit('newmessageprive', { 'pseudo_dest' : data.pseudo_dest, 'pseudo_exp' : data.pseudo_exp } );
    });

    /**
    * On reçois une demande d'amis
    **/
    socket.on('newaskfriend', function(data) {
        console.log("Demande d'amis à " + data.pseudo_dest + " par " + data.pseudo_exp);
        /* io.sockets.emit('newaskfriend', { 'pseudo_dest' : data.pseudo_dest, 'pseudo_exp' : data.pseudo_exp } ); */
        io.sockets.in(data.pseudo_dest).emit('newaskfriend', { 'pseudo_dest' : data.pseudo_dest, 'pseudo_exp' : data.pseudo_exp } );
    });


    /** =============== AddOne  ================= **/
    /** ========================================= **/

    /**
    * On envoi le nombre actuelle
    **/
    socket.emit('allnumber', allnumbers);
	socket.emit('allscore', users);
    /**
    * Connection d'un utilisateur
    **/
    socket.on('login', function(username){
    	// On sauvegarde le pseudo de l'utilisateur
        socket.username = username;
        // On vérifie si le pseudo existe déjà
        if(users[socket.username]) {
        	// On défini son score
        	socket.totaladdoner = users[socket.username]["totalscore"];
        	socket.iduser = users[socket.username]["iduser"];
        } else {
        	// Si il existe pas on créer les variables
        	socket.totaladdoner = 0;
        	var iduserk = 0;
        	for(k in users) {
				iduserk++;
		    }
		    socket.iduser = iduserk + 1;
        }
        // On stock dans une variable ses infos
        users[socket.username] = { 'pseudo': socket.username, 'totalscore': socket.totaladdoner, 'iduser': socket.iduser };
        io.sockets.emit('nouveau_client', socket.username);
        socket.emit('logged');
    });

    /**
    * Quand quelqu'un ajoute +1
    **/
    socket.on('addone', function(data) {
    	// +1 au nombre actuelle
        allnumbers = allnumbers + 1;
        // +1 pour l'utilisateur
        socket.totaladdoner = socket.totaladdoner + 1;
        users[socket.username]["totalscore"] = socket.totaladdoner;
        // On renvoi le nombre total avec en + l'ajouteur et son nombre total
        io.sockets.emit('addone', { 'number': allnumbers, 'useradder': socket.username, 'totaladdoner': socket.totaladdoner, 'addonerid': socket.iduser });
    });
});