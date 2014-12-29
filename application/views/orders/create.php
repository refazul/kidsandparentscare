<form style='width:60%;float:left;' action="<?php echo site_url();?>products/ajax" method="POST" id="products-fetch">
    
    <input type='hidden' name='intent' value='search'/>
    
    <div style="margin:0px 10px 0px 0px;font-weight:bold;">Search :</div>
    <input style="float:left;margin-right:5px;margin-top:10px;margin-left: -1px;margin-bottom: 10px;" class='form-control' autocomplete="off" type='text' id='filter' name='filter'/>
    <div style='float:left;width:100px;' class="filter-wrapper">
        <div class="select-wrap">
            <select name="filter_by" id="filter_by">
                <?php foreach($search_fields as $key=>$value):?>
                <option <?php if($key==$sort_by)echo 'selected="selected"'?> value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>        
        </div>
    </div>
        
    <div class="sort_by-wrapper" style='margin-top:10px;width:100px;'>        
        <div class="select-wrap">
            <select name="sort_by" id="sort_by" onchange="$('#products-fetch').submit();">
                <?php foreach($fields as $key=>$value):?>
                <option <?php if($key==$sort_by)echo 'selected="selected"'?> value="<?php echo $key;?>"><?php echo $value[0];?></option>
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

        $('input[type="radio"]').click(function(){$('#products-fetch').submit();});
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
        <?php foreach($orders as $key=>$value):?>
        <div style="margin-right: 20px;float:left;">
        <input type="radio" name="order" style="float:left;" id='order-<?php echo $key;?>' <?php if($key==$order)echo 'checked'?> value="<?php echo $key;?>"/>
        <label for="order-<?php echo $key;?>" style='float:left;font-size: 13px;margin-top:4px;padding-left:3px;'><?php echo $value;?></label>
        <div style="height:100%;"></div>
        </div>
        <?php endforeach;?>
        <div style="clear:both;"></div>
    </div>
    <div style="float:right;margin:15px 10px 0px 0px;font-weight:bold;">Order :</div>
    
    <div id="pagination" class="middle"></div>
    

    <script type="text/javascript">
        window.total=0;
        window.points=0;

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

                            <?php foreach($visible_fields as $key=>$value):?>
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
                                $('<td/>',{}).append(document.createTextNode(results[i].stock)).appendTo('#product-'+results[i].barcode);
                                $('<td/>',{}).append(document.createTextNode(results[i].price)).appendTo('#product-'+results[i].barcode);
                                
                                $('#product-'+results[i].barcode).click(results[i],function(event){
                                    var data={
                                        pid     :event.data.pid,
                                        name    :event.data.name,
                                        barcode :event.data.barcode,
                                        stock   :event.data.stock,
                                        price   :event.data.price,
                                        discount_amount     :event.data.discount_amount,
                                        discount_type       :event.data.discount_type,
                                    };
                                    <?php if(user_can('CREATE_ORDER')):?>
                                    addToCart(data);
                                    <?php endif;?>
                                });
                            }
                                                     
                            $("#products-list").tablesorter({widgets: ['zebra']});                        

                            /* Pagination */                            

                            console.log('total-'+data.responseJSON.total);
                            pages=parseInt(data.responseJSON.total/$('#limit').val(),10)+1;
                            console.log('pages-'+pages);
                            for(i=0;i<pages;i++)
                            {
                                $('<div/>',{class:'pages',id:'page-'+i,onclick:"$('#page').val("+i+");$('#products-fetch').submit();"}).append(document.createTextNode(i)).appendTo('#pagination');
                            }
                            $('#page-'+data.responseJSON.page).addClass('active');
                            $('#limit-view').html(data.responseJSON.limit);
                        }
                    }
                    else
                    {
                    }
                }
            });
            /*
            
            $('#filter').bind("keydown", function(e) {
                var code = e.keyCode || e.which;
                if (code  == 13)
                {
                    $('#products-fetch').submit();
                }
            });
            
            */
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

            },500);
            

        });
        function isInt(n){
            return typeof n== "number" && isFinite(n) && n%1===0;
        }
        <?php if(user_can('CREATE_ORDER')): ?>

        window.vat=<?php echo $vat;?>;
        window.ratio=<?php echo $ratio;?>;

        function addToCart(data)
        {            
            var quantity=parseInt(prompt('Quantity','1'),10);
            if(quantity!=null && isInt(quantity))
            {
                data.quantity=parseFloat(quantity);
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
                        id: 'discount-'+data.barcode,
                        type: 'hidden',
                        class: 'discount'
                    }).appendTo('#holder-'+data.barcode);

                    $('#pid-'+data.barcode).val(data.pid);
                    $('#barcode-'+data.barcode).val(data.barcode);
                    $('#quantity-'+data.barcode).val(data.quantity);

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

                $('#product-quantity-'+data.barcode).html(data.quantity);
                $('#product-subtotal-'+data.barcode).html((data.quantity * data.price).toFixed(2));
                
                if(data.discount_type=='percent')
                    $('#discount-'+data.barcode).val((data.quantity * data.price * data.discount_amount/100));
                else
                    $('#discount-'+data.barcode).val(data.quantity * data.discount_amount);
                
                console.log(window.total);
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
            $('.discount').each(function()
            {
                window.discount += parseFloat($(this).val());
            });
            window.vatamount=window.total*window.vat/100;            
            window.payable=window.total + window.vatamount - window.discount;
            if($('#cash_paid').val().length>0)
                window.change=parseFloat($('#cash_paid').val()) - (window.total + window.vatamount - window.discount);
            else
                window.change=parseFloat(0 - (window.total + window.vatamount - window.discount));
            
            $('#subtotal').html(window.total.toFixed(2));
            $('#vat').html((window.vatamount).toFixed(2));
            $('#discount').html((window.discount).toFixed(2));
            $('#payable').html((window.payable).toFixed(2));
            $('#change').html((window.change).toFixed(2));

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


<form id="bill-form" action="<?php echo site_url();?>orders/commit" method="POST" style="width:35%;float:left;margin-left:5%;">
    <input type="hidden" id='customer_filter_hook' name='customer_filter' value='0'/>
    <div id="bill" style="display:none;">
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
            <!--<tr id="tr-612378100001"><td>1</td><td>Kid 123</td><td>15.00</td><td id="product-quantity-612378100001">20</td><td id="product-subtotal-612378100001">300</td></tr><tr id="tr-612378101446"><td>2</td><td>Lakme Sun B</td><td>15.00</td><td id="product-quantity-612378101446">2</td><td id="product-subtotal-612378101446">30</td></tr><tr id="tr-612378100002"><td>3</td><td>Baby pant</td><td>10.00</td><td id="product-quantity-612378100002">1</td><td id="product-subtotal-612378100002">10</td></tr>-->
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
        <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Net Payable :</div>
        <div style='float:right;width:40%;'><div id='payable' name='payable' style='float:right;'>0.00</div></div>
        <div style='clear:both;'></div>        
    </div>
    
    <div id='cash' style='margin-bottom:10px;'>
        <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Cash Paid :</div>
        <div style='float:right;width:40%;'><input type='text' value='0.00' id='cash_paid' name='cash_paid' autocomplete="off" class='form-control' style='float:right;width:70%;text-align: right;'/></div>
        <div style='clear:both;'></div>
    </div>

    <script type='text/javascript'>
        $(document).ready(function(){
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
            $("#cash_paid").click(function () {
                $(this).select();
            });
        });
    </script>
    
    <div style='margin-bottom:10px;'>
        <div style='float:left;font-size:12px;margin-top:6px;width:60%;'>Change :</div>
        <div style='float:right;width:40%;'><div type='text' id='change' name='change' style='float:right;'>0.00</div></div>
        <div style='clear:both;'></div>        
    </div>
    <button style="float:right;clear:both;" class="btn btn-default">Confirm</button>
</form>