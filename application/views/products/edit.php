<div style="width:303px;clear:both;margin:10px auto 20px;">

    <?php

    $upload_form = uniqid().'_'.time();

    $this->load->vars(
                    array(
                        'form_id' => $upload_form,
                        '_scope' => 'products',
                        '_name' => uniqid().'_'.time(),
                        'destination_form_id' => 'product_edit_form',
                        'destination_hook_id' => 'product_image',
                        'DEFAULT_IMG' => asset_url().'images/alt.png',
                        'IMG' => $image,
                    ));
    //$this->load->view('general/upload');
    ?>

</div>


<div style="width:303px;clear:both;margin:10px auto 20px;">

    <?php

    $form = array(
        'id' => 'product_edit_form',
        'value_width' => 178,
        'value_height' => 28,
        'action' => site_url().'products/ajax',
    );

    $fields = array(
        'product_barcode' => array(
            'id' => 'product_barcode',
            'type' => 'text',
            'value' => $barcode,
            'title' => 'Barcode',
        ),
        'product_name' => array(
            'id' => 'product_name',
            'type' => 'text',
            'value' => $name,
            'title' => 'Name',
        ),
        'product_sku' => array(
            'id' => 'product_sku',
            'type' => 'text',
            'value' => $sku,
            'title' => 'SKU',
        ),
        'product_unit' => array(
            'id' => 'product_unit',
            'type' => 'text',
            'value' => $unit,
            'title' => 'Unit',
        ),
        'product_department' => array(
            'id' => 'product_department',
            'type' => 'select',
            'values' => $departments,
            'selected' => $department,
            'title' => 'Department',
        ),
        'product_category' => array(
            'id' => 'product_category',
            'type' => 'select',
            'values' => $categories,
            'selected' => $category,
            'title' => 'Category',
        ),
        'product_review' => array(
            'id' => 'product_review',
            'type' => 'select',
            'values' => $reviews,
            'selected' => $review,
            'title' => 'Mark as Reviewed',
        ),
        'product_image' => array(
            'id' => 'product_image',
            'type' => 'hidden',
            'value' => $image,
        ),
    );

    ?>

    <form action="<?php echo $form['action'];?>" method="POST" id="<?php echo $form['id'];?>">

        <?php foreach ($fields as $key => $field):?>

            <?php if ($field['type'] == 'text'): ?>
            <div class="part">
                <div class="field"><?php echo $field['title'];?></div>
                <div class="seperator"></div>
                <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
                    <input type="text" id="<?php echo $field['id'];?>" name="<?php echo $field['id'];?>" autocomplete="off" class="form-control" value="<?php echo $field['value'] ? $field['value'] : '';?>"/>
                    <div class="mini-status-after" id="msgholder-<?php echo $field['id'];?>"></div>
                </div>
                <div class="end"></div>
            </div>

            <?php elseif ($field['type'] == 'password'): ?>
            <div class="part">
                <div class="field"><?php echo $field['title'];?></div>
                <div class="seperator"></div>
                <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
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
                            <option <?php if ($value == $field['selected']) {
    echo 'selected="selected"';
}?> value="<?php echo $value;?>"><?php echo $key;?></option>
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

        <input type='hidden' name='intent' value='edit'/>

        <button style="float:right;margin-top:10px;" id="product_edit" class="btn btn-default">Continue</button>
        <button style="float:right;margin-top:10px;margin-right:10px;" id="print_barcode" class="btn btn-default">Print Barcode</button>

        <div style="width:45px;float:right;margin-top:10px;margin-right:18px;"><input style="height:26px;width:100%" type="number" min="1" max="50" id="Q" autocomplete="off" class="form-control" value="1"/></div>

        <?php
        $price_offset = 320;

        if ($price <= 99) {
            $price_offset += 5;
        } elseif ($price > 99 && $price <= 999) {
            $price_offset += 0;
        } elseif ($price > 999 && $price <= 9999) {
            $price_offset -= 5;
        } elseif ($price > 9999) {
            $price_offset -= 12;
        }

        $sku_offset = 390;

        if (strlen($sku) == 2) {
            $sku_offset += 20;
        } elseif (strlen($sku) == 3) {
            $sku_offset += 15;
        } elseif (strlen($sku) == 4) {
            $sku_offset += 10;
        } elseif (strlen($sku) == 5) {
            $sku_offset += 5;
        } elseif (strlen($sku) == 6) {
            $sku_offset -= 0;
        } elseif (strlen($sku) == 7) {
            $sku_offset -= 5;
        } elseif (strlen($sku) == 8) {
            $sku_offset -= 10;
        } elseif (strlen($sku) == 9) {
            $sku_offset -= 15;
        } elseif (strlen($sku) == 10) {
            $sku_offset -= 20;
        } elseif (strlen($sku) == 11) {
            $sku_offset -= 25;
        } elseif (strlen($sku) == 12) {
            $sku_offset -= 30;
        }

            $e = '{ESC}';
            $data = '';
            $data .= $e.'A';
            $data .= $e.'H0320';
            $data .= $e.'V0030';
            $data .= $e.'XSKIDS & PARENTS CARE';
            $data .= $e.'H0330';
            $data .= $e.'V0060';
            //$data.=$e.'D302050'.$barcode;
            $data .= $e.'D302050'.substr($barcode, 0, 12);
            $data .= $e.'XS'.$barcode;
            $data .= $e.'H0'.$sku_offset;
            $data .= $e.'V0140';
            $data .= $e.'XS'.$sku;
            $data .= $e.'H0'.$price_offset;
            $data .= $e.'V0170';
            $data .= $e.'H0350';
            $data .= $e.'L0102';
            $data .= $e.'XSMRP TK. '.$price;
            $data .= $e.'Q1';
            $data .= $e.'Z';
            /*

            $e='{ESC}';
            $data='';
            $data.=$e.'A';
            $data.=$e.'H0320';
            $data.=$e.'V0030';
            $data.=$e.'XSKIDS & PARENTS CARE';
            $data.=$e.'H0330';
            $data.=$e.'V0060';
            //$data.=$e.'D302050'.$barcode;
            $data.=$e.'B102050'.$barcode;
            //$data.=$e.'XS'.$barcode;
            $data.=$e.'H0'.$sku_offset;
            $data.=$e.'V0140';
            $data.=$e.'XS'.$sku;
            $data.=$e.'H0'.$price_offset;
            $data.=$e.'V0170';
            $data.=$e.'L0102';
            $data.=$e.'XSMRP TK. '.$price.' + VAT';
            $data.=$e.'Q1';
            $data.=$e.'Z';

            */

        ?>
        <script type="text/javascript">
            $(document).ready(function()
            {
                $('#print_barcode').click(function(e){
                    var data='<?php echo $data?>';
                    data=data.replace('{ESC}Q1','{ESC}Q'+$('#Q').val());
                    console.log(data);

                    var jqxhr = $.ajax({
                        url : '<?php echo site_url();?>invoices/pos/',
                        method: 'POST',
                        data: {data:data}
                    })
                    .done(function(data){
                        //console.log(data);
                        window.location='zz:www.kidsandparentscare.com/'+data;
                    });

                    e.preventDefault();
                });
                setTimeout(function () {
                    $('#product_edit').focus();
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
                        //console.log(data);

                        if(data.responseJSON.status=='ok')
                        {
                                //window.location=window.location;
                                parent.unloadPopupBox();
                        }
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

<div id="stock_table" style="clear:both;margin:35px auto 20px;">

    <?php

        unset($form);
        $form = array(
            'id' => 'stock_form',
            'action' => site_url().'stocks/ajax',
            'method' => 'POST',
        );

        unset($fields);
        $fields = array(
            'stock_id' => array(
                'id' => 'stock_id',
                'type' => 'passive',
                'title' => 'ID',
                'width' => 5,
            ),
            'stock_buy' => array(
                'id' => 'stock_buy',
                'type' => 'text',
                'value' => '',
                'title' => 'Buy',
                'width' => 5,
            ),
            'stock_sell' => array(
                'id' => 'stock_sell',
                'type' => 'text',
                'value' => '',
                'title' => 'Sell',
                'width' => 5,
            ),
            'stock_quantity' => array(
                'id' => 'stock_quantity',
                'type' => 'text',
                'value' => '0',
                'title' => 'Showroom Quantity',
                'width' => 5,
            ),
            'stock_store_quantity' => array(
                'id' => 'stock_store_quantity',
                'type' => 'text',
                'value' => '0',
                'title' => 'Store Quantity',
                'width' => 5,
            ),
            'stock_supplier' => array(
                'id' => 'stock_supplier',
                'type' => 'select',
                'values' => $suppliers,
                'title' => 'Supplier',
                'width' => 10,
            ),
            'stock_discount_amount' => array(
                'id' => 'stock_discount_amount',
                'type' => 'text',
                'value' => '0',
                'title' => 'Discount',
                'width' => 5,
            ),
            'stock_discount_type' => array(
                'id' => 'stock_discount_type',
                'type' => 'select',
                'values' => array(
                    'absolute' => 'Absolute',
                    'percent' => 'Percent',
                ),
                'title' => 'Type',
                'width' => 10,
            ),
            'stock_stocked_on' => array(
                'id' => 'stock_stocked_on',
                'title' => 'Stocked On',
                'type' => 'date',
                'last' => true,
                'width' => 10,
            ),
        );

    ?>
    <form action="<?php echo $form['action'];?>" method="<?php echo $form['method'];?>" id="<?php echo $form['id'];?>">

        <table class='tablesorter tablesorterz'>
            <thead>
                <tr>
                    <?php foreach ($fields as $key => $value):?>
                        <?php if ($value['type'] == 'hidden'): continue; endif;?>
                        <th style="font-size:10pt;<?php if (isset($value['width'])) {
    echo 'width:'.$value['width'].'%';
}?>"><?php echo $value['title'];?></th>
                    <?php endforeach;?>
                </tr>
            </thead>
            <tbody>

                <?php if (isset($preload)):?>
                    <?php foreach ($preload as $pre):?>
                        <tr onclick="loadPopupBox();$('#holder').attr('src','<?php echo site_url()?>stocks/miniedit/<?php echo $pre['stock_id'];?>');">
                            <?php foreach ($fields as $key => $value):?>
                            <?php if ($value['type'] == 'hidden'): continue; endif; ?>
                            <td><?php echo $pre[$key];?></td>
                            <?php endforeach;?>
                        </tr>
                    <?php endforeach;?>
                <?php endif;?>

                <tr>

                    <?php foreach ($fields as $key => $field):?>

                        <?php if (isset($field['type'])): if ($field['type'] == 'passive'):?>
                        <td style="width:1px;"></td>
                        <?php continue;endif;endif;?>

                        <?php if ($field['type'] == 'text'): ?>
                        <td class="part">
                            <div class="value" style="float:none;">
                                <input type="text" id="<?php echo $field['id'];?>" name="<?php echo $field['id'];?>" autocomplete="off" class="form-control" style="width:100%;padding:3px 1px;" value="<?php echo $field['value'];?>"/>
                            </div><!--value-->
                            <div class="end"></div>
                            <div class="mini-status-after" id="msgholder-<?php echo $field['id'];?>"></div>
                        </td>
                        <?php endif; ?><!--if($field['type']=='text'):-->

                        <?php if ($field['type'] == 'select'): ?>
                        <td class="part">
                            <div class="value" style="float:none;">
                                <div class="select-wrap <?php if (isset($field['width_class'])) {
    echo $field['width_class'];
}?>"">
                                    <select name="<?php echo $field['id'];?>">
                                        <?php foreach ($field['values'] as $value => $key):?>
                                        <option value="<?php echo $value;?>"><?php echo $key;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>

                            </div><!--value-->
                            <div class="end"></div>
                            <div class="mini-status-after" id="msgholder-<?php echo $field['id'];?>"></div>
                        </td>
                        <?php endif; ?><!--if($field['type']=='select'):-->

                        <?php if (isset($field['last'])): if ($field['last'] == true):?>
                        <td><button style="width:100%;height:100%;padding:0px;" id="create_stock" class="btn btn-default">Add Stock</button></td>
                        <?php endif;endif;?>

                    <?php endforeach; ?><!--foreach($fields as $key=>$field):-->
                </tr>
            </tbody>
        </table>

        <input type='hidden' id='stock_pid' name='stock_pid' value='<?php echo $pid;?>'/>
        <input type='hidden' name='intent' value='create'/>



        <script type="text/javascript">
            function closeIframe()
            {
                    unloadPopupBox();
                    window.location=window.location;
            }
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
                            if(data.responseJSON.status=='no_buy')
                            {
                                $('#msgholder-stock_buy').parent().css('height','auto');
                                $('#msgholder-stock_buy').html('').html('No Buy Price!').slideDown();
                            }
                            else if(data.responseJSON.status=='no_sell')
                            {
                                $('#msgholder-stock_sell').parent().css('height','auto');
                                $('#msgholder-stock_sell').html('').html('No Sell Price!').slideDown();
                            }
                            else if(data.responseJSON.status=='no_quantity')
                            {
                                $('#msgholder-stock_quantity').parent().css('height','auto');
                                $('#msgholder-stock_quantity').html('').html('No Stock Quantity!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_buy')
                            {
                                $('#msgholder-stock_buy').parent().css('height','auto');
                                $('#msgholder-stock_buy').html('').html('Invalid Buy Price!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_sell')
                            {
                                $('#msgholder-stock_sell').parent().css('height','auto');
                                $('#msgholder-stock_sell').html('').html('Invalid Sell Price!').slideDown();
                            }
                            else if(data.responseJSON.status=='invalid_quantity')
                            {
                                $('#msgholder-stock_quantity').parent().css('height','auto');
                                $('#msgholder-stock_quantity').html('').html('Invalid Stock Quantity!').slideDown();
                            }
                            if(data.responseJSON.status=='invalid_supplier')
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
    </form>

</div>
