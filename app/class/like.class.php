<?php
/*|-------------------------------------------------
  | Class pour gérer les likes d'un post
  | (Vérifier si like, récupérer nbr like, etc..)
  |-------------------------------------------------
*/
class LIKE{

    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

 	/*|-------------------------------------------------
	  | Fonction pour un like à un post
	  |-------------------------------------------------
	*/
	public function addlike($id_post, $user_id) {

		try {
			$selectlike = $this->db->prepare("SELECT likes FROM posts WHERE id=:id_post LIMIT 1");
			$selectlike->execute(array(
		        'id_post' => $id_post
		    ));

			$addlikefetch = $selectlike->fetch();

		    $listliker = $addlikefetch['likes'];
		    $arraylistliker = explode(',', $listliker);

		    /* Si déjà like */
		    if (in_array($user_id, $arraylistliker)) {
		    	return false;
		    } else {
		    	$addliker = $listliker.$user_id.",";

		    	$addlike = $this->db->prepare("UPDATE posts SET likes=:likes WHERE id=:id_post");
				$addlike->execute(array(
					'likes' => $addliker,
			        'id_post' => $id_post
			    ));

			    return true;
		    }
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour un unlike à un post
	  |-------------------------------------------------
	*/
	public function unlike($id_post, $user_id) {

		try {
			$selectlike = $this->db->prepare("SELECT likes FROM posts WHERE id=:id_post LIMIT 1");
			$selectlike->execute(array(
		        'id_post' => $id_post
		    ));

			$addlikefetch = $selectlike->fetch();

		    $listliker = $addlikefetch['likes'];
		    $arraylistliker = explode(',', $listliker);

		    $user_idarray[] = $user_id;

		    /* Si déjà like */
		    if (in_array($user_id, $arraylistliker)) {
		    	$arraylistliker = array_diff($arraylistliker, $user_idarray);
	    		$newlist = implode(',', $arraylistliker);

		    	$unlike = $this->db->prepare("UPDATE posts SET likes=:likes WHERE id=:id_post");
				$unlike->execute(array(
					'likes' => $newlist,
			        'id_post' => $id_post
			    ));
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
	  | Fonction pour un vérifier si user à like un post
	  |-------------------------------------------------
	*/
	public function verif_if_like($id_post, $user_id) {

		try {
			$selectlike = $this->db->prepare("SELECT likes FROM posts WHERE id=:id_post LIMIT 1");
			$selectlike->execute(array(
		        'id_post' => $id_post
		    ));

			$addlikefetch = $selectlike->fetch();

		    $listliker = $addlikefetch['likes'];
		    $arraylistliker = explode(',', $listliker);

		    /* Si déjà like */
		    if(in_array($user_id, $arraylistliker)) {
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
	  | Fonction pour un récupéré nbr de like d'un post
	  |-------------------------------------------------
	*/
	public function get_nbr_likes($id_post) {

		try {
			$selectlikes = $this->db->prepare("SELECT likes FROM posts WHERE id=:id_post LIMIT 1");
			$selectlikes->execute(array(
		        'id_post' => $id_post
		    ));

			$likes = $selectlikes->fetch();

		    $listlikes = $likes['likes'];
		    $listlikes = explode(',', $listlikes);
		    $nbrlikes = count($listlikes) - 1;

		    return $nbrlikes;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour un récupéré nbr de like d'un post
	  |-------------------------------------------------
	*/
	public function get_nbr_likes_user($user) {

		try {
			$selectnbrlikes = $this->db->prepare("SELECT likes FROM posts");
			$selectnbrlikes->execute();

			$nbrlike = 0;
			while($likes = $selectnbrlikes->fetch()) {
				$listlikes = $likes['likes'];
	    		$arraylistliker = explode(',', $listlikes);

	    		if(in_array($user, $arraylistliker)) {
			    	$nbrlike++;
			    }
			}
            return $nbrlike;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}
}
?>