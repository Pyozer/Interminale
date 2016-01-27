<?php
/*|-------------------------------------------------
  | Class pour gérer les likes d'un post
  | (Vérifier si like, récupérer nbr like, etc..)
  |-------------------------------------------------
*/
class COMMENT{

    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

 	/*|-------------------------------------------------
	  | Fonction pour ajouter un commentaire à un post
	  |-------------------------------------------------
	*/
	public function addcomment($id_post, $username, $commentaire) {

		try {
			$date = time();

			$addcomment = $this->db->prepare("INSERT INTO comments(id_post,auteur,content,date_comment) VALUES(:id_post, :auteur, :content, :date_comment)");   
	        $addcomment->execute(array(
	            'id_post' => $id_post,
	            'auteur' => $username,
	            'content' => $commentaire,
	            'date_comment' => $date
	        ));
	        return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour supprimer un commentaire à un post
	  |-------------------------------------------------
	*/
	public function supprcomment($id_comment, $username) {

		try {
			$supprcomment = $this->db->prepare("DELETE FROM comments WHERE id=:id AND auteur=:auteur");   
	        $supprcomment->execute(array(
	            'id' => $id_comment,
	            'auteur' => $username
	        ));
	        return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour supprimer un commentaire à un post
	  |-------------------------------------------------
	*/
	public function suppr_all_comments($idpost) {

		try {
			$supprcomments = $this->db->prepare("DELETE FROM comments WHERE id_post=:id_post");   
	        $supprcomments->execute(array(
	            'id_post' => $idpost
	        ));
	        return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour un récupéré comments d'un post
	  |-------------------------------------------------
	*/
	public function get_comments($id_post) {

		try {
			$selectcomments = $this->db->prepare("SELECT * FROM comments WHERE id_post=:id_post");
			$selectcomments->execute(array(
		        'id_post' => $id_post
		    ));

			$req = $selectcomments->fetchAll(PDO::FETCH_ASSOC);
			
		    return $req;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour un récupéré dernier comments d'un user
	  |-------------------------------------------------
	*/
	public function get_last_comment_user($id_post, $username) {

		try {
			$selectcomments = $this->db->prepare("SELECT * FROM comments WHERE id_post=:id_post AND auteur=:auteur ORDER BY id DESC LIMIT 1");
			$selectcomments->execute(array(
		        'id_post' => $id_post,
		        'auteur' => $username
		    ));

			$req = $selectcomments->fetch();
			
		    return $req;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour un récupéré nbr de comments d'un post
	  |-------------------------------------------------
	*/
	public function get_nbr_comments($id_post) {

		try {
			$selectcomments = $this->db->prepare("SELECT id_post FROM comments WHERE id_post=:id_post");
			$selectcomments->execute(array(
		        'id_post' => $id_post
		    ));

			$nbrcomments = $selectcomments->rowCount();

		    return $nbrcomments;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour un récupéré nbr de commentaires d'un user
	  |-------------------------------------------------
	*/
	public function get_nbr_comments_user($user) {

		try {
			$selectnbrcomment = $this->db->prepare("SELECT auteur FROM comments");
			$selectnbrcomment->execute();

			$nbrcomment = 0;
			while($comments = $selectnbrcomment->fetch()) {
				$auteurcomment = $comments['auteur'];

	    		if($auteurcomment == $user) {
			    	$nbrcomment++;
			    }
			}
            return $nbrcomment;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}
}
?>