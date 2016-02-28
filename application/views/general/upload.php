<?php

/*
 *  array(
        'form_id'=>uniqid().'_'.time(),                     //just give an arbitrary id
        '_scope'=>'users',                                  //upload folder
        '_name'=>uniqid().'_'.time(),                       //this is totally arbitrary
        'destination_form_id'=>'',                          //destination form id where the hook exists
        'destination_hook_id'=>'',                          //hook id which is typically hidden input
        'DEFAULT_IMG'=>asset_url().'images/alt.png',        //default image
        'IMG'=>$product['image']                            //preload
    ));
 */

?>
<form action="<?php echo site_url();?>general/upload" method="post" enctype="multipart/form-data" id="<?php echo $form_id;?>">
   <input type="hidden" name="_scope" value="<?php echo $_scope;?>"/>
   <input type="hidden" name="_name" value="<?php echo $_name;?>"/>

   <div class="part">
      <div class="field"><?php echo $LABEL?></div>
      <div class="seperator"></div>

      <div class='files'>
         <?php $files=array_filter(explode(';',$VALUE)); ?>
         <?php foreach($files as $file): $uniqid=uniqid(); ?>
            <div class="docs-container" id="" data-file='<?php echo $file; ?>'>
               <div class='cross-sign'></div>
               <div class="doc-icons pdf" id="file-<?php echo $uniqid;?>"></div>
               <script type="text/javascript">
                  $('#'+'file-<?php echo $uniqid;?>').click(function(){
                     var file='<?php echo site_url();?>uploads/'+$(this).parent().attr('data-file');
                     $('body').append('<iframe class="tempviewer" src="http://docs.google.com/gview?url='+file+'&embedded=true" style="margin:5%;width:90%; height:90%;position:fixed;z-index:100000;" frameborder="0"></iframe>');

                     $('.global-overlay').show();
                     $('.global-overlay').unbind('click');
                     $('.global-overlay').click(function(){
                        $(this).hide();
                        $('.tempviewer').remove();
                     });
                  });
               </script>
               <a class="doc-links" href="<?php echo site_url();?>uploads/<?php echo $file;?>" target="_blank"><?php echo $file;?></a>
            </div>
         <?php endforeach; ?>
      </div>

      <div class="image-holder" style="width:300px;float:right;">
         <div style="width:96.5%;padding:5px;background:#fff;height:125px;margin-top:10px;">
            <input type="file" id="<?php echo $_name;?>" name="<?php echo $_name;?>" style="width: 100%;height: 100%;position: relative;top: 0%;left: 0px;opacity: 0;cursor:pointer">
            <img class="preview-image" width="100%" height="100%" src="<?php if($IMG==NULL) echo $DEFAULT_IMG;else echo base_url().'uploads/'.$IMG;?>" style="position: relative; z-index: -10;top:-100%;"/>
         </div>
         <!--<input type="submit" value="Upload File to Server">-->
         <div class="progress" style="display: none;width:100%;margin-bottom:0px;border-radius: 0px;">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                <div class="percent">0%</div>
            </div>
         </div>
         <div class="status" style="display:none;font-size: 11px;text-align: center;width: 100%;margin-top:3px;">Ready</div>
      </div>

      <div class="end"></div>
   </div>
</form>

<script type="text/javascript">
   $(document).ready(function(){
      var form = '#<?php echo $form_id;?>';
      var hook = '#<?php echo $_name;?>';
      var dest_form = '#<?php echo $destination_form_id;?>';
      var dest_hook = '#<?php echo $destination_hook_id;?>';

      $('.docs-container .cross-sign').click(function(){
         var file=$(this).parent().attr('data-file');
         var existing_files=$(dest_hook).val().split(';');
         var new_files=[];
         for(var i=0;i<existing_files.length;i++){
            if(file==existing_files[i])
               continue;
            new_files.push(existing_files[i]);
         }
         $(dest_hook).val(new_files.join(';'));
         $(this).parent().remove();
      });

      default_img='<?php echo $DEFAULT_IMG;?>';
      has_image=<?php if($IMG==NULL)echo 'false';else echo 'true';?>;

      var progress = '.progress';
      var preview = '.preview-image';
      var status = '.status';
      var bar = '.progress-bar';
      var percent = '.percent';

      $(form).ajaxForm({

         /* set data type json */
         dataType:  'json',

         /* reset before submitting */
         beforeSend: function() {
            $(progress,$(form)).fadeIn();
            $(bar,$(form)).width('0%');
            $(percent,$(form)).html('0%');
         },

         /* progress bar call back*/
         uploadProgress: function(event, position, total, percentComplete) {
            var pVel = percentComplete + '%';
            $(bar,$(form)).width(pVel);
            $(percent,$(form)).html(pVel);
            $(status,$(form)).html('Uploading...Please Wait').fadeIn();
         },

         /* complete call back */
         complete: function(data) {
            console.log(data);
            has_image=true;
            $(status,$(form)).html(data.responseJSON.msg).fadeIn();
            if(data.responseJSON.status=='ok')
            {
               var _existing_files=$(dest_hook).val().split(';');
               var _new_files=[];
               for(var i=0;i<_existing_files.length;i++){
                  _new_files.push(_existing_files[i]);
               }
               _new_files.push(data.responseJSON.path);
               if(dest_form=='#')$(dest_hook).val(_new_files.join(';'));

               var container=$('<div>',{class:'docs-container'}).attr('data-file',data.responseJSON.path);
               var cross_sign=$('<div>',{class:'cross-sign'});
               var docs_icon=$('<div>',{class:'doc-icons'});
               var docs_link=$('<a>',{class:'doc-links'});

               $(cross_sign).unbind('click');
               $(cross_sign).click(function(){
                  var file=$(this).parent().attr('data-file');
                  var existing_files=$(dest_hook).val().split(';');
                  var new_files=[];
                  for(var i=0;i<existing_files.length;i++){
                     if(file==existing_files[i])
                        continue;
                     new_files.push(existing_files[i]);
                  }
                  $(dest_hook).val(new_files.join(';'));
                  $(this).parent().remove();
               });

               $(docs_icon).unbind('click');
               $(docs_icon).click(function(){
                  var file='<?php echo site_url();?>uploads/'+$(this).parent().attr('data-file');
                  $('body').append('<iframe class="tempviewer" src="http://docs.google.com/gview?url='+file+'&embedded=true" style="margin:5%;width:90%; height:90%;position:fixed;z-index:100000;" frameborder="0"></iframe>');

                  $('.global-overlay').show();
                  $('.global-overlay').unbind('click');
                  $('.global-overlay').click(function(){
                     $(this).hide();
                     $('.tempviewer').remove();
                  });
               });

               $(docs_link).attr('href',"<?php echo site_url();?>uploads/"+data.responseJSON.path).attr('target',"_blank").text(data.responseJSON.path);

               $(container).append(cross_sign);
               $(container).append(docs_icon);
               $(container).append(docs_link);

               $('#<?php echo $form_id;?> .files').append(container);

               var extension=data.responseJSON.path.split('.').pop();
               $(docs_icon).addClass(extension);
            }

            $(hook).val('');
         }
      });

      $(hook).change(function()
      {
         if (this.files && this.files[0])
         {
            var file = this.files[0];
            var name = file.name;
            var size = file.size;
            var type = file.type;
            /* validation */


            //var reader = new FileReader();
            //reader.onload = function (e) {
            //  $(preview,$(form)).attr('src', e.target.result);
            //}
            //reader.readAsDataURL(file);
            $(form).submit();
         }
      });
   });
</script>
