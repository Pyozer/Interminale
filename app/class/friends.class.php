<?php
/*|-------------------------------------------------
  | Class pour gérer les amis d'un utilisateur
  | Ajout, suppresion, liste des amis
  |-------------------------------------------------
*/
class AMIS {

    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

 	/*|-------------------------------------------------
	  | Fonction pour faire une demande d'amis
	  |-------------------------------------------------
	*/
	public function add_friend($username, $user_to_add) {

		try {
			$verif = $this->db->prepare('SELECT * FROM amis WHERE (pseudo_exp=:username AND pseudo_dest=:usertwo) OR (pseudo_exp=:usertwo AND pseudo_dest=:username)');
            $verif->execute(array(
              'username' => $username,
              'usertwo' => $user_to_add
            ));

            if($verif->rowCount() > 0) {
            	$addfriend = $this->db->prepare("UPDATE amis SET active=:active WHERE (pseudo_exp=:username AND pseudo_dest=:usertwo) OR (pseudo_exp=:usertwo AND pseudo_dest=:username)");
				$addfriend->execute(array(
					'username' => $username,
              		'usertwo' => $user_to_add,
					'active' => '1'
				));
				$arr = array('status' => 1, 'alreadyask' => 1);
		    	return $arr;
            } else {
            	$addfriend = $this->db->prepare("INSERT INTO amis(pseudo_exp,pseudo_dest,active) VALUES(:pseudo_exp, :pseudo_dest, :active)");
				$addfriend->execute(array(
					'pseudo_exp' => $username,
					'pseudo_dest' => $user_to_add,
					'active' => 0
				));
		    	$arr = array('status' => 1, 'alreadyask' => 0);
		    	return $arr;
            }
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour supprimer l'amis d'un utilisateur
	  |-------------------------------------------------
	*/
	public function delete_friend($username, $user_to_delete) {

		try {
			$deleteMembre = $this->db->prepare('DELETE FROM amis WHERE (pseudo_exp=:username AND pseudo_dest=:usertwo) OR (pseudo_exp=:usertwo AND pseudo_dest=:username)');
            $deleteMembre->execute(array(
              'username' => $username,
              'usertwo' => $user_to_delete
            ));
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour récupéré les amis d'un utilisateur + lui même
	  |-------------------------------------------------
	*/
	public function get_friends_with_user($username) {

		try {
			$getFriendsMembre = $this->db->prepare('SELECT pseudo_dest FROM amis WHERE active=:active AND pseudo_exp=:pseudo_exp');
            $getFriendsMembre->execute(array(
              'pseudo_exp' => $username,
              'active' => 1
            ));

            $getFriendsMembre2 = $this->db->prepare('SELECT pseudo_exp FROM amis WHERE active=:active AND pseudo_dest=:pseudo_dest');
            $getFriendsMembre2->execute(array(
              'pseudo_dest' => $username,
              'active' => 1
            ));

            $listamis = array($username);

            if($getFriendsMembre->rowCount() > 0 || $getFriendsMembre2->rowCount() > 0 ) {
				while($req = $getFriendsMembre->fetch()) {
					$listamis[] = $req['pseudo_dest'];
				}
				while($req2 = $getFriendsMembre2->fetch()) {
					$listamis[] = $req2['pseudo_exp'];
				}
		    	return $listamis;
            } else {
            	return $listamis;
            }
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour récupéré les amis d'un utilisateur
	  |-------------------------------------------------
	*/
	public function get_friends($username) {

		try {
			$getFriendsMembre = $this->db->prepare('SELECT pseudo_dest FROM amis WHERE active=:active AND pseudo_exp=:pseudo_exp');
            $getFriendsMembre->execute(array(
              'pseudo_exp' => $username,
              'active' => 1
            ));

            $getFriendsMembre2 = $this->db->prepare('SELECT pseudo_exp FROM amis WHERE active=:active AND pseudo_dest=:pseudo_dest');
            $getFriendsMembre2->execute(array(
              'pseudo_dest' => $username,
              'active' => 1
            ));

            $listamis = array();

            if($getFriendsMembre->rowCount() > 0 || $getFriendsMembre2->rowCount() > 0 ) {
				while($req = $getFriendsMembre->fetch()) {
					$listamis[] = $req['pseudo_dest'];
				}
				while($req2 = $getFriendsMembre2->fetch()) {
					$listamis[] = $req2['pseudo_exp'];
				}
		    	return $listamis;
            } else {
            	return false;
            }
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour vérifier si le profil appartient
	  | à un amis
	  |-------------------------------------------------
	*/
	public function verif_friend($username, $friend) {

		try {
			$verifFriendsMembre = $this->db->prepare('SELECT * FROM amis WHERE active=:active AND ((pseudo_exp=:username OR pseudo_dest=:username) AND (pseudo_exp=:friend OR pseudo_dest=:friend)) LIMIT 1');
            $verifFriendsMembre->execute(array(
              'friend' => $friend,
              'friend' => $friend,
              'username' => $username,
              'username' => $username,
              'active' => 1
            ));

			if($verifFriendsMembre->rowCount() > 0) {
				return true;
			} else {
				return false;
			}
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour vérifier si l'utilisateur à
	  | FAIT une demande
	  |-------------------------------------------------
	*/
	public function verif_ask_demande($username, $user_to_add) {

		try {
			$verifaskdemand = $this->db->prepare("SELECT pseudo_dest FROM amis WHERE pseudo_exp=:pseudo_exp AND pseudo_dest=:pseudo_dest AND active=:active");
			$verifaskdemand->execute(array(
				'pseudo_exp' => $username,
				'pseudo_dest' => $user_to_add,
				'active' => "0"
			));

			if($verifaskdemand->rowCount() > 0) {
				return true;
			} else {
				return false;

			}
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour vérifier si l'utilisateur à
	  | RECU une demande
	  |-------------------------------------------------
	*/
	public function verif_demande($username) {

		try {
			$verifdemand = $this->db->prepare("SELECT * FROM amis WHERE pseudo_dest=:pseudo_dest AND active=:active");
			$verifdemand->execute(array(
				'pseudo_dest' => $username,
				'active' => 0
			));

			$req = $verifdemand->fetch(PDO::FETCH_ASSOC);
			$resultat = $req['active'];

			if($resultat == 1) {
				return false;
			} else {
				return true;

			}
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour vérifier si l'utilisateur à
	  | reçu une demande
	  |-------------------------------------------------
	*/
	public function nbr_ask_demande($username) {

		try {
			$verifaskdemand = $this->db->prepare("SELECT pseudo_exp FROM amis WHERE pseudo_dest=:pseudo_dest AND active=:active");
			$verifaskdemand->execute(array(
				'pseudo_dest' => $username,
				'active' => "0"
			));
			$nbrdemande = $verifaskdemand->rowCount();
			if($nbrdemande > 0) {
				return $nbrdemande;
			} else {
				return "0";
			}
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour accepter un utilisateur dans ses amis
	  |-------------------------------------------------
	*/
	public function accept_demand($idinvitation, $friendtoaccept) {
		try {
			$acceptMembre = $this->db->prepare('UPDATE amis SET active=:active WHERE pseudo_exp=:pseudo_exp AND id_invitation=:id_invitation');
            $acceptMembre->execute(array(
              'active' => 1,
              'pseudo_exp' => $friendtoaccept,
              'id_invitation' => $idinvitation
            ));
		    return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}


	/*|-------------------------------------------------
	  | Fonction pour refuser un utilisateur dans ses amis
	  |-------------------------------------------------
	*/
	public function denied_demand($idinvitation, $friendtoaccept) {
		try {
			$acceptMembre = $this->db->prepare('DELETE FROM amis WHERE pseudo_exp=:pseudo_exp AND id_invitation=:id_invitation');
            $acceptMembre->execute(array(
              'pseudo_exp' => $friendtoaccept,
              'id_invitation' => $idinvitation
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