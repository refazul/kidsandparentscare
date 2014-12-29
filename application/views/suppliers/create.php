<div style="width:303px;clear:both;margin:10px auto 20px;">
    
    <?php
    
    $form=array(
        'id'=>'supplier_create_form',
        'value_width'=>178,
        'value_height'=>28,
        'action'=>site_url().'suppliers/ajax'
    );
    
    $fields=array(
        'supplier_name'=>array(
            'id'=>'supplier_name',
            'type'=>'text',
            'value'=>'',
            'title'=>'Name'
        ),
        'supplier_active'=>array(
            'id'=>'supplier_active',
            'type'=>'select',
            'values'=>array(
                '1'=>'Yes',
                '0'=>'No'
            ),
            'title'=>'Active'
        ),
        'supplier_address'=>array(
            'id'=>'supplier_address',
            'type'=>'text',
            'value'=>'',
            'title'=>'Address'
        ),
        'supplier_city'=>array(
            'id'=>'supplier_city',
            'type'=>'text',
            'value'=>'',
            'title'=>'City'
        ),
        'supplier_country'=>array(
            'id'=>'supplier_country',
            'type'=>'text',
            'value'=>'BD',
            'title'=>'Country'
        ),
        'supplier_phone'=>array(
            'id'=>'supplier_phone',
            'type'=>'text',
            'value'=>'',
            'title'=>'Phone'
        ),
        'supplier_cell'=>array(
            'id'=>'supplier_cell',
            'type'=>'text',
            'value'=>'',
            'title'=>'Cell'
        ),
        'supplier_email'=>array(
            'id'=>'supplier_email',
            'type'=>'text',
            'value'=>'',
            'title'=>'Email'
        ),
        'supplier_fax'=>array(
            'id'=>'supplier_fax',
            'type'=>'text',
            'value'=>'',
            'title'=>'Fax'
        ),
        'supplier_description'=>array(
            'id'=>'supplier_description',
            'type'=>'text',
            'value'=>'',
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
                            <option value="<?php echo $value;?>"><?php echo $key;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
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

        <button style="float:right;margin-top:10px;" id="supplier_create" class="btn btn-default">Continue</button>

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
                            window.location='<?php echo site_url();?>suppliers/edit/'+data.responseJSON.sid;
                        else
                        {
                            $('.mini-status-after').each(function(){
                                $(this).slideUp();
                            });
                            if(data.responseJSON.status=='already_exists')
                            {                            
                                $('#msgholder-supplier_name').parent().css('height','auto');
                                $('#msgholder-supplier_name').html('').html('Supplier Already Exists!').slideDown();
                            }                            
                            else if(data.responseJSON.status=='no_name')
                            {
                                $('#msgholder-supplier_name').parent().css('height','auto');
                                $('#msgholder-supplier_name').html('').html('Please Give it a Name!').slideDown();
                            }
                        }                        
                    }
                });
            });
        </script>
        <div style="clear:both;"></div>
    </form>
</div>