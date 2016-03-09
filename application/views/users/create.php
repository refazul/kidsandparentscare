<div style="width:450px;clear:both;margin:10px auto 20px;">

    <?php

    $form = array(
        'id' => 'user_create_form',
        'value_height' => 28,
        'action' => site_url().'users/ajax',
    );

    $fields = array(
        'user_name' => array(
            'id' => 'user_name',
            'type' => 'text',
            'value' => '',
            'title' => 'User',
        ),
        'user_pass' => array(
            'id' => 'user_pass',
            'type' => 'password',
            'value' => '',
            'title' => 'Password',
        ),
        'user_fullname' => array(
            'id' => 'user_fullname',
            'type' => 'text',
            'value' => '',
            'title' => 'Full Name',
        ),
        'user_contact' => array(
            'id' => 'user_contact',
            'type' => 'text',
            'value' => '',
            'title' => 'Contact',
        ),
        'user_email' => array(
            'id' => 'user_email',
            'type' => 'text',
            'value' => '',
            'title' => 'Email',
        ),
        'user_address' => array(
            'id' => 'user_address',
            'type' => 'text',
            'value' => '',
            'title' => 'Address',
        ),
    );

    ?>

    <form action="<?php echo $form['action'];?>" method="POST" id="<?php echo $form['id'];?>">

        <?php foreach ($fields as $key => $field):?>

            <?php if ($field['type'] == 'text'): ?>
            <div class="part">
                <div class="field"><?php echo $field['title'];?></div>
                <div class="seperator"></div>
                <div class="value" style="height:<?php echo $form['value_height'];?>px;">
                    <input type="text" id="<?php echo $field['id'];?>" name="<?php echo $field['id'];?>" autocomplete="off" class="form-control" value="<?php echo $field['value'] ? $field['value'] : '';?>"/>
                    <div class="mini-status-after" id="msgholder-<?php echo $field['id'];?>"></div>
                </div>
                <div class="end"></div>
            </div>

            <?php elseif ($field['type'] == 'password'): ?>
            <div class="part">
                <div class="field"><?php echo $field['title'];?></div>
                <div class="seperator"></div>
                <div class="value" style="height:<?php echo $form['value_height'];?>px;">
                    <input type="password" id="<?php echo $field['id'];?>" name="<?php echo $field['id'];?>" autocomplete="off" class="form-control" value="<?php echo $field['value'] ? $field['value'] : '';?>"/>
                    <div class="mini-status-after" id="msgholder-<?php echo $field['id'];?>"></div>
                </div>
                <div class="end"></div>
            </div>

            <?php elseif ($field['type'] == 'select'): ?>
            <div class="part">
                <div class="field"><?php echo $field['title'];?></div>
                <div class="seperator"></div>
                <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
                    <div class="select-wrap">
                        <select name="<?php echo $field['id'];?>">
                            <?php foreach ($field['values'] as $value => $key):?>
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

        <?php foreach ($fields as $field):?>
            <?php if ($field['type'] == 'hidden'): ?>
            <input type='hidden' id='<?php echo $field['id'];?>' name='<?php echo $field['id'];?>' value='<?php echo $field['value'] ? $field['value'] : '';?>'/>
            <?php endif; ?>
        <?php endforeach;?>

        <input type='hidden' name='intent' value='create'/>

        <button style="float:right;margin-top:10px;" id="user_create" class="btn btn-default">Continue</button>

        <script type="text/javascript">
            $(document).ready(function()
            {
                $('#<?php echo $form['id'];?>').ajaxForm({

                    /* set data type json */
                    dataType:  'json',

                    /* reset before submitting */
                    beforeSend: function() {
                    },

                    beforeSubmit: function(formData) {
                        formData[1].value = CryptoJS.MD5(formData[1].value).toString();
                        //console.log(formData);
                    },

                    /* progress bar call back*/
                    uploadProgress: function(event, position, total, percentComplete) {
                    },

                    /* complete call back */
                    complete: function(data) {
                        console.log(data);
                        if(data.responseJSON.status=='ok')
                            window.location='<?php echo site_url();?>users/edit/'+data.responseJSON.uid;
                        else
                        {
                            $('.mini-status-after').each(function(){
                                $(this).slideUp();
                            });
                            if(data.responseJSON.status=='already_exists')
                            {
                                $('#msgholder-user_name').parent().css('height','auto');
                                $('#msgholder-user_name').html('').html('User Already Exists!').slideDown();
                            }
                            else if(data.responseJSON.status=='no_user')
                            {
                                $('#msgholder-user_name').parent().css('height','auto');
                                $('#msgholder-user_name').html('').html('Please Enter a Username First!').slideDown();
                            }
                            else if(data.responseJSON.status=='no_pass')
                            {
                                $('#msgholder-user_pass').parent().css('height','auto');
                                $('#msgholder-user_pass').html('').html('Please Enter a Password!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_role')
                            {
                                $('#msgholder-user_role').parent().css('height','auto');
                                $('#msgholder-user_role').html('').html('Invalid Role!').slideDown();
                            }
                        }
                    }
                });
            });
        </script>
        <div style="clear:both;"></div>
    </form>
</div>
