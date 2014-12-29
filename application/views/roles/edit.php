<div style="width:303px;clear:both;margin:10px auto 20px;">
    
    <?php
    
    $form=array(
        'id'=>'role_edit_form',
        'value_width'=>178,
        'value_height'=>28,
        'action'=>site_url().'roles/ajax'
    );
    
    $fields=array(
        'role_name'=>array(
            'id'=>'role_name',
            'type'=>'text',
            'value'=>$role,
            'title'=>'Name'
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
            
        <div class="part">
            <div class="field">Product</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="product[]" value='c' <?php if(in_array('4',$priv))echo 'checked="checked"';?> id="product_create"/><label class='mini-label' for="product_create">Create</label>
                <input type="checkbox" name="product[]" value='e' <?php if(in_array('5',$priv))echo 'checked="checked"';?> id="product_edit"/><label class='mini-label' for="product_edit">Edit</label>
                <input type="checkbox" name="product[]" value='r' <?php if(in_array('6',$priv))echo 'checked="checked"';?> id="product_remove"/><label class='mini-label' for='product_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Category</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="category[]" value='c' <?php if(in_array('22',$priv))echo 'checked="checked"';?> id="category_create"/><label class='mini-label' for="category_create">Create</label>
                <input type="checkbox" name="category[]" value='e' <?php if(in_array('23',$priv))echo 'checked="checked"';?> id="category_edit"/><label class='mini-label' for="category_edit">Edit</label>
                <input type="checkbox" name="category[]" value='r' <?php if(in_array('24',$priv))echo 'checked="checked"';?> id="category_remove"/><label class='mini-label' for='category_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Department</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="department[]" value='c' <?php if(in_array('7',$priv))echo 'checked="checked"';?> id="department_create"/><label class='mini-label' for="department_create">Create</label>
                <input type="checkbox" name="department[]" value='e' <?php if(in_array('8',$priv))echo 'checked="checked"';?> id="department_edit"/><label class='mini-label' for="department_edit">Edit</label>
                <input type="checkbox" name="department[]" value='r' <?php if(in_array('9',$priv))echo 'checked="checked"';?> id="department_remove"/><label class='mini-label' for='department_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Supplier</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="supplier[]" value='c' <?php if(in_array('13',$priv))echo 'checked="checked"';?> id="supplier_create"/><label class='mini-label' for="supplier_create">Create</label>
                <input type="checkbox" name="supplier[]" value='e' <?php if(in_array('14',$priv))echo 'checked="checked"';?> id="supplier_edit"/><label class='mini-label' for="supplier_edit">Edit</label>
                <input type="checkbox" name="supplier[]" value='r' <?php if(in_array('15',$priv))echo 'checked="checked"';?> id="supplier_remove"/><label class='mini-label' for='supplier_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Manufacturer</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="manufacturer[]" value='c' <?php if(in_array('16',$priv))echo 'checked="checked"';?> id="manufacturer_create"/><label class='mini-label' for="manufacturer_create">Create</label>
                <input type="checkbox" name="manufacturer[]" value='e' <?php if(in_array('17',$priv))echo 'checked="checked"';?> id="manufacturer_edit"/><label class='mini-label' for="manufacturer_edit">Edit</label>
                <input type="checkbox" name="manufacturer[]" value='r' <?php if(in_array('18',$priv))echo 'checked="checked"';?> id="manufacturer_remove"/><label class='mini-label' for='manufacturer_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Customer</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="customer[]" value='c' <?php if(in_array('25',$priv))echo 'checked="checked"';?> id="customer_create"/><label class='mini-label' for="customer_create">Create</label>
                <input type="checkbox" name="customer[]" value='e' <?php if(in_array('26',$priv))echo 'checked="checked"';?> id="customer_edit"/><label class='mini-label' for="customer_edit">Edit</label>
                <input type="checkbox" name="customer[]" value='r' <?php if(in_array('27',$priv))echo 'checked="checked"';?> id="customer_remove"/><label class='mini-label' for='customer_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Stock</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="stock[]" value='c' <?php if(in_array('10',$priv))echo 'checked="checked"';?> id="stock_create"/><label class='mini-label' for="stock_create">Create</label>
                <input type="checkbox" name="stock[]" value='e' <?php if(in_array('11',$priv))echo 'checked="checked"';?> id="stock_edit"/><label class='mini-label' for="stock_edit">Edit</label>
                <input type="checkbox" name="stock[]" value='r' <?php if(in_array('12',$priv))echo 'checked="checked"';?> id="stock_remove"/><label class='mini-label' for='stock_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
            
        <div class="part">
            <div class="field">Invoice</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="invoice[]" value='c' <?php if(in_array('19',$priv))echo 'checked="checked"';?> id="invoice_create"/><label class='mini-label' for="invoice_create">Create</label>
                <input type="checkbox" name="invoice[]" value='e' <?php if(in_array('20',$priv))echo 'checked="checked"';?> id="invoice_edit"/><label class='mini-label' for="invoice_edit">Edit</label>
                <input type="checkbox" name="invoice[]" value='r' <?php if(in_array('21',$priv))echo 'checked="checked"';?> id="invoice_remove"/><label class='mini-label' for='invoice_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
            
        <div class="part">
            <div class="field">User</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="user[]" value='c' <?php if(in_array('1',$priv))echo 'checked="checked"';?> id="user_create"/><label class='mini-label' for="user_create">Create</label>
                <input type="checkbox" name="user[]" value='e' <?php if(in_array('2',$priv))echo 'checked="checked"';?> id="user_edit"/><label class='mini-label' for="user_edit">Edit</label>
                <input type="checkbox" name="user[]" value='r' <?php if(in_array('3',$priv))echo 'checked="checked"';?> id="user_remove"/><label class='mini-label' for='user_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
            
        <script type='text/javascript'>
            $(document).ready(function(){
                
            });
        </script>
            
        <input type='hidden' name='intent' value='edit'/>

        <button style="float:right;margin-top:10px;" id="role_edit" class="btn btn-default">Continue</button>

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
                            ;//window.location=window.location
                        else
                        {
                            $('.mini-status-after').each(function(){
                                $(this).slideUp();
                            });
                            if(data.responseJSON.status=='already_exists')
                            {                            
                                $('#msgholder-role_name').parent().css('height','auto');
                                $('#msgholder-role_name').html('').html('Role Already Exists!').slideDown();
                            }                            
                            else if(data.responseJSON.status=='invalid_value')
                            {
                                $('#msgholder-role_active').parent().css('height','auto');
                                $('#msgholder-role_active').html('').html('Invalid Value!').slideDown();
                            }
                        }                        
                    }
                });
            });
        </script>
        <div style="clear:both;"></div>
    </form>
</div>