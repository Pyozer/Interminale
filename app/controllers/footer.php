<?php
//****************************************************
// Controller: Footer
//****************************************************

$OLDfooter_li = '
<li><a href="/a-propos-de-nous">A propos de nous</a></li>
<li><a href="/conditions-utilisation">Conditions d\'utilisation</a></li>
<li><a href="/mentions-legales">Mentions légales</a></li>
<li><a href="/contact">Contact</a></li>';

$footer_li = '';

$OLDcopyright = 'Copyright © 2016. Tous droits réservés.';
$copyright = 'Copyright © 2016. Développé avec <i class="fa fa-heart" title="Amour"></i>';

/* SI CONNECTE */
require $_SERVER['DOCUMENT_ROOT'].'/app/controllers/addpost.php';
/* ################################### */

require $_SERVER['DOCUMENT_ROOT'].'/app/view/footer.template.php';
?>