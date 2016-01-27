<?php
/*|-------------------------------------------------
  | Class pour gérer les données d'un
  | utilisateur.
  |-------------------------------------------------
*/
class PROFIL {

    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

 	/*|-------------------------------------------------
	  | Fonction pour récupéré données profil utilisateur
	  |-------------------------------------------------
	*/
	public function get_profil_info($username) {

		try {
			$requeteinfo = $this->db->prepare("SELECT * FROM users WHERE pseudo=:pseudo OR id=:id LIMIT 1");
			$requeteinfo->execute(array(
		        'pseudo' => $username,
		        'id' => $username
		    ));
			$req = $requeteinfo->fetch(PDO::FETCH_ASSOC);
			
		    return $req;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour récupéré prenom & nom profil utilisateur
	  |-------------------------------------------------
	*/
	public function get_profil_info_name($username) {

		try {
			$requeteinfo = $this->db->prepare("SELECT prenomplusnom,prenom,nom FROM users WHERE pseudo=:pseudo OR id=:id LIMIT 1");
			$requeteinfo->execute(array(
		        'pseudo' => $username,
		        'id' => $username
		    ));
			$req = $requeteinfo->fetch(PDO::FETCH_ASSOC);
			
		    return $req;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}


	/*|-------------------------------------------------
	  | Fonction pour supprimer un utilisateur
	  |-------------------------------------------------
	*/
	public function delete_user($username, $id) {

		try {
			$deleteMembre = $this->db->prepare('DELETE FROM users WHERE pseudo=:pseudo AND session=:session AND id=:id');
            $deleteMembre->execute(array(
              'pseudo' => $username,
              'session' => $_SESSION['session'],
              'id' => $id
            ));
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}
	/*|-------------------------------------------------
	  | Fonction pour modifier données profil utilisateur
	  |-------------------------------------------------
	*/
	public function change_profil_info($username, $bio, $datedenaissance) {

		try {
			$updateInfoMembre = $this->db->prepare('UPDATE users SET bio=:bio, datedenaissance=:datedenaissance WHERE pseudo=:pseudo AND session=:session');
            $updateInfoMembre->execute(array(
              'pseudo' => $username,
              'session' => $_SESSION['session'],
              'bio' => $bio,
              'datedenaissance' => $datedenaissance
            ));
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}


	/*|-------------------------------------------------
	  | Fonction pour modifier mdp utilisateur
	  |-------------------------------------------------
	*/
	public function change_settings_password($username, $newmdp) {

		$newpassword = passwordhash($newmdp);

		try {
			$updateMdpMembre = $this->db->prepare('UPDATE users SET password=:password WHERE pseudo=:pseudo AND session=:session');
            $updateMdpMembre->execute(array(
              'pseudo' => $username,
              'session' => $_SESSION['session'],
              'password' => $newpassword
            ));
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour modifier email utilisateur
	  |-------------------------------------------------
	*/
	public function change_settings_email($username, $newemail) {

		try {
			$updateEmailMembre = $this->db->prepare('UPDATE users SET email=:email WHERE pseudo=:pseudo AND session=:session');
            $updateEmailMembre->execute(array(
              'pseudo' => $username,
              'session' => $_SESSION['session'],
              'email' => $newemail
            ));
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour modifier notifications utilisateur
	  |-------------------------------------------------
	*/
	/* public function change_settings_notif($username, $envoiMailMsgPrive, $envoiMailTag) {

		try {
			$updateNotifMembre = $this->db->prepare('UPDATE users SET envoiMailMsgPrive=:envoiMailMsgPrive, envoiMailTag=:envoiMailTag WHERE pseudo=:pseudo AND session=:session');
            $updateNotifMembre->execute(array(
              'pseudo' => $username,
              'session' => $_SESSION['session'],
              'envoiMailMsgPrive' => $envoiMailMsgPrive,
              'envoiMailTag' => $envoiMailTag
            ));
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}
	*/
	/*|-------------------------------------------------
	  | Fonction pour modifier confidentialite utilisateur
	  |-------------------------------------------------
	*/
	public function change_settings_conf($username, $allowFindSearch, $comptePrive, $notifMailPrive) {

		try {
			$updateNotifMembre = $this->db->prepare('UPDATE users SET notifMailPrive=:notifMailPrive, allowFindSearch=:allowFindSearch, comptePrive=:comptePrive WHERE pseudo=:pseudo AND session=:session');
            $updateNotifMembre->execute(array(
              'pseudo' => $username,
              'session' => $_SESSION['session'],
              'allowFindSearch' => $allowFindSearch,
              'comptePrive' => $comptePrive,
              'notifMailPrive' => $notifMailPrive
            ));
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}
}
?>