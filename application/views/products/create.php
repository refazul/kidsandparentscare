<div style="width:303px;clear:both;margin:10px auto 20px;">

    <?php

    $upload_form =  uniqid().'_'.time();

    $this->load->vars(
                    array(
                        'form_id'=>$upload_form,
                        '_scope'=>'products',
                        '_name'=>uniqid().'_'.time(),
                        'destination_form_id'=>'product_create_form',
                        'destination_hook_id'=>'product_image',
                        'DEFAULT_IMG'=>asset_url().'images/alt.png',
                        'IMG'=>NULL
                    ));
    $this->load->view('general/upload');
    ?>

</div>


<div style="width:303px;clear:both;margin:10px auto 20px;">
    
    <?php
    
    $form=array(
        'id'=>'product_create_form',
        'value_width'=>178,
        'value_height'=>28,
        'action'=>site_url().'products/ajax'
    );
    
    $fields=array(
        'product_barcode'=>array(
            'id'=>'product_barcode',
            'type'=>'text',
            'value'=>substr(round(microtime(true) * 1000),0,12),
            'title'=>'Barcode'
        ),
        'product_name'=>array(
            'id'=>'product_name',
            'type'=>'text',
            'value'=>'',
            'title'=>'Name'
        ),
        'product_sku'=>array(
            'id'=>'product_sku',
            'type'=>'text',
            'value'=>'',
            'title'=>'SKU'
        ),
        'product_unit'=>array(
            'id'=>'product_unit',
            'type'=>'text',
            'value'=>'pc',
            'title'=>'Unit'
        ),        
        'product_department'=>array(
            'id'=>'product_department',
            'type'=>'select',
            'values'=>$departments,
            'title'=>'Department'
        ),
        'product_category'=>array(
            'id'=>'product_category',
            'type'=>'select',
            'values'=>$categories,
            'title'=>'Category'
        ),
        'product_image'=>array(
            'id'=>'product_image',
            'type'=>'hidden',
            'value'=>''
        )
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
                    <div class="select-wrap">
                        <select name="<?php echo $field['id'];?>">
                            <?php foreach($field['values'] as $value=>$key):?>
                            <option value="<?php echo $value;?>"><?php echo $key;?></option>
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
            
        <input type='hidden' name='intent' value='create'/>

        <button style="float:right;margin-top:10px;" id="product_create" class="btn btn-default">Continue</button>

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
                        //console.log(data);
                        
                        if(data.responseJSON.status=='ok')
                            window.location='<?php echo site_url();?>products/edit/'+data.responseJSON.pid;
                        else
                        {
                            $('.mini-status-after').each(function(){
                                $(this).slideUp();
                            });
                            if(data.responseJSON.status=='barcode_already_exists')
                            {                            
                                $('#msgholder-product_barcode').parent().css('height','auto');
                                $('#msgholder-product_barcode').html('').html('Barcode Already Exists!').slideDown();
                            }
                            else if(data.responseJSON.status=='sku_already_exists')
                            {
                                $('#msgholder-product_sku').parent().css('height','auto');
                                $('#msgholder-product_sku').html('').html('SKU Already Exists!').slideDown();
                            }
                            else if(data.responseJSON.status=='no_barcode')
                            {
                                $('#msgholder-product_barcode').parent().css('height','auto');
                                $('#msgholder-product_barcode').html('').html('Please Enter a Barcode First!').slideDown();
                            }
                            else if(data.responseJSON.status=='no_sku')
                            {
                                $('#msgholder-product_sku').parent().css('height','auto');
                                $('#msgholder-product_sku').html('').html('Please Enter SKU!').slideDown();
                            }
                            else if(data.responseJSON.status=='no_name')
                            {
                                $('#msgholder-product_name').parent().css('height','auto');
                                $('#msgholder-product_name').html('').html('Please Give it a Name!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_department')
                            {
                                $('#msgholder-product_department').parent().css('height','auto');
                                $('#msgholder-product_department').html('').html('Invalid Department!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_category')
                            {
                                $('#msgholder-product_category').parent().css('height','auto');
                                $('#msgholder-product_category').html('').html('Invalid Category!').slideDown();
                            }
                        }                        
                    }
                });
            });
        </script>
        <div style="clear:both;"></div>
    </form>
</div>