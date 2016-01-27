<?php
/*|--------------------------------------------------
  | TEMPLATE: Ajout post bouton
  |--------------------------------------------------
  */
?>

<?php if(connect()) { ?>
  <nav id="tchatmenu" class="navmenu navmenu-default navmenu-fixed-right offcanvas" role="navigation" style="width: 350px;">
    <div id="tchat" style="max-height: 80%;overflow-y: auto;">
    </div>
    <div class="row" style="width: 100%;vertical-align:bottom;">
    <hr>
      <div class="col-md-1"></div>
      <div class="col-xs-12 col-md-10">
      <form method="post" id="chatForm">
        <input type="text" id="messageinput" class="form-control" placeholder="Votre message" autocomplete="off">
        <br />  
        <button type="submit" class="btn btn-success btn-block" id="submittchat">Envoyer</button>
      </form>
      </div>
    </div>
  </nav>
  <a href="" class="cd-top btn btn-danger btn-fab btn-raised mdi-editor-mode-edit" data-toggle="modal" data-target="#addPostModal" title="Ajouter un post" style="color: #fff!important;transition: all 0.4s ease;"></a>
  <div class="modal fade" id="addPostModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h3 class="modal-title">Publier un post</h3>
          </div>
          <form method="post" class="form-horizontal" id="form-addpost" accept-charset="UTF-8" enctype="multipart/form-data">
            <div class="modal-body">
              <fieldset>
                  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                      <textarea class="form-control input-lg" rows="5" id="inputPost" name="post" placeholder="Votre publication" style="max-width: 100%;" required></textarea>
                      <div class="row">
                          <div class="col-xs-12 col-sm-5">
                              <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <span class="input-group-addon btn btn-flat btn-file" style="text-transform: none;padding: 3px 8px;border-radius: 3px;">
                                    <span class="fileinput-new" style="font-weight: 500;"><i class="fa fa-camera"></i> Ajouter une photo</span>
                                    <span class="fileinput-exists" style="font-weight: 500;">Modifier</span>
                                    <input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
                                    <input type="file" name="fichierjoint" id="fichierjoint" accept="image/*"/>
                                </span>
                                <a href="#" class="input-group-addon fileinput-exists no_underline" data-dismiss="fileinput">Supprimer</a>
                                <div class="form-control" data-trigger="fileinput" style="margin-left: 5px;border: 0;background-image: none;">
                                </div>
                              </div>
                          </div>
                          <div class="col-xs-12 col-sm-7">
                              <span class="help-block" id="errorpost" style="text-align: right;color: red;font-weight: 500;"></span>
                          </div>
                      </div>
                      <br />
                      <div class="form-group">
                          <div class="radio radio-primary" style="display: inline-block;">
                              <label>
                                <input type="radio" class="inputConf" name="conf" value="friends" checked>
                                Classe / Amis
                              </label>
                          </div>
                          <div class="radio radio-success" style="margin-left: 20px;display: inline-block;">
                              <label>
                                <input type="radio" class="inputConf" name="conf" value="public">
                                Public
                              </label>
                          </div>
                      </div>
                  </div>
              </fieldset>
              <div class="collapse" id="collapseEmoji">
                  <?php require $_SERVER['DOCUMENT_ROOT'].'/app/view/emojilist.template.php'; ?>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-flat" id="btnlistemoji" data-toggle="collapse" data-target="#collapseEmoji" aria-expanded="false" aria-controls="collapseEmoji" style="float: left;"><i></i> Liste Emoticons</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-material-blue" name="submitaddpost" id="submitaddpost">Publier</button>
            </div>
          </form>
        </div>
      </div>
  </div>
<?php } ?>