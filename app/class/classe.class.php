<?php
/*|-------------------------------------------------
  | Class pour gérer les amis d'un utilisateur
  | Ajout, suppresion, liste des amis
  |-------------------------------------------------
*/
class CLASSE {

    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

	/*|-------------------------------------------------
	  | Fonction pour récupéré les amis d'un utilisateur + lui même
	  |-------------------------------------------------
	*/
	public function get_user_classe_with_user($username) {

		try {
			      $getClasseMembre = $this->db->prepare('SELECT classe FROM users WHERE pseudo=:pseudo LIMIT 1');
            $getClasseMembre->execute(array(
              'pseudo' => $username
            ));
            $resultClasse = $getClasseMembre->fetch();
            $classresult = $resultClasse['classe'];

            $getListClasseMembre = $this->db->prepare('SELECT * FROM users WHERE classe=:classe');
            $getListClasseMembre->execute(array(
              'classe' => $classresult
            ));

            $listclasse = array($username);

            if($getClasseMembre->rowCount() > 0) {
            	if($getListClasseMembre->rowCount() > 0 ) {
      					while($req = $getListClasseMembre->fetch()) {
      						$listclasse[] = $req['pseudo'];
      					}
			    	    return $listclasse;
			        } else {
            	  return $listclasse;
            	}
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
	  | Fonction pour récupéré les amis d'un utilisateur
	  |-------------------------------------------------
	*/
	public function get_user_classe($username) {

		try {
			      $getClasseMembre = $this->db->prepare('SELECT classe FROM users WHERE pseudo=:pseudo LIMIT 1');
            $getClasseMembre->execute(array(
              'pseudo' => $username
            ));
            $resultClasse = $getClasseMembre->fetch();
            
            $getListClasseMembre = $this->db->prepare('SELECT * FROM users WHERE classe=:classe');
            $getListClasseMembre->execute(array(
              'classe' => $resultClasse['classe']
            ));
            
            $listclasse = array();
            
            if($getClasseMembre->rowCount() > 0) {
            	if($getListClasseMembre->rowCount() > 0) {
      					while($req = $getListClasseMembre->fetch()) {
      						$listclasse[] = $req['pseudo'];
      					}
      			    return $listclasse;
      			  } else {
            		return $listclasse;
            	}
            } else {
            	return false;
            }
	  }
    catch(PDOException $e) {
      echo $e->getMessage();
    }
	}

	/*|-------------------------------------------------
	  | Fonction pour vérifier si le profil appartient
	  | à un amis
	  |-------------------------------------------------
	*/
	public function verif_classe($username, $friend) {

		try {
			  $getClasseMembre = $this->db->prepare('SELECT classe FROM users WHERE pseudo=:pseudo LIMIT 1');
        $getClasseMembre->execute(array(
          'pseudo' => $username
        ));
        $resultClasse = $getClasseMembre->fetch();

	      $verifClasseMembre = $this->db->prepare('SELECT * FROM users WHERE pseudo=:pseudo AND classe=:classe LIMIT 1');
        $verifClasseMembre->execute(array(
          'pseudo' => $friend,
          'classe' => $resultClasse
        ));
        $listinfo = $verifClasseMembre->fetch();

	      if($verifClasseMembre->rowCount() > 0) {
		      if($listinfo['classe'] == $resultClasse) {
			        return true;
		      } else {
			        return false;
		      }
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
	  | reçu une demande
	  |-------------------------------------------------
	*/
	public function nbr_membre_classe($username) {

		try {
	      $getClasseMembre = $this->db->prepare('SELECT classe FROM users WHERE pseudo=:pseudo LIMIT 1');
        $getClasseMembre->execute(array(
          'pseudo' => $username
        ));
        $resultClasse = $getClasseMembre->fetch();

  			$listclasse = $this->db->prepare("SELECT * FROM users WHERE classe=:classe");
  			$listclasse->execute(array(
  				'classe' => $resultClasse
  			));
  			$nbrmembre = $listclasse->rowCount();
  			if($nbrmembre > 0) {
  				return $nbrmembre;
  			} else {
  				return "0";
  			}
    }
    catch(PDOException $e)
    {
       echo $e->getMessage();
    }
	}

  public function addevent($name_event, $desc_event, $date_event, $type_event, $classe, $public_event) {
    try {
        $addevent = $this->db->prepare('INSERT INTO evenements(name_event,desc_event,date_event,type_event,classe,public) VALUES(:name_event, :desc_event, :date_event, :type_event, :classe, :public)');
        $addevent->execute(array(
          'name_event' => $name_event,
          'desc_event' => $desc_event,
          'date_event' => $date_event,
          'type_event' => $type_event,
          'classe' => $classe,
          'public' => $public_event
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