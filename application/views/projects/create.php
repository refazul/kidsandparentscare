<div style="width:303px;clear:both;margin:10px auto 20px;">

    <?php

    $form=array(
        'id'=>'project_create_form',
        'value_width'=>178,
        'value_height'=>28,
        'action'=>site_url().'projects/ajax'
    );

    $fields=array(
        'project_name'=>array(
            'id'=>'project_name',
            'type'=>'text',
            'value'=>'',
            'title'=>'Name'
        ),
        'project_buyer'=>array(
            'id'=>'buyer_id',
            'type'=>'select',
            'values'=>$buyers,
            'title'=>'Buyer'
        ),
        'project_suppliers'=>array(
            'id'=>'supplier_id',
            'type'=>'select',
            'values'=>$suppliers,
            'title'=>'Supplier'
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

        <button style="float:right;margin-top:10px;" id="project_create" class="btn btn-default">Continue</button>

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
                            window.location='<?php echo site_url();?>projects/edit/'+data.responseJSON.project_id;
                        else
                        {
                            $('.mini-status-after').each(function(){
                                $(this).slideUp();
                            });
                            if(data.responseJSON.status=='no_name')
                            {
                                $('#msgholder-project_name').parent().css('height','auto');
                                $('#msgholder-project_name').html('').html('Please give a name!').slideDown();
                            }
                            else if(data.responseJSON.status=='no_code')
                            {
                                $('#msgholder-project_code').parent().css('height','auto');
                                $('#msgholder-project_code').html('').html('Please give a code!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_code')
                            {
                                $('#msgholder-project_code').parent().css('height','auto');
                                $('#msgholder-project_code').html('').html('Invalid code!').slideDown();
                            }
                            else if(data.responseJSON.status=='already_exists')
                            {
                                $('#msgholder-project_code').parent().css('height','auto');
                                $('#msgholder-project_code').html('').html('project with this code already exists!').slideDown();
                            }
                            else if(data.responseJSON.status=='unknown_error')
                            {
                                $('#msgholder-project_code').parent().css('height','auto');
                                $('#msgholder-project_code').html('').html('An unknown error occured! Please contact technical support').slideDown();
                            }
                        }
                    }
                });
            });
        </script>
        <div style="clear:both;"></div>
    </form>
</div>