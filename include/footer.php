        </div>
		<!-- === Derniers CSS à charger -->
        <link href="/assets/lightbox/css/lightbox.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/assets/css/bootstrap-material-datetimepicker.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.9.1/bootstrap-table.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojify.js/0.9.5/emojify.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojify.js/0.9.5/emojify-emoticons.min.css" />
		<!-- === Fonts === -->
		<link href="https://fonts.googleapis.com/css?family=RobotoDraft:regular,bold,italic,thin,light,bolditalic,black,medium" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic" rel="stylesheet" type="text/css">
        <!-- === Son === -->
		<audio preload="auto" id="soundtoplay">
		  <source src="/assets/sound/pop-chat.mp3" type="audio/mp3">
		</audio>
        <!-- === Fichiers JS a charger === -->
        <script src="/assets/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
        <script src="/assets/js/ripples.min.js"></script>
        <script src="/assets/js/material.min.js"></script>
        <script src="/assets/js/ohsnap.js"></script>
        <script src="/assets/js/typeahead.bundle.min.js"></script>
        <script src="/assets/lightbox/js/lightbox.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.9.1/bootstrap-table.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.9.1/locale/bootstrap-table-fr-FR.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/emojify.js/0.9.5/emojify.min.js"></script>
        <!-- Calendrier -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/locale/fr.js"></script>
        <script src="/assets/js/bootstrap-material-datetimepicker.js"></script>
        <script src="/assets/js/jquery.timeago.min.js" type="text/javascript"></script>
        <!-- Interminale JS -->
        <script type="text/javascript" src="/assets/js/custom.min.js"></script>
        <script src="https://cdn.socket.io/socket.io-1.3.7.js"></script>
        <script src="/app/nodejs/client.js"></script>
        <!-- Execution des scripts JS -->
        <script type="text/javascript">
    	$(document).ready(function() {
            $.material.init();
            $('#date_event').bootstrapMaterialDatePicker({ format : 'DD/MM/YYYY', lang : 'fr', weekStart : 1, time : false, cancelText : 'ANNULER', minDate : new Date() });
            $('[data-toggle="tooltip"]').tooltip({
                title: getuserliked,
                html: true,
                container: 'body',
                trigger: "hover"
            });

            <?php if(connect()) { ?>
                valid_user_connected("<?php echo $_SESSION['userid']; ?>");
                get_all_msg();
                setTimeout(function() {
                    gotobottom('tchat');
                }, 350);
                setTimeout(function() {
                    update_posts();
                }, 15000);
                <?php
                $requesturi = explode('/', $_SERVER['REQUEST_URI']);
                if($requesturi[1] == "messages" && !empty($requesturi[2])) { ?>
                    setTimeout(function() {
                        gotobottom('messages-prives');
                    }, 350);
                <?php }
                if(isset($id_user) && $id_user != $_SESSION['userid']) { ?>
                    setTimeout(function() {
                        verif_user_connected('<?php echo $id_user; ?>');
                    }, 20000);
                <?php }
                if(isset($connectauto)) {
                    echo 'ohSnap("Votre session a été rétablie", "green");';
                }
                ?>
                update_msg_friend();
                check_alluser_connected("<?php echo $_SESSION['userid']; ?>");
                emojify.setConfig({
                    emojify_tag_type : 'span',
                    img_dir          : '/assets/img/emoji',
                    mode             : 'sprite',
                    ignored_tags     : {
                        'SCRIPT'  : 1,
                        'TEXTAREA': 1,
                        'PRE'     : 1,
                        'CODE'    : 1
                    }
                });
                emojify.run();
            <?php } ?>
        });
        jQuery(document).ready(function() {
            $("time.timeago").timeago();
        });
		</script>
    </body>
</html>