<?php
/*|-------------------------------------------------
  | Class pour gérer les messages d'un utilisateur
  |-------------------------------------------------
*/
class MESSAGE {

    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

 	/*|-------------------------------------------------
	  | Fonction pour avoir info d'un messages
	  |-------------------------------------------------
	*/
	public function get_message($msg_id) {

		try {
			$requetemsg = $this->db->prepare("SELECT * FROM messages WHERE id=:id LIMIT 1");
			$requetemsg->execute(array(
		        'id' => $msg_id
		    ));
			$req = $requetemsg->fetch();
			
		    return $req;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour avoir nbr de messages non lus
	  |-------------------------------------------------
	*/
	public function get_nbr_non_lus($username) {

		try {
			$requetenbrmsg = $this->db->prepare("SELECT id FROM messages WHERE pseudo_dest=:pseudo_dest AND lu=:lu");
			$requetenbrmsg->execute(array(
		        'pseudo_dest' => $username,
		        'lu' => "0"
		    ));
			$req = $requetenbrmsg->rowCount();

			if($req == 0) {
				$reponse = "0";
			} else {
				$reponse = $req;
			}
			
		    return $reponse;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour mettre un message en lu
	  |-------------------------------------------------
	*/
	public function msg_put_read($id_conv) {
		try {
			$putreadmsg = $this->db->prepare("UPDATE messages SET lu=:lu WHERE id_conv=:id_conv AND pseudo_dest=:me");
			$putreadmsg->execute(array(
		        'id_conv' => $id_conv,
		        'me' => $_SESSION['username'],
		        'lu' => 1
		    ));
			
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour envoyer un message prive
	  |-------------------------------------------------
	*/
	public function envoi_msg($pseudo_exp, $pseudo_dest, $msg, $id_conv) {

		$date = time();

		try {
			$newmsg = $this->db->prepare('INSERT INTO messages(id_conv, pseudo_dest, pseudo_exp, message, date_msg, lu) VALUES(:id_conv, :pseudo_dest, :pseudo_exp, :message, :date_msg, :lu)');
            $newmsg->execute(array(
            	'id_conv' => $id_conv,
            	'pseudo_dest' => $pseudo_dest,
            	'pseudo_exp' => $pseudo_exp,
            	'message' => $msg,
            	'date_msg' => $date,
            	'lu' => 0
            ));
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour avoir derniere message prive
	  |-------------------------------------------------
	*/
	public function get_message_from_user($username) {

		try {
			$selectmsg = $this->db->prepare("SELECT * FROM messages WHERE pseudo_exp=:auteur ORDER BY id DESC LIMIT 1");
			$selectmsg->execute(array(
				'auteur' => $username
			));

			$message = array();
			$result = $message->fetch();

            return $result;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}
}
?>