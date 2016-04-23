<?php date_default_timezone_set('Asia/Dhaka'); ?>
<div id="clock" class="clock" style="margin-bottom: 18px;margin-top:-44px;">
    <div style="font-size:20px;float:left;width:182px;"><?php echo date('j F, Y', strtotime($invoice['bill_time']));?></div>
    <div style="font-size:20px;float:left;"><?php echo date('h:i A', strtotime($invoice['bill_time']));?></div>
    <div style="font-size:16px;float:left;clear:left;font-family: monospace;margin-top:5px;"><?php echo $invoice['billed_by_name'];?></div>
    <div style="font-size:16px;float:right;font-family: monospace;margin-top:5px;">Invoice ID: <?php echo $invoice['generated_id'];?></div>
    <div style="clear:both;"></div>
</div>

<form style='width:60%;float:left;' action="<?php echo site_url();?>products/fetch" method="POST" id="products-fetch">

    <input type='hidden' name='intent' value='search'/>
    <input type='hidden' name='invoice' value='1'/>

    <div style="margin:0px 10px 0px 0px;font-weight:bold;">Search :</div>
    <input style="float:left;margin-right:5px;margin-top:10px;margin-left: -1px;margin-bottom: 10px;" class='form-control' autocomplete="off" type='text' id='filter' name='filter'/>
    <div id="filter_by_department" style="display:none;margin-right:5px;float:left;" class="filter-wrapper filter_select">
        <div class="select-wrap">
            <select class="filter_by_onchange">
                <?php foreach ($departments as $key => $value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>

    <div style='float:left;width:100px;' class="filter-wrapper">
        <div class="select-wrap">
            <select name="filter_by" id="filter_by">
                <?php foreach ($search_fields as $key => $value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $('#filter_by').on('change', function() {
                var filter_by=$(this).val();
                if(filter_by=='department')
                {
                    $('.filter_select').hide();
                    $('#filter').hide();
                    $('#filter').val('');

                    $('#filter_by_department').fadeIn('slow');
                    setTimeout(function(){$('#filter').val($('#filter_by_department option:first').val());},400);
                }
                else
                {
                    $('.filter_select').hide();
                    $('#filter').val('');
                    $('#filter').fadeIn('slow');
                }
            });
            $('.filter_by_onchange').on('change',function(){
                $('#filter').val($(this).val());
            });
        });
    </script>

    <div class="sort_by-wrapper" style='margin-top:10px;width:100px;'>
        <div class="select-wrap">
            <select name="sort_by" id="sort_by" onchange="$('#products-fetch').submit();">
                <?php foreach ($fields as $key => $value):?>
                <option <?php if ($key == $sort_by) {
    echo 'selected="selected"';
}?> value="<?php echo $key;?>"><?php echo $value[0];?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>
    <div style="float:right;margin:15px 10px 0px 0px;font-weight:bold;">Sort By :</div>



    <input type="hidden" id="limit" name="limit" value="<?php echo $limit;?>"/>
    <input type="hidden" id="page" name="page" value="0"/>

    <div class="slider-wrapper">
        <div id="slider"></div>
    </div>
    <script type="text/javascript">
    $(function(){
        $( "#slider" ).slider({
            range: "max",
            min: 10,
            max: 50,
            value: <?php echo $limit;?>,
            change: function( event, ui ) {
                $("#limit").val( ui.value );
                $('#page').val(0);
                $('#products-fetch').submit();
            },
            slide: function(event,ui){
                $('#limit-view').html(ui.value);
            }
        });

        $('input[name="order"]').click(function(){$('#products-fetch').submit();});
    });
    </script>

    <div class='table-wrapper'>
        <table id="products-list" class="tablesorter">
            <thead>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div style="float:left;font-size: 12px;margin-top: 18px;">Entries per page : <div style="display:inline-block;" id="limit-view"><?php echo $limit;?></div></div>

    <div class="order-wrapper" style='float:right;margin-top:15px;'>
        <?php foreach ($orders as $key => $value):?>
        <div style="margin-right: 20px;float:left;">
        <input type="radio" name="order" style="float:left;" id='order-<?php echo $key;?>' <?php if ($key == $order) {
    echo 'checked';
}?> value="<?php echo $key;?>"/>
        <label for="order-<?php echo $key;?>" style='float:left;font-size: 13px;margin-top:4px;padding-left:3px;'><?php echo $value;?></label>
        <div style="height:100%;"></div>
        </div>
        <?php endforeach;?>
        <div style="clear:both;"></div>
    </div>
    <div style="float:right;margin:15px 10px 0px 0px;font-weight:bold;">Order :</div>

    <div id="pagination" class="middle"></div>


    <script type="text/javascript">

        $(document).ready(function()
        {
            $('#products-fetch').ajaxForm({

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
                        $('#products-list thead').remove();
                        $('#products-list tbody').remove();
                        $('#pagination').empty();

                        if(data.responseJSON.results.length>0)
                        {

                            $('<thead/>',{}).appendTo('#products-list');
                            $('<tr/>',{}).appendTo('#products-list thead');

                            <?php foreach ($visible_fields as $key => $value):?>
                            $('<th/>',{style:'width:<?php echo $value[1];?>%;'}).append(document.createTextNode("<?php echo $value[0];?>")).appendTo('#products-list thead tr');
                            <?php endforeach;?>

                            results=data.responseJSON.results;

                            $('<tbody/>',{}).appendTo('#products-list');
                            for(i=0;i<results.length;i++)
                            {
                                $('<tr/>',{id:'product-'+results[i].barcode}).appendTo('#products-list tbody');

                                $('<td/>',{}).append(document.createTextNode(results[i].barcode)).appendTo('#product-'+results[i].barcode);
                                $('<td/>',{}).append(document.createTextNode(results[i].name)).appendTo('#product-'+results[i].barcode);
                                $('<td/>',{}).append(document.createTextNode(results[i].sku)).appendTo('#product-'+results[i].barcode);
                                $('<td/>',{}).append(document.createTextNode(results[i].unit)).appendTo('#product-'+results[i].barcode);
                                $('<td/>',{}).append(document.createTextNode(results[i].quantity)).appendTo('#product-'+results[i].barcode);

                                if(results.length==1)
                                {
                                    var tdata={
                                        pid     :results[0].pid,
                                        name    :results[0].name,
                                        barcode :results[0].barcode,
                                        quantity   :results[0].quantity
                                    };
                                    <?php if (user_can('CREATE_INVOICE')):?>
                                    addToCart(tdata);
                                    <?php endif;?>
                                }

                                $('#product-'+results[i].barcode).click(results[i],function(event){
                                    var data={
                                        pid     :event.data.pid,
                                        name    :event.data.name,
                                        barcode :event.data.barcode,
                                        quantity   :event.data.quantity
                                    };
                                    <?php if (user_can('CREATE_INVOICE')):?>
                                    addToCart(data);
                                    <?php endif;?>
                                });
                            }

                            $("#products-list").tablesorter({widgets: ['zebra']});

                            /* Pagination */

                            //console.log(data.responseJSON.total);
                            pages=Math.ceil(data.responseJSON.total/$('#limit').val());
                            //console.log(pages);
                            for(i=0;i<pages;i++)
                            {
                                $('<div/>',{class:'pages',id:'page-'+i,onclick:"$('#page').val("+i+");$('#products-fetch').submit();"}).append(document.createTextNode(i)).appendTo('#pagination');
                            }
                            scrollx=$('#page-'+data.responseJSON.page).offset().left - $('#pagination').offset().left - $('#pagination').width()/2;

                            $('#pagination').animate({scrollLeft:scrollx},200);
                            $('#page-'+data.responseJSON.page).addClass('active');
                            $('#limit-view').html(data.responseJSON.limit);
                        }
                    }
                    else
                    {
                    }
                }
            });

            $('#filter').bind("keydown keyup change", function(e) {
                if($('#filter_by').val()=='barcode')
                {
                    var length=Number(String($(this).val()).length);

                    if(length>12)
                    {
                        $(this).val(Number(String($(this).val()).slice(0,12)));
                    }
                }
            });

            $('#products-fetch').submit();


            var lastValue = '';
            setInterval(function(){
                var presentValue=$('#filter').val();
                if(presentValue!=lastValue)
                {
                    lastValue=presentValue;
                    $('#page').val(0);
                    $('#products-fetch').submit();
                }

            },1000);

            setInterval(function(){
                $('#invoice-table > tbody > tr td:first-child').each(function(index){
                    $(this).html(index+1);
                });

            },500);


        });
        function isInt(n){
            return typeof n== "number" && isFinite(n) && n%1===0;
        }
        <?php if (user_can('CREATE_INVOICE')): ?>

        window.vat=<?php echo $vat;?>;

        function addToCart(data)
        {
            var quantity=parseInt(prompt('Quantity','1'),10);
            var price=parseFloat(prompt('Price'));
            if(quantity!=null && isInt(quantity) && price!=null)
            {
                data.quantity=parseFloat(quantity);
                data.price=parseFloat(price);
                console.log(data);
                if($('#holder-'+data.barcode).length==0)
                {
                    jQuery('<div/>', {
                        id: 'holder-'+data.barcode,
                        title: data.name
                    }).appendTo('#bill');

                    jQuery('<input/>',{
                        id: 'pid-'+data.barcode,
                        type: 'hidden',
                        name: 'orders['+data.barcode+'][pid]'
                    }).appendTo('#holder-'+data.barcode);

                    jQuery('<input/>',{
                        id: 'barcode-'+data.barcode,
                        type: 'hidden',
                        name: 'orders['+data.barcode+'][barcode]'
                    }).appendTo('#holder-'+data.barcode);

                    jQuery('<input/>',{
                        id: 'quantity-'+data.barcode,
                        type: 'hidden',
                        name: 'orders['+data.barcode+'][quantity]'
                    }).appendTo('#holder-'+data.barcode);

                    jQuery('<input/>',{
                        id: 'price-'+data.barcode,
                        type: 'hidden',
                        name: 'orders['+data.barcode+'][price]'
                    }).appendTo('#holder-'+data.barcode);

                    $('#pid-'+data.barcode).val(data.pid);
                    $('#barcode-'+data.barcode).val(data.barcode);
                    $('#quantity-'+data.barcode).val(data.quantity);
                    $('#price-'+data.barcode).val(data.price);

                    jQuery('<tr/>',{
                        id: 'tr-'+data.barcode,
                    }).appendTo('#invoice-table tbody');

                    jQuery('<td/>',{}).append($('#bill > div').length).appendTo('#tr-'+data.barcode);
                    jQuery('<td/>',{}).append(data.name).appendTo('#tr-'+data.barcode);
                    jQuery('<td/>',{}).append(data.price).appendTo('#tr-'+data.barcode);
                    jQuery('<td/>',{id: 'product-quantity-'+data.barcode,style:'text-align:right;'}).appendTo('#tr-'+data.barcode);
                    jQuery('<td/>',{id: 'product-subtotal-'+data.barcode, class:'subtotal',style:'text-align:right;'}).appendTo('#tr-'+data.barcode);
                    jQuery('<td/>',{id: 'product-remove-'+data.barcode}).append($('<div/>',{class:'red-cross'})).appendTo('#tr-'+data.barcode);

                    jQuery('#product-remove-'+data.barcode).click(data,function(event){
                        $('#holder-'+event.data.barcode).remove();
                        $('#tr-'+event.data.barcode).remove();
                    });
                }
                $('#quantity-'+data.barcode).val(data.quantity);
                $('#price-'+data.barcode).val(data.price);

                $('#product-price-'+data.barcode).html(data.price.toFixed(2));
                $('#product-quantity-'+data.barcode).html(data.quantity);
                $('#product-subtotal-'+data.barcode).html((data.quantity * data.price).toFixed(2));

                console.log(window.total);
                $('#filter').focus();
                $('#filter').val('');
            }
        }

        setInterval(function()
        {
            window.total=0;
            window.discount=0;
            $('.subtotal').each(function()
            {
                window.total += parseFloat($(this).html());
            });
            if($('#extra_discount').val().length>0)
            {
                window.discount += parseFloat($('#extra_discount').val());
            }


            window.vatamount=window.total*window.vat/100;
            window.payable=window.total + window.vatamount - window.discount;
            window.netpayable=Math.round(window.payable);
            window.rounding=Math.abs(window.payable-window.netpayable);


            if(window.netpayable>window.payable)
                    $('#rounding').html('+'+(window.rounding).toFixed(2));
            else
                    $('#rounding').html('-'+(window.rounding).toFixed(2));

            if($('#cash_paid').val().length>0)
                window.change=parseFloat($('#cash_paid').val()) - (window.netpayable);
            else
                window.change=parseFloat(0 - (window.netpayable));

            $('#subtotal').html(window.total.toFixed(2));
            $('#vat').html((window.vatamount).toFixed(2));
            $('#payable').html((window.netpayable).toFixed(2));
            $('#change').html((window.change).toFixed(2));
            $('#t').val(window.netpayable);

        },200);

        <?php endif; ?>
    </script>
</form>

<form id="customer-form" method="POST" style="width:35%;float:left;margin-left:5%;">
    <input type="hidden" name='filter_by' value='code'/>
    <div style="margin:0px 10px 0px 0px;font-weight:bold;">Customer :</div>
    <input id='customer_filter' name='filter' style="float:left;width:55%;margin-top:10px;margin-left: -1px;margin-bottom: 10px;" class='form-control' autocomplete="off" type='text'/>
    <div style='margin-top:10px;float:right;width:40%;'>
        <div style='float:left;font-size:12px;margin-right: 5px;'>Points :</div><div id='customer_points' style='float:right;font-size:12px;font-weight:bold;'>0</div>
        <div style='float:left;font-size:12px;margin-right: 5px;margin-top:3px;clear:right;'>Amount :</div><div id='equivalent_amount' style='float:right;font-size:12px;font-weight:bold;margin-top:3px;'>0</div>
    </div>
</form>


<form id="bill-form" action="<?php echo site_url();?>invoices/ajax" method="POST" style="width:35%;float:left;margin-left:5%;">
    <input type="hidden" name="intent" value="edit"/>
    <div id="bill" style="display:none;">
        <?php foreach ($invoice['orders'] as $order):?>
        <div id="holder-<?php echo $order['barcode'];?>" title="Kid 123">
            <input id="pid-<?php echo $order['barcode'];?>" type="hidden" name="orders[<?php echo $order['barcode'];?>][pid]" value="<?php echo $order['pid'];?>"/>
            <input id="barcode-<?php echo $order['barcode'];?>" type="hidden" name="orders[<?php echo $order['barcode'];?>][barcode]" value="<?php echo $order['barcode'];?>"/>
            <input id="quantity-<?php echo $order['barcode'];?>" type="hidden" name="orders[<?php echo $order['barcode'];?>][quantity]" value="<?php echo $order['quantity'];?>"/>
            <input id="price-<?php echo $order['barcode'];?>" type="hidden" name="orders[<?php echo $order['barcode'];?>][price]" value="<?php echo $order['price'];?>"/>
        </div>
        <?php endforeach;?>
    </div>
    <table id="invoice-table" class="tablesorter-passive">
        <thead>
            <tr>
                <th>SL</th>
                <th>Item Description</th>
                <th>MRP</th>
                <th style='text-align: right;'>Qty</th>
                <th style='text-align: right;'>Total</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoice['orders'] as $order):?>
            <tr id="tr-<?php echo $order['barcode'];?>">
                <td>1</td>
                <td><?php echo $order['name'];?></td>
                <td id="product-price-<?php echo $order['barcode'];?>"><?php echo $order['price'];?></td>
                <td id="product-quantity-<?php echo $order['barcode'];?>" style="text-align:right;"><?php echo $order['quantity'];?></td>
                <td id="product-subtotal-<?php echo $order['barcode'];?>" class="subtotal" style="text-align:right;"><?php echo $order['total_sale'];?></td>
                <td id="product-remove-<?php echo $order['barcode'];?>"><div class="red-cross"></div></td>
            </tr>
            <script type='text/javascript'>
                $(document).ready(function(){
                    jQuery('#product-remove-<?php echo $order['barcode'];?>').click(function(){
                        $('#holder-<?php echo $order['barcode'];?>').remove();
                        $('#tr-<?php echo $order['barcode'];?>').remove();
                    });
                });
            </script>
            <?php endforeach;?>
        </tbody>
    </table>
    <div style='margin-bottom:10px;padding-top: 10px;border-top: 1px dotted #969696;'>
        <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Sub Total :</div>
        <div style='float:right;width:40%;'><div id='subtotal' name='subtotal' style='float:right;'>0.00</div></div>
        <div style='clear:both;'></div>
    </div>
    <div style='margin-bottom:10px;'>
        <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>VAT (+) :</div>
        <div style='float:right;width:40%;'><div id='vat' name='vat' style='float:right;'>0.00</div></div>
        <div style='clear:both;'></div>
    </div>
    <div style='margin-bottom:10px;padding-bottom:10px;border-bottom: 1px dotted #969696;'>
        <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Discount (-) :</div>
        <div style='float:right;width:40%;'><div id='discount' name='discount' style='float:right;'>0.00</div></div>
        <div style='clear:both;'></div>
    </div>
	<div style='margin-bottom:10px;'>
        <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Rounding :</div>
        <div style='float:right;width:40%;'><div id='rounding' name='rounding' style='float:right;'>0.00</div></div>
        <div style='clear:both;'></div>
    </div>
    <div style='margin-bottom:10px;padding-bottom:10px;border-bottom: 1px dotted #969696;'>
        <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Net Payable :</div>
        <div style='float:right;width:40%;'><div id='payable' name='payable' style='float:right;'>0.00</div></div>
        <div style='clear:both;'></div>
    </div>

    <div style='margin-bottom:10px;'>
        <div style='float:left;font-size:12px;margin-top:6px;width:40%;'>Payment Method :</div>
        <div style='float:right;'>
            <input type='radio' value='cash' checked="checked" id='payment_method_cash' name='payment_method'/>
            <label for='payment_method_cash' style='position:relative;top:-1px;'>Cash</label>
            <input type='radio' value='card' id='payment_method_card' name='payment_method'/>
            <label for='payment_method_card' style='position:relative;top:-1px;'>Card</label>
        </div>
        <div style='clear:both;'></div>
    </div>

    <div id='cash'>

        <div style='margin-bottom:10px;'>
            <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Cash Paid :</div>
            <div style='float:right;width:40%;'><input type='text' value='0.00' id='cash_paid' name='cash_paid' autocomplete="off" class='form-control' style='float:right;width:70%;text-align: right;'/></div>
            <div style='clear:both;'></div>
        </div>
        <div style='margin-bottom:10px;padding-bottom:10px;border-bottom: 1px dotted #969696;'>
            <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Change :</div>
            <div style='float:right;width:40%;'><div type='text' id='change' name='change' style='float:right;'>0.00</div></div>
            <div style='clear:both;'></div>
        </div>

    </div>

    <div id='card' style='display:none;margin-bottom:10px;border-bottom: 1px dotted #969696'>

        <div style='margin-bottom:10px;'>
            <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Bank :</div>
            <div style='float:right;width:102px;'>
                <div class="select-wrap">
                    <select name="bank" id="bank">
                        <?php foreach ($banks as $key => $value):?>
                        <option value="<?php echo $key;?>"><?php echo $value;?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div style='clear:both;'></div>
        </div>

    </div>

    <div style='margin-bottom:10px;'>
        <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Special Discount :</div>
        <div style='float:right;width:40%;'><input type='number' value='<?php echo $invoice['extra_discount'];?>' id='extra_discount' name='extra_discount' autocomplete="off" class='form-control' style='float:right;width:70%;text-align: right;'/></div>
        <div style='clear:both;'></div>
    </div>

    <input type="hidden" id='t' name='t' value='0'/>

    <script type='text/javascript'>
        $(document).ready(function(){

            $('input[name="payment_method"]').click(function(){
                if($('#payment_method_cash').is(':checked'))
                {
                    $('#card').hide();
                    $("#cash_paid").val(0);
                    $('#cash').fadeIn('fast');
                }
                else
                {
                    $('#cash').hide();
                    $("#cash_paid").val(0);
                    $('#card').fadeIn('fast');
                }
            });

            $('#bill-form').ajaxForm({

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
                        window.location='<?php echo site_url();?>invoices/edit/'+data.responseJSON.id;
                    else if(data.responseJSON.status=='invalid')
                    {
                        alert("Total Bill must exceed "+(data.responseJSON.threshold-1)+" Restriced by Rule : "+(data.responseJSON.rule));
                    }
                }
            });

            $("#cash_paid").keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                     // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                     // Allow: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                         // let it happen, don't do anything
                         return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
            $("#cash_paid, #extra_discount").click(function () {
                $(this).select();
            });
        });
    </script>

    <button style="float:right;clear:both;width:22%;" class="btn btn-default" id="confirm_invoice">Confirm</button>

    <button style="float:right;width:22%;margin-right:3%;" class="btn btn-default" id='remove_invoice'>Remove</button>

    <button style="float:left;width:22%;margin-right:3%;" class="btn btn-default" id='print_invoice'>Print</button>

    <?php

    $time = date_parse($invoice['bill_time']);

    $data = '';
    $data .= '{ESC}@{LF}';
    $data .= '{FONTA}{SIZE17}';
    $data .= ' KIDS & PARENTS CARE{LF}';
    $data .= '{FONTB}{SIZE0}{LF}';
    $data .= '      Quality Family Mega Mall for Parents & Child{LF}';
    $data .= '     Kalponayton Market, Mohila College Road, Pabna{LF}{LF}';
    $data .= '{FONTA}{SIZE0}';
    //$data.='------------------------------------------{LF}{LF}';
    //$data.='VAT REG NO:{LF}';
    //$data.='VAT AREA CODE:{LF}{LF}';
    $data .= '------------- RETAIL INVOICE -------------{LF}{LF}';
    $data .= 'Invoice ID: '.$invoice['generated_id'].'   Date: '.str_pad($time['day'], 2, '0', STR_PAD_LEFT).'/'.str_pad($time['month'], 2, '0', STR_PAD_LEFT).'/'.substr($time['year'], 2).'{LF}';
    $data .= 'Cashier: '.$this->session->userdata('name').'{LF}{LF}';
    //$data.='{BARCODE}'.$invoice['generated_id'].'{LF}';
    $data .= '------------------------------------------{LF}';
    $data .= '{FONTB}{SIZE0}';
    $data .= '                To Enjoy Special Discount{LF}';
    $data .= '           Please Register as a Loyal Customer{LF}';
    $data .= '{FONTA}{SIZE0}';
    $data .= '------------------------------------------{LF}{LF}';

    //$data='';
    //$data.='{ESC}@{LF}';
    $data .= '{FONTB}{SIZE0}';
    $data .= '{BEGIN}';
    $data .= 'SL{HT}Item{HT}     MRP{HT} Qty{HT}     Total{LF}{LF}';

    ?>

    <script type='text/javascript'>
        $(document).ready(function(){
            $('#confirm_invoice').click(function(event){
                $('#remove_invoice').attr('disabled','disabled');
                $('#cancel_invoice').attr('disabled','disabled');
            });
            $('#remove_invoice').click(function(event)
            {
                var jqxhr = $.ajax({
                    url : '<?php echo site_url();?>invoices/remove/<?php echo $invoice['generated_id'];?>',
                    dataType: 'json'
                })
                .success(function(data) {
                    console.log(data);
                    if(data.status=='ok')
                        window.location='<?php echo site_url();?>invoices/create';
                })
                .fail(function() {
                    //alert( "error" );
                })
                .always(function() {
                    //alert( "complete" );
                });
                event.preventDefault();
            });
            $('#print_invoice').click(function(event)
            {
                var data='<?php echo $data?>';
                //console.log(data);

                $('#invoice-table tbody tr').each(function(){
                    var sl=$(':nth-child(1)',this).html();
                    var item=$(':nth-child(2)',this).html();
                    var mrp=$(':nth-child(3)',this).html();
                    var qty=$(':nth-child(4)',this).html();
                    var total=$(':nth-child(5)',this).html();


                    mrp=('        '+mrp).slice(-8);
                    qty=('    '+qty).slice(-4);
                    total=('          '+total).slice(-10);

                    if(item.length<19)
                        data+=sl+'{HT}'+item+'{HT}'+mrp+'{HT}'+qty+'{HT}'+total+'{LF}';
                    else
                    {
                        var first=item.slice(0,19);
                        var second=item.slice(19);
                        data+=sl+'{HT}'+first+'{HT}'+mrp+'{HT}'+qty+'{HT}'+total+'{LF}';
                        data+='{HT}'+second+'{HT}{HT}{HT}{LF}';
                    }
                });

                data+='{END}';
                data+='{FONTA}{SIZE0}';
                data+='------------------------------------------{LF}';

                data+='{HT}'+('                '+'Sub Total:').slice(-16)+('             '+$('#subtotal').html()).slice(-13)+'{LF}';
                //data+='{HT}'+('                '+'(+) VAT:').slice(-16)+('             '+$('#vat').html()).slice(-13)+'{LF}';
                data+='{HT}'+('                '+'(-) Discount:').slice(-16)+('             '+$('#discount').html()).slice(-13)+'{LF}';
                data+='{HT}'+('                '+'(+/-) Rouding:').slice(-16)+('             '+$('#rounding').html()).slice(-13)+'{LF}';
                data+='              ----------------------------{LF}';
                data+='{HT}'+('                '+'Net Payable:').slice(-16)+('             '+$('#payable').html()).slice(-13)+'{LF}';

                data+='{LF}';
                data+='------------------------------------------{LF}{LF}';
                data+='{FONTB}{SIZE0}';
                data+='Thank You for Shopping with Kids & Parents Care.{LF}';
                data+='For any Query, Please call{LF}';
                data+='               01743-795117, 01728-324856    (9am - 8pm){LF}';
                data+='{LF}';
                data+='Powered By: {FONTA}{SIZE0}AllSpark Inc.';
                data+='{FONTB}{SIZE0}          www.all-spark.com{LF}{LF}{LF}{LF}{LF}{LF}';

                data+='{ESC}{CUT}';
                //console.log(data);
                //console.log(data.length);

                var jqxhr = $.ajax({
                    url : '<?php echo site_url();?>invoices/pos/',
                    method: 'POST',
                    data: {data:data}
                })
                .done(function(data){
                    //console.log(data);
                    window.location='zozo:<?php echo site_url();?>'+data;
                });

                event.preventDefault();
            });
        });
    </script>

</form>

<div style='float:left;width:35%;margin-left:5%;'>
    <form action='<?php echo site_url();?>invoices/recall' method='POST' id='recall_invoice_form'>
        <input type='text' name='recall_invoice' id='recall_invoice' autocomplete="off" placeholder="Recall Invoice" value='' style='float:left;margin-top:10px;padding: 2%;width: 96%;' class='form-control'/>
    </form>
    <?php if (isset($invoice['supplementary'])): ?>
    <div style="margin-top:15px;font-size:14px;float:right;">
        <div style="float:right;margin-bottom:10px;">Supplementary Cash : <?php echo $invoice['supplementary']['cash'];?></div>
        <div style="float:right;">Update Time : <?php echo $invoice['supplementary']['time'];?></div>
    </div>
    <?php endif;?>
</div>


<script type='text/javascript'>
    $(document).ready(function(){

        $('#recall_invoice_form').ajaxForm({
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
                    window.location='<?php echo site_url();?>invoices/edit/'+data.responseJSON.id;
            }
        });
    });
</script>
