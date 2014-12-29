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

    <div class="image-holder" style="width:210px;margin:auto;">
        <div title="Remove" class="cross-sign product_image_unset" style="cursor:pointer;display:none;position: absolute;z-index: 100;right: 8px;top: 8px;"></div>
        <div style="width:95%;padding:5px;background:#fff;height:125px;">
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
</form>

<script type="text/javascript">
    $(document).ready(function()
    {   
        var form = '#<?php echo $form_id;?>';
        var hook = '#<?php echo $_name;?>';
        var dest_form = '#<?php echo $destination_form_id;?>';
        var dest_hook = '#<?php echo $destination_hook_id;?>';
        
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
                    $(dest_hook,$(dest_form)).val(data.responseJSON.path);                                            
                }
            }
        });

        $('.image-holder',$(form)).on('mouseenter',function(){
            if(has_image==true) $('.product_image_unset',$(form)).show();
        });
        $('.image-holder',$(form)).on('mouseleave',function(){
            $('.product_image_unset',$(form)).hide();
        });

        $('.product_image_unset',$(form)).click(function(){
            has_image=false;
            $('.product_image_unset',(form)).hide();
            $(dest_hook).val('');
            $(preview,$(form)).attr('src',default_img);
            $(hook).replaceWith($(hook).val('').clone(true));
            $(progress,$(form)).slideUp(100);
            $(status,$(form)).slideUp(100);
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


                var reader = new FileReader();            
                reader.onload = function (e) {
                    $(preview,$(form)).attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
                $(form).submit();
            }
        });
    });
</script>