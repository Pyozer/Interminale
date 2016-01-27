<div class="msgme" style="padding: 5px;">
  <div class="media">
    <div class="media-left media-middle">
      <a href="/profil/<?php echo $pseudo_me; ?>" title="<?php echo $name_me; ?>">
        <img class="img-circle media-object" src="<?php echo $imgprofil_me; ?>" alt="<?php echo $name_me; ?>" style="width: 50px;height: 50px;">
      </a>
    </div>
    <div class="media-body">
      <div class="wellcustom msg" id="<?php echo $id_msg; ?>" style="margin-bottom: 0;padding: 13px 15px;">
          <p style="margin: 0;font-size: 17px;width: 100%;"><?php echo $msg; ?></p>
          <p style="text-align: right;margin: 5px 0 0 0;">
            <small style="color: #555;"><?php echo $date; ?></small>
          </p>
        </div>
    </div>
  </div>
</div>