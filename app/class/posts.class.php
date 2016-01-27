<?php
/*|-------------------------------------------------
  | Class pour gérer les posts des utilisateurs
  |-------------------------------------------------
*/
class POST{

    private $db;
 
    function __construct($DB_con)
    {
      $this->db = $DB_con;
    }

    /*|-------------------------------------------------
	  | Fonction pour ajouter un post
	  |-------------------------------------------------
	*/
	public function addpost($username, $post, $public, $img = '') {

		try {
			$date = time();

			$addpost = $this->db->prepare("INSERT INTO posts(auteur,content,date_post,public,img_post) VALUES(:auteur, :content, :date_post, :public, :img_post)");   
	        $addpost->execute(array(
	            'auteur' => $username,
	            'content' => $post,
	            'date_post' => $date,
	            'public' => $public,
	            'img_post' => $img
	        ));
	        return true;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour supprimer un post
	  |-------------------------------------------------
	*/
	public function supprpost($post_id, $username) {

		try {
			$supprpost = $this->db->prepare("DELETE FROM posts WHERE id=:id AND auteur=:auteur");   
	        $supprpost->execute(array(
	        	'id' => $post_id,
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
	  | Fonction pour récupéré données profil utilisateur
	  |-------------------------------------------------
	*/
	public function get_all_posts() {

		try {
			$requeteposts = $this->db->prepare("SELECT * FROM posts WHERE public=:public ORDER BY id DESC");
			$requeteposts->execute(array(
		        'public' => "1"
		    ));

			while($selectedPost = $requeteposts->fetch()) {
				$post_id = $selectedPost['id'];
				$post_user_name = $selectedPost['auteur'];
				$post_message = $selectedPost['content'];
				/* strftime('%d %B %Y à %H:%M'); */
				$date = $selectedPost['date_post'];
				$date = strftime('%d %B %Y à %H:%M', $date);
				$post_like = $selectedPost['likes'];
				$post_comments = $selectedPost['comments'];
				$hrefimgprofil = $img->get_img_profil($post_user_name);

				include $_SERVER['DOCUMENT_ROOT'].'/app/view/post.template.php';
			}
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}

	/*|-------------------------------------------------
	  | Fonction pour avoir nbr de post
	  |-------------------------------------------------
	*/
	public function get_post_from_user($username) {

		try {
			$selectpost = $this->db->prepare("SELECT * FROM posts WHERE auteur=:auteur ORDER BY id DESC LIMIT 1");
			$selectpost->execute(array(
				'auteur' => $username
			));

			$post = array();
			$post = $selectpost->fetch();

            return $post;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}
	
	/*|-------------------------------------------------
	  | Fonction pour avoir nbr de post
	  |-------------------------------------------------
	*/
	public function get_nbr_post_from_user($username) {

		try {
			$selectnbrpost = $this->db->prepare("SELECT auteur FROM posts");
			$selectnbrpost->execute();

			$nbrpost = 0;
			while($posts = $selectnbrpost->fetch()) {
				$auteurpost = $posts['auteur'];

	    		if($auteurpost == $username) {
			    	$nbrpost++;
			    }
			}
            return $nbrpost;
	    }
        catch(PDOException $e)
        {
           echo $e->getMessage();
        }
	}
}
?>