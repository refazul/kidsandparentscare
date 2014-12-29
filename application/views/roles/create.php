<div style="width:303px;clear:both;margin:10px auto 20px;">
    
    <?php
    
    $form=array(
        'id'=>'role_create_form',
        'value_width'=>178,
        'value_height'=>28,
        'action'=>site_url().'roles/ajax'
    );
    
    $fields=array(
        'role_name'=>array(
            'id'=>'role_name',
            'type'=>'text',
            'value'=>'',
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
        
        <div class="part">
            <div class="field">Product</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="product[]" value='c' id="product_create"/><label class='mini-label' for="product_create">Create</label>
                <input type="checkbox" name="product[]" value='e' id="product_edit"/><label class='mini-label' for="product_edit">Edit</label>
                <input type="checkbox" name="product[]" value='r' id="product_remove"/><label class='mini-label' for='product_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Category</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="category[]" value='c' id="category_create"/><label class='mini-label' for="category_create">Create</label>
                <input type="checkbox" name="category[]" value='e' id="category_edit"/><label class='mini-label' for="category_edit">Edit</label>
                <input type="checkbox" name="category[]" value='r' id="category_remove"/><label class='mini-label' for='category_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Department</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="department[]" value='c' id="department_create"/><label class='mini-label' for="department_create">Create</label>
                <input type="checkbox" name="department[]" value='e' id="department_edit"/><label class='mini-label' for="department_edit">Edit</label>
                <input type="checkbox" name="department[]" value='r' id="department_remove"/><label class='mini-label' for='department_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Supplier</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="supplier[]" value='c' id="supplier_create"/><label class='mini-label' for="supplier_create">Create</label>
                <input type="checkbox" name="supplier[]" value='e' id="supplier_edit"/><label class='mini-label' for="supplier_edit">Edit</label>
                <input type="checkbox" name="supplier[]" value='r' id="supplier_remove"/><label class='mini-label' for='supplier_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Manufacturer</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="manufacturer[]" value='c' id="manufacturer_create"/><label class='mini-label' for="manufacturer_create">Create</label>
                <input type="checkbox" name="manufacturer[]" value='e' id="manufacturer_edit"/><label class='mini-label' for="manufacturer_edit">Edit</label>
                <input type="checkbox" name="manufacturer[]" value='r' id="manufacturer_remove"/><label class='mini-label' for='manufacturer_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Customer</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="customer[]" value='c' id="customer_create"/><label class='mini-label' for="customer_create">Create</label>
                <input type="checkbox" name="customer[]" value='e' id="customer_edit"/><label class='mini-label' for="customer_edit">Edit</label>
                <input type="checkbox" name="customer[]" value='r' id="customer_remove"/><label class='mini-label' for='customer_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        <div class="part">
            <div class="field">Stock</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="stock[]" value='c' id="stock_create"/><label class='mini-label' for="stock_create">Create</label>
                <input type="checkbox" name="stock[]" value='e' id="stock_edit"/><label class='mini-label' for="stock_edit">Edit</label>
                <input type="checkbox" name="stock[]" value='r' id="stock_remove"/><label class='mini-label' for='stock_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
        
        <div class="part">
            <div class="field">Order</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="invoice[]" value='c' id="invoice_create"/><label class='mini-label' for="invoice_create">Create</label>
                <input type="checkbox" name="invoice[]" value='e' id="invoice_edit"/><label class='mini-label' for="invoice_edit">Edit</label>
                <input type="checkbox" name="invoice[]" value='r' id="invoice_remove"/><label class='mini-label' for='invoice_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
            
        <div class="part">
            <div class="field">User</div>
            <div class="seperator"></div>
            <div class="value">
                <input type="checkbox" name="user[]" value='c' id="user_create"/><label class='mini-label' for="user_create">Create</label>
                <input type="checkbox" name="user[]" value='e' id="user_edit"/><label class='mini-label' for="user_edit">Edit</label>
                <input type="checkbox" name="user[]" value='r' id="user_remove"/><label class='mini-label' for='user_remove'>Remove</label>
            </div>
            <div class='end'></div>
        </div>
            
        <input type='hidden' name='intent' value='create'/>

        <button style="float:right;margin-top:10px;" id="role_create" class="btn btn-default">Continue</button>

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
                            window.location='<?php echo site_url();?>roles/edit/'+data.responseJSON.rid;
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
                            else if(data.responseJSON.status=='no_name')
                            {
                                $('#msgholder-role_name').parent().css('height','auto');
                                $('#msgholder-role_name').html('').html('Please Give it a Name!').slideDown();
                            }
                        }                        
                    }
                });
            });
        </script>
        <div style="clear:both;"></div>
    </form>
</div>