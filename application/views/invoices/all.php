<form action="<?php echo site_url();?>invoices/fetch" method="POST" id="invoices-fetch">
    <div class="sort_by-wrapper">
        <div class="select-wrap">
            <select name="sort_by" id="sort_by" onchange="$('#invoices-fetch').submit();">
                <?php foreach ($sort_fields as $key => $value):?>
                <option <?php if ($key == $sort_by) {
    echo 'selected="selected"';
}?> value="<?php echo $key;?>"><?php echo $value[0];?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>
    <div style="float:right;margin:5px 10px 0px 0px;font-weight:bold;">Sort By :</div>

    <div style="float:left;margin:2px 10px 0px 0px;font-weight:bold;">Order :</div>
    <div class="order-wrapper">
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
            max: 100,
            value: <?php echo $limit;?>,
            change: function( event, ui ) {
                $("#limit").val( ui.value );
                $('#page').val(0);
                $('#invoices-fetch').submit();
            },
            slide: function(event,ui){
                $('#limit-view').html(ui.value);
            }
        });

        $('input[type="radio"]').click(function(){$('#invoices-fetch').submit();});
    });
    </script>

    <div class='table-wrapper'>
        <table id="invoices-list" class="tablesorter">
            <thead>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div style='float:right;margin-top:20px;margin-bottom: 10px;font-family:monospace;'>
        <div style='display:inline-block;width:156px;text-align:right;float:right;' id="total_total_cost"></div><div style="display:inline-block;float:right;">Net Cost:</div><br/><br/>
        <div style='display:inline-block;width:156px;text-align:right;float:right;' id="total_total_subtotal"></div><div style="display:inline-block;float:right;">Gross Sale:</div><br/>
        <div style='display:inline-block;width:156px;text-align:right;float:right;' id="total_total_discount"></div><div style="display:inline-block;float:right;">Discount:</div><br/>
        <div style='display:inline-block;width:156px;text-align:right;float:right;' id="total_total_sale"></div><div style="display:inline-block;float:right;">Net Sale:</div><br/><br/>
        <div style='display:inline-block;width:156px;text-align:right;float:right;' id="total_total_discount_percent"></div><div style="display:inline-block;float:right;">Avg. Discount %:</div><br/>
    </div>

    <div style="float:left;font-size: 12px;margin-top: 18px;">Entries per page : <div style="display:inline-block;" id="limit-view"><?php echo $limit;?></div></div>
    <div style="clear:left;float:left;font-size: 12px;margin-top: 18px;">Matched Entries : <div style="display:inline-block;" id="total-view"></div></div>

    <div class="filter-wrapper" style="clear:right;">
        <div class="select-wrap">
            <select name="filter_by" id="filter_by">
                <?php foreach ($search_fields as $key => $value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>
    <input style="float:right;display:none;margin-right:5px;margin-top:10px;" class='form-control' autocomplete="off" type='text' id='filter' name='filter'/>
    <div id="filter_by_time" style="width:auto;margin-right:5px;" class="filter-wrapper filter_select">

        <label for='_from'>From</label>
        <input type="text" id="_from" class="form-control">
        <input type="text" style='display:none;' id="from" value='' name="from" class="form-control">

        <label for='_to'>Before</label>
        <input type="text" id="_to" class="form-control">
        <input type="text" style='display:none;' id="to" name="to" class="form-control">
    </div>

    <div style="float:right;margin:15px 10px 0px 0px;font-weight:bold;">Search :</div>

    <script type="text/javascript">
        $(document).ready(function(){

            $(function() {
              $( "#_from" ).datepicker({
                dateFormat: 'yy-mm-dd',
                defaultDate: "+0w",
                changeMonth: true,
                numberOfMonths: 1,
                onSelect: function( selectedDate )
                {
                    var date=new moment(selectedDate);
                    $("#_from").val(date.format('Do MMM, YYYY'));
                    $("#from").val(selectedDate);
                    $('#page').val(0);
                    $('#invoices-fetch').submit();
                }
              });
              $( "#_to" ).datepicker({
                dateFormat: 'yy-mm-dd',
                defaultDate: "+0w",
                changeMonth: true,
                numberOfMonths: 1,
                onSelect: function( selectedDate )
                {
                    var date=new moment(selectedDate);
                    $("#_to").val(date.format('Do MMM, YYYY'));
                    $("#to").val(selectedDate);
                    $('#page').val(0);
                    $('#invoices-fetch').submit();
                }
              });
            });

            $('#filter_by').on('change',function(){
                var filter_by=$(this).val();
                if(filter_by=='bill_time')
                {
                    $('.filter_select').hide();
                    $('#filter').hide();
                    $('#filter').val('');

                    $('#filter_by_time').fadeIn('slow');
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

            $('#filter_2').val($('.filter_by_onchange_2 option:first').val());
            $('#active_2').click(function(){
                $('#page').val(0);
                $('#invoices-fetch').submit();
            });
            $('.filter_by_onchange_2').on('change',function(){
                $('#filter_2').val($(this).val());
            });
        });
    </script>

    <div id="pagination" class="middle"></div>

    <script type="text/javascript">

        $(document).ready(function()
        {
            $('#invoices-fetch').ajaxForm({

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
                        $('#invoices-list thead').remove();
                        $('#invoices-list tbody').remove();
                        $('#pagination').empty();

                        if(data.responseJSON.results.length>0)
                        {

                            $('<thead/>',{}).appendTo('#invoices-list');
                            $('<tr/>',{}).appendTo('#invoices-list thead');

                            <?php foreach ($fields as $key => $value): if (!isset($value[2])) {
     $value[2] = 'right';
 }?>
                                $('<th/>',{style:'text-align:<?php echo $value[2];?>;width:<?php echo $value[1];?>%;'}).append(document.createTextNode("<?php echo $value[0];?>")).appendTo('#invoices-list thead tr');
                            <?php endforeach;?>

                            results=data.responseJSON.results;

                            $('<tbody/>',{}).appendTo('#invoices-list');
                            for(i=0;i<results.length;i++)
                            {
                                $('<tr/>',{id:'invoice-'+results[i].invoice_id
                                    <?php if (user_can('EDIT_INVOICE')):?>
                                        ,onclick:"window.open('<?php echo site_url();?>invoices/edit/"+results[i].generated_id+"', '_blank');"
                                    <?php endif;?>
                                }).appendTo('#invoices-list tbody');

                                var discount_percent=parseFloat(results[i].extra_discount)*100/parseFloat(results[i].subtotal);
                                discount_percent=discount_percent.toFixed(2)+'%';

                                $('<td/>',{}).append(document.createTextNode(results[i].generated_id)).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:center;'}).append(document.createTextNode(results[i].billed_by)).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:center;'}).append(document.createTextNode(results[i].customer_id)).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].total_cost.toFixed(2))).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].subtotal)).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].vat)).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].discount)).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].extra_discount)).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(discount_percent)).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].total_bill)).appendTo('#invoice-'+results[i].invoice_id);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].bill_time)).appendTo('#invoice-'+results[i].invoice_id);

                                if(parseFloat(results[i].extra_discount) >= parseFloat(results[i].total_bill)*.05)
                                    $('#invoice-'+results[i].invoice_id+' td').css('background-color','#facedc');

                            }
                            $("#invoices-list").tablesorter({widgets: ['zebra']});

                            /* Pagination */

                            console.log(data.responseJSON.total);
                            pages=parseInt(data.responseJSON.total/$('#limit').val(),10)+1;
                            //console.log(pages);
                            for(i=0;i<pages;i++)
                            {
                                $('<div/>',{class:'pages',id:'page-'+i,onclick:"$('#page').val("+i+");$('#invoices-fetch').submit();"}).append(document.createTextNode(i)).appendTo('#pagination');
                            }
                            scrollx=$('#page-'+data.responseJSON.page).offset().left - $('#pagination').offset().left - $('#pagination').width()/2;

                            $('#pagination').animate({scrollLeft:scrollx},200);
                            $('#page-'+data.responseJSON.page).addClass('active');
                            $('#limit-view').html(data.responseJSON.limit);
                            $('#total-view').html(data.responseJSON.total);

                            $('#total_total_cost').html(Number(data.responseJSON.total_total_cost).toLocaleString('en-IN',{maximumFractionDigits: 2,minimumFractionDigits: 2}));
                            $('#total_total_subtotal').html(Number(data.responseJSON.total_total_subtotal).toLocaleString('en-IN',{maximumFractionDigits: 2,minimumFractionDigits: 2}));
                            $('#total_total_discount').html(Number(data.responseJSON.total_total_discount).toLocaleString('en-IN',{maximumFractionDigits: 2,minimumFractionDigits: 2}));
                            $('#total_total_sale').html(Number(data.responseJSON.total_total_sale).toLocaleString('en-IN',{maximumFractionDigits: 2,minimumFractionDigits: 2}));
                            var total_discount_percent=parseFloat(data.responseJSON.total_total_discount)*100/parseFloat(data.responseJSON.total_total_subtotal);
                            total_discount_percent=total_discount_percent.toFixed(2)+'%';
                            $('#total_total_discount_percent').html(total_discount_percent);
                        }
                        else
                        {
                            $('#total_total_cost').html('0.00');
                            $('#total_total_subtotal').html('0.00');
                            $('#total_total_discount').html('0.00');
                            $('#total_total_sale').html('0.00');
                            $('#total_total_discount_percent').html('0.00%');
                        }
                    }
                    else
                    {
                    }
                }
            });
            $('#invoices-fetch').submit();

            var lastValue_2 = '';
            setInterval(function(){
                var presentValue=$('#filter_2').val();
                if($('#active_2').is(':checked'))
                {
                    if(presentValue!=lastValue_2)
                    {
                        lastValue_2=presentValue;
                        $('#page').val(0);
                        $('#invoices-fetch').submit();
                    }
                }

            },200);


            var lastValue = '';
            setInterval(function(){
                var presentValue=$('#filter').val();
                if(presentValue!=lastValue)
                {
                    lastValue=presentValue;
                    $('#page').val(0);
                    $('#invoices-fetch').submit();
                }

            },1000);

        });
    </script>
</form>
