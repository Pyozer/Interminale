<?php
/*|-------------------------------------------------
  | Class pour connecté l'utilisateur, vérifié
  | si connecté, déconnexion, redirection.
  |-------------------------------------------------
*/
class USER
{
    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

   	/*|-------------------------------------------------
  	  | Fonction pour inscrire l'utilisateur
  	  |-------------------------------------------------
  	*/
    public function register($pseudo,$prenom,$nom,$email,$password,$classe,$datedenaissance,$sexe)
    {
       try
       {
          $passwordhash = passwordhash($password);

          $dateinscription = date('Y-m-d', time());
          $prenomplusnom = $prenom." ".$nom;
          $lastco = strftime('%d %B %Y à %H:%M');
   
          $stmt = $this->db->prepare("INSERT INTO users(pseudo,prenom,nom,prenomplusnom,email,password,classe,datedenaissance,sexe,dateinscription,notifMailPrive,allowFindSearch,comptePrive,lastco) VALUES(:pseudo, :prenom, :nom, :prenomplusnom, :email, :password, :classe, :datedenaissance, :sexe, :dateinscription, :notifMailPrive, :allowFindSearch, :comptePrive, :lastco)");   
          $stmt->execute(array(
            'pseudo' => $pseudo,
            'prenom' => $prenom,
            'nom' => $nom,
            'prenomplusnom' => $prenomplusnom,
            'email' => $email,
            'password' => $passwordhash,
            'classe' => $classe,
            'datedenaissance' => $datedenaissance,
            'sexe' => $sexe,
            'dateinscription' => $dateinscription,
            'notifMailPrive' => 'true',
            'allowFindSearch' => 'true',
            'comptePrive' => 'false',
            'lastco' => $lastco
          ));
   
          return true; 
       }
       catch(PDOException $e)
       {
           echo $e->getMessage();
       }
    }
 
    /*|-------------------------------------------------
      | Fonction pour connecter l'utilisateur
      |-------------------------------------------------
    */
    public function login($username,$password)
    {
       try
       {
          $stmt = $this->db->prepare("SELECT id,prenom,nom,email,password,pseudo,classe FROM users WHERE email=:email LIMIT 1");
          $stmt->execute(array(
            'email'=> mb_strtolower($username, 'UTF-8')
          ));
          $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
          if($stmt->rowCount() > 0)
          {
             if(password_verify($password, $userRow['password']))
             {
                $session = md5(rand());
                $lastco = strftime('%d %B %Y à %H:%M');
                $updateMembre = $this->db->prepare('UPDATE users SET session=:session, lastco=:lastco WHERE email=:email');
                $updateMembre->execute(array(
                  'email' => $username,
                  'session' => $session,
                  'lastco' => $lastco
                ));
                $_SESSION['session'] = $session;
                $_SESSION['userid'] = $userRow['id'];
                $_SESSION['userpseudo'] = $userRow['prenom']." ".$userRow['nom'];
                $_SESSION['username'] = $userRow['pseudo'];
                $_SESSION['userclasse'] = $userRow['classe'];

                if(isset($_POST['rememberme'])) {
                  $contentcook = $userRow['id']."===".sha1($username.$_SERVER['REMOTE_ADDR']);
                  setcookie('auth', $contentcook, time() + 3600 * 24 * 7, '/', 'www.interminale.fr.nf', false, true);
                }
                return true;
             }
             else
             {
                return false;
             }
          }
          else
          {
            return false;
          }
       }
       catch(PDOException $e)
       {
          die('<h1>ERREUR LORS DE LA CONNEXION A LA BASE DE DONNEE. <br />REESAYEZ ULTERIEUREMENT</h1>');
       }
    }

    /*|-------------------------------------------------
      | Fonction pour connecter l'utilisateur
      |-------------------------------------------------
    */
    public function forget_password($username)
    {
       try
       {
          $stmt = $this->db->prepare("SELECT email FROM users WHERE email=:email LIMIT 1");
          $stmt->execute(array(
            'email'=> mb_strtolower($username, 'UTF-8')
          ));
          $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
          if($stmt->rowCount() > 0)
          {
            $tokenreset = sha1(uniqid().$username);
            $linkreset = "http://interminale.fr.nf/forget_password?email=".$username."&token=".$tokenreset;

            $addmdpoublie = $this->db->prepare("INSERT INTO forget_password(email,token) VALUES(:email, :token)");
            $addmdpoublie->execute(array(
              'email'=> mb_strtolower($username, 'UTF-8'),
              'token' => $tokenreset
            ));

            require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/phpmailer/PHPMailerAutoload.php';

            // Retrieve the email template required
            $msg_html = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/app/view/email-forgetpassword.template.html');

            // Replace the % with the actual information
            $msg_html = str_replace('%linkreset%', $linkreset, $msg_html);

            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';
            $mail->SMTPAuth = true;
            $mail->Username = "enjoycraftfr@gmail.com";
            $mail->Password = "~Py+Ai3j(O5g8!";
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('no-reply@interminale.fr', 'Interminale');
            $mail->addReplyTo($username);
            $mail->addAddress($username);
            $mail->Subject = 'Réinitialisation mot de passe - Interminale';
            $mail->Body = $msg_html;

            $mail->AltBody = strip_tags($msg_html);

            //send the message, check for errors
            if (!$mail->send()) {
                $message = array('status' => 0, 'err' => "L'email n'a pas pu être envoyé :/ Réessayez.");
                return $message;
            } else {
                $message = array('status' => 1, 'err' => 'Un mail a été envoyé à '.$username.'.<br />Cliquez sur le lien reçu pour changer le mot de passe');
                return $message;
            }
          }
          else
          {
            $message = array('status' => 0, 'err' => 'L\'adresse mail ne conrespond à aucun compte :/');
            return $message;
          }
       }
       catch(PDOException $e)
       {
          die('<h1>ERREUR LORS DE LA CONNEXION A LA BASE DE DONNEE. <br />REESAYEZ ULTERIEUREMENT</h1>');
       }
    }
 	
   	/*|-------------------------------------------------
  	  | Fonction pour vérifier si un utilisateur est connecté
  	  |-------------------------------------------------
  	*/
    public function valid_user_connected($id_user)
      {
  		try
  		{
  			$last_ping = time();
  			/* On supprime les anciennes présences de connexion */
  			$removeoldco = $this->db->prepare("DELETE FROM connected_user WHERE id_user=:id_user AND last_ping < :last_ping");
  			$removeoldco->execute(array(
  				'id_user' => $id_user,
  				'last_ping' => $last_ping
  			));
  			/* On valide la présence de l''utilisateur */
  			$stmt = $this->db->prepare("INSERT INTO connected_user(id_user,last_ping) VALUES(:id_user, :last_ping)");
  			$stmt->execute(array(
  				'id_user' => $id_user,
  				'last_ping' => $last_ping
  			));
  			return true;
  		}
  		catch(PDOException $e) {
  			die('<h1>ERREUR LORS DE LA CONNEXION A LA BASE DE DONNEE. <br />REESAYEZ ULTERIEUREMENT</h1>');
  		}
    }

   	/*|-------------------------------------------------
  	  | Fonction pour vérifier si un utilisateur est connecté
  	  |-------------------------------------------------
  	*/
    public function user_connected($id_user) {
  		try
  		{
  			$max_last_ping = time() - 30;
  			$stmt = $this->db->prepare("SELECT * FROM connected_user WHERE id_user=:id_user AND last_ping > :last_ping LIMIT 1");
  			$stmt->execute(array(
  				'id_user' => $id_user,
  				'last_ping' => $max_last_ping
  			));
  			if($stmt->rowCount() > 0) {
  				return true;
  			} else {
  				return false;
  			}
  		}
  		catch(PDOException $e) {
  			die('<h1>ERREUR LORS DE LA CONNEXION A LA BASE DE DONNEE. <br />REESAYEZ ULTERIEUREMENT</h1>');
  		}
    }

    /*|-------------------------------------------------
      | Fonction pour avoir tous les utilisateur de co
      |-------------------------------------------------
    */
    public function all_user_connected() {
      try
      {
        $max_last_ping = time() - 31;
        $stmt = $this->db->prepare("SELECT * FROM connected_user WHERE last_ping > :last_ping");
        $stmt->execute(array(
          'last_ping' => $max_last_ping
        ));

        while($result = $stmt->fetch()) {
          $listid[] = $result['id_user'];
        }
        
        return $listid;
      }
      catch(PDOException $e) {
        die('<h1>ERREUR LORS DE LA CONNEXION A LA BASE DE DONNEE. <br />REESAYEZ ULTERIEUREMENT</h1>');
      }
    }

    /*|-------------------------------------------------
  	  | Fonction pour supprimer utilisateur connecté
  	  |-------------------------------------------------
  	*/
    public function remove_user_connected($id_user) {
    		try
    		{
    			/* On supprime les anciennes présences de connexion */
    			$removeoldco = $this->db->prepare("DELETE FROM connected_user WHERE id_user=:id_user");
    			$removeoldco->execute(array(
    				'id_user' => $id_user
    			));
    			return true;
    		}
    		catch(PDOException $e) {
    			die('<h1>ERREUR LORS DE LA CONNEXION A LA BASE DE DONNEE. <br />REESAYEZ ULTERIEUREMENT</h1>');
    		}
    }

    /*|-------------------------------------------------
      | Fonction pour récuperer tous les utilisateurs inscrit
      |-------------------------------------------------
    */
    public function all_user_register() {
        try
        {
          /* On récupères tous les utilisateurs */
          $getListMembres = $this->db->prepare('SELECT id,pseudo,prenomplusnom FROM users ORDER BY prenomplusnom ASC');
          $getListMembres->execute();
          return $getListMembres;
        }
        catch(PDOException $e) {
          die('<h1>ERREUR LORS DE LA CONNEXION A LA BASE DE DONNEE. <br />REESAYEZ ULTERIEUREMENT</h1>');
        }
    }

    /*|-------------------------------------------------
	    | Fonction pour rediriger l'utilisateur
	    |-------------------------------------------------
	  */
    public function redirect($url)
    {
        header("Location: $url");
        exit;
    }
   
    /*|-------------------------------------------------
  	  | Fonction pour déconnecter l'utilisateur
  	  |-------------------------------------------------
  	*/
    public function logout()
    {
        /* On détruit toutes les variable de $_SESSION */
        $_SESSION = array('');
        unset($_SESSION);
        session_destroy();
        setcookie('auth', '', time() - 3600 * 24, '/', 'www.interminale.fr.nf', false, true);
        return true;
    }
}
?>