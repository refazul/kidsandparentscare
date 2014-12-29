<div style="width:303px;clear:both;margin:10px auto 20px;">
    
    <?php
    
    $form=array(
        'id'=>'stock_edit_form',
        'value_width'=>178,
        'value_height'=>28,
        'action'=>site_url().'stocks/ajax'
    );
    
    $fields=array(
        'stock_buy'=>array(
            'id'=>'stock_buy',
            'type'=>'text',
            'value'=>$unit_cost,
            'title'=>'Buy'
        ),
        'stock_sell'=>array(
            'id'=>'stock_sell',
            'type'=>'text',
            'value'=>$unit_sale,
            'title'=>'Sell'
        ),
        'stock_quantity'=>array(
            'id'=>'stock_quantity',
            'type'=>'text',
            'value'=>$quantity,
            'title'=>'Showroom Quanity'
        ),
        'stock_store_quantity'=>array(
            'id'=>'stock_store_quantity',
            'type'=>'text',
            'value'=>$store_quantity,
            'title'=>'Store Quantity'
        ),
        'stock_supplier'=>array(
            'id'=>'stock_supplier',
            'type'=>'select',
            'values'=>$suppliers,
            'selected'=>$sid,
            'title'=>'Supplier'
        ),
        'stock_discount_amount'=>array(
            'id'=>'stock_discount_amount',
            'type'=>'text',
            'value'=>$discount_amount,
            'title'=>'Discount'
        ),
        'stock_discount_type'=>array(
            'id'=>'stock_discount_type',
            'type'=>'select',
            'values'=>array(
                'absolute'=>'Absolute',
                'percent'=>'Percent'
            ),
            'selected'=>$discount_type,
            'title'=>'Type'
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
        <input type='hidden' id='stock_pid' name='stock_pid' value='<?php echo $pid;?>'/>

        <button style="float:right;margin-top:10px;" id="stock_edit" class="btn btn-default">Continue</button>

        <script type="text/javascript">            
            $(document).ready(function()
            {
                setTimeout(function () {
                        $('#stock_edit').focus();
                },100);
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
                        {
                            ;//window.location=window.location
                            parent.closeIframe();
                        }
                        else
                        {
                            $('.mini-status-after').each(function(){
                                $(this).slideUp();
                            });
                            if(data.responseJSON.status=='invalid_buy')
                            {                            
                                $('#msgholder-stock_buy').parent().css('height','auto');
                                $('#msgholder-stock_buy').html('').html('Invalid Buy Price!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_sell')
                            {                            
                                $('#msgholder-stock_sell').parent().css('height','auto');
                                $('#msgholder-stock_sell').html('').html('Invalid Sell Price!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_supplier')
                            {                            
                                $('#msgholder-stock_supplier').parent().css('height','auto');
                                $('#msgholder-stock_supplier').html('').html('Invalid Supplier!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_discount_amount')
                            {                            
                                $('#msgholder-stock_discount_amount').parent().css('height','auto');
                                $('#msgholder-stock_discount_amount').html('').html('Invalid Discount Amount!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_discount_type')
                            {                            
                                $('#msgholder-stock_discount_type').parent().css('height','auto');
                                $('#msgholder-stock_discount_type').html('').html('Invalid Discount Type!').slideDown();
                            }
                        }
                        
                    }
                });
            });
        </script>
        <div style="clear:both;"></div>
    </form>
</div>