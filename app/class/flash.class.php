<?php
/*|-------------------------------------------------
  | Class pour généré un message après envoie 
  | d'un formulaire etc..
  | Paramètre: Message et type (danger, success)
  |-------------------------------------------------
*/
function flash(){
    if(isset($_SESSION['Flash'])){
        $message = $_SESSION['Flash'];
        $type = $_SESSION['Flash_Type'];
        echo "<div class='alert alert-$type'><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>$message</div>";
        unset($_SESSION['Flash_Type']);
        unset($_SESSION['Flash']);
    }
}

function setFlash($message, $type = 'success') {
    $_SESSION['Flash'] = $message;
    $_SESSION['Flash_Type'] = $type;
}

function errorFlash(){
    if(isset($_SESSION['ErrorFlash'])){
        $message = $_SESSION['ErrorFlash'];
        unset($_SESSION['ErrorFlash']);
        return $message;
    }
}

function setErrorFlash($message) {
    $_SESSION['ErrorFlash'] = $message;
}
?>