<div style="width:303px;clear:both;margin:10px auto 20px;">
    
    <?php
    
    $form=array(
        'id'=>'manufacturer_edit_form',
        'value_width'=>178,
        'value_height'=>28,
        'action'=>site_url().'manufacturers/ajax'
    );
    
    $fields=array(
        'manufacturer_name'=>array(
            'id'=>'manufacturer_name',
            'type'=>'text',
            'value'=>$name,
            'title'=>'Name'
        ),
        'manufacturer_active'=>array(
            'id'=>'manufacturer_active',
            'type'=>'select',
            'values'=>array(
                '1'=>'Yes',
                '0'=>'No'
            ),
            'selected'=>$active,
            'title'=>'Active'
        ),
        'manufacturer_description'=>array(
            'id'=>'manufacturer_description',
            'type'=>'text',
            'value'=>$description,
            'title'=>'Description'
        ),
    );
    
    ?>

    <form action="<?php echo $form['action'];?>" method="POST" id="<?php echo $form['id'];?>">
    
        <?php foreach($fields as $key=>$field):?>

            <?php if($field['type']=='text'): ?>
            <div class="part">
                <div class="field"><?php echo $field['title'];?></div>
                <div class="seperator"></div>
                <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
                    <input type="text" id="<?php echo $field['id'];?>" name="<?php echo $field['id'];?>" autocomplete="off" class="form-control" value="<?php echo $field['value']?$field['value']:'';?>"/>
                    <div class="mini-status-after" id="msgholder-<?php echo $field['id'];?>"></div>
                </div>
                <div class="end"></div>
            </div>            
        
            <?php elseif($field['type']=='password'): ?>
            <div class="part">
                <div class="field"><?php echo $field['title'];?></div>
                <div class="seperator"></div>
                <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
                    <input type="password" id="<?php echo $field['id'];?>" name="<?php echo $field['id'];?>" autocomplete="off" class="form-control" value="<?php echo $field['value']?$field['value']:'';?>"/>
                    <div class="mini-status-after" id="msgholder-<?php echo $field['id'];?>"></div>
                </div>
                <div class="end"></div>
            </div>
        
            <?php elseif($field['type']=='select'): ?>
            <div class="part">
                <div class="field"><?php echo $field['title'];?></div>
                <div class="seperator"></div>
                <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
                    <div class="select-wrap" style="height:100%;">
                        <select name="<?php echo $field['id'];?>" style="height:100%;">
                            <?php foreach($field['values'] as $value=>$key):?>
                            <option <?php if($value==$field['selected'])echo 'selected="selected"'?> value="<?php echo $value;?>"><?php echo $key;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="mini-status-after" id="msgholder-<?php echo $field['id'];?>"></div>
                </div>
                <div class="end"></div>
            </div>
            <?php endif; ?>

        <?php endforeach; ?>

        <?php foreach($fields as $field):?>
            <?php if($field['type']=='hidden'): ?>
            <input type='hidden' id='<?php echo $field['id'];?>' name='<?php echo $field['id'];?>' value='<?php echo $field['value']?$field['value']:'';?>'/>
            <?php endif; ?>
        <?php endforeach;?>
            
        <input type='hidden' name='intent' value='edit'/>

        <button style="float:right;margin-top:10px;" id="manufacturer_edit" class="btn btn-default">Continue</button>

        <script type="text/javascript">            
            $(document).ready(function()
            {            
                $('#<?php echo $form['id'];?>').ajaxForm({

                    /* set data type json */
                    dataType:  'json',

                    /* reset before submitting */
                    beforeSend: function() {                                                                                
                    },

                    /* progress bar call back*/
                    uploadProgress: function(event, position, total, percentComplete) {                                        
                    },

                    /* complete call back */
                    complete: function(data) {
                        console.log(data);
                        
                        if(data.responseJSON.status=='ok')
                            window.location=window.location
                        else
                        {
                            $('.mini-status-after').each(function(){
                                $(this).slideUp();
                            });
                            if(data.responseJSON.status=='already_exists')
                            {                            
                                $('#msgholder-manufacturer_name').parent().css('height','auto');
                                $('#msgholder-manufacturer_name').html('').html('Manufacturer Already Exists!').slideDown();
                            }                            
                            else if(data.responseJSON.status=='invalid_value')
                            {
                                $('#msgholder-manufacturer_active').parent().css('height','auto');
                                $('#msgholder-manufacturer_active').html('').html('Invalid Value!').slideDown();
                            }
                        }                        
                    }
                });
            });
        </script>
        <div style="clear:both;"></div>
    </form>
</div>