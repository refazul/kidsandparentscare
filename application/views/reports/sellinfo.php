<form action="<?php echo site_url();?>reports/fetch" method="POST" id="sellinfo-fetch">
    <input type="hidden" name="type" value="sellinfo"/>
    
    <div class="sort_by-wrapper">        
        <div class="select-wrap">
            <select name="sort_by" id="sort_by" onchange="$('#sellinfo-fetch').submit();">
                <?php foreach($sort_fields as $key=>$value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>
        </div>        
    </div>
    <div style="float:right;margin:5px 10px 0px 0px;font-weight:bold;">Sort By :</div>

    <div style="float:left;margin:2px 10px 0px 0px;font-weight:bold;">Order :</div>
    <div class="order-wrapper">        
        <?php foreach($orders as $key=>$value):?>
        <div style="margin-right: 20px;float:left;">
        <input type="radio" name="order" style="float:left;" id='order-<?php echo $key;?>' <?php if($key==$order)echo 'checked'?> value="<?php echo $key;?>"/>
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
                $('#sellinfo-fetch').submit();
            },
            slide: function(event,ui){
                $('#limit-view').html(ui.value);
            }
        });

        $('input[type="radio"]').click(function(){$('#sellinfo-fetch').submit();});
    });
    </script>

    <div class='table-wrapper'>
        <table id="sellinfo-list" class="tablesorter">
            <thead>
            </thead>
            <tbody>
            </tbody>
        </table>    
    </div>
    <div style='float:right;margin-top:20px;margin-bottom: 10px;font-family:monospace;'>
        <div style="display:inline-block;">Total Cost:</div><div style='display:inline-block;width:156px;text-align:right;' id="total_total_cost"></div><br/><br/>
        <div style="display:inline-block;">Total Sale:</div><div style='display:inline-block;width:156px;text-align:right;' id="total_total_sale"></div><br/>
    </div>
    
    <div style="float:left;font-size: 12px;margin-top: 18px;">Entries per page : <div style="display:inline-block;" id="limit-view"><?php echo $limit;?></div></div>
    <div style="clear:left;float:left;font-size: 12px;margin-top: 18px;">Matched Entries : <div style="display:inline-block;" id="total-view"></div></div>

    <!-----------------------------------------------FILTER 1----------------------------------------------->
    <div class="filter-wrapper" style="clear:right;">
        <div class="select-wrap">
            <select name="filter_by_1" id="filter_by_1">
                <?php foreach($search_fields_1 as $key=>$value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>        
        </div>
    </div>
    <input style="float:right;width:142px;margin-right:5px;margin-top:10px;" value="" class='form-control' autocomplete="off" type='text' id='filter_1' name='filter_1'/>
    <div style="float:right;float: right;margin-right: 10px;margin-top: 14px;"><input type="checkbox" name="active_1" id="active_1" value="1"/></div>
    
    
    <!-----------------------------------------------FILTER 2----------------------------------------------->    
    <div class="filter-wrapper" style="clear:right;">
        <div class="select-wrap">
            <select name="filter_by_2" id="filter_by_2">
                <?php foreach($search_fields_2 as $key=>$value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>        
        </div>
    </div>
    <input style="float:right;display:none;margin-right:5px;margin-top:10px;" value="1" class='form-control' autocomplete="off" type='text' id='filter_2' name='filter_2'/>
    <div id="filter_by_sid_2" style="margin-right:5px;" class="filter-wrapper filter_select_2">
        <div class="select-wrap">
            <select class="filter_by_onchange_2">
                <?php foreach($departments as $key=>$value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>        
        </div>
    </div>
    <div style="float:right;float: right;margin-right: 10px;margin-top: 14px;"><input type="checkbox" id="active_2" name="active_2" value="1"/></div>
    
    <!-----------------------------------------------FILTER 3----------------------------------------------->    
    <div class="filter-wrapper" style="clear:right;">
        <div class="select-wrap">
            <select name="filter_by_3" id="filter_by_3">
                <?php foreach($search_fields_3 as $key=>$value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>        
        </div>
    </div>
    <input style="float:right;display:none;margin-right:5px;margin-top:10px;" value="1" class='form-control' autocomplete="off" type='text' id='filter_3' name='filter_3'/>
    <div id="filter_by_sid_3" style="margin-right:5px;" class="filter-wrapper filter_select_2">
        <div class="select-wrap">
            <select class="filter_by_onchange_3">
                <?php foreach($suppliers as $key=>$value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>        
        </div>
    </div>
    <div style="float:right;float: right;margin-right: 10px;margin-top: 14px;"><input type="checkbox" id="active_3" name="active_3" value="1"/></div>
    
    <!-----------------------------------------------FILTER 4----------------------------------------------->
    <div class="filter-wrapper" style="clear:right;">
        <div class="select-wrap">
            <select name="filter_by_4" id="filter_by_4">
                <?php foreach($search_fields_4 as $key=>$value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>        
        </div>
    </div>    
    <div id="filter_by_time" style="width:auto;margin-right:5px;" class="filter-wrapper">
        <label for='_from'>From</label>
        <input type="text" id="_from" class="form-control">
        <input type="text" style='display:none;' id="from" name="from" class="form-control">
        
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
                    $('#sellinfo-fetch').submit();
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
                    $('#sellinfo-fetch').submit();
                }
              });
            });
            
            $('#filter_by_1').on('change',function(){
                $("#filter_1").val('');
            });
            
            $('#active_1').click(function(){
                $('#page').val(0);
                $('#sellinfo-fetch').submit();
            });
            $('#active_2').click(function(){
                $('#page').val(0);
                $('#sellinfo-fetch').submit();
            });
            $('#active_3').click(function(){
                $('#page').val(0);
                $('#sellinfo-fetch').submit();
            });
            
            $('.filter_by_onchange_2').on('change',function(){
                $('#filter_2').val($(this).val());
            });
            $('.filter_by_onchange_3').on('change',function(){
                $('#filter_3').val($(this).val());
            });
            
        });
    </script>
    
    <div id="pagination" class="middle"></div>    

    <script type="text/javascript">     

        $(document).ready(function()
        {   
            $('#sellinfo-fetch').ajaxForm({

                /* set data type json */
                dataType:  'json',

                /* reset before submitting */
                beforeSend: function() {                                                                                
                },
                
                beforeSubmit: function(formData) {                
                    //formData[1].value = CryptoJS.MD5(formData[1].value).toString();
                    //console.log(formData);                
                },

                /* progress bar call back*/
                uploadProgress: function(event, position, total, percentComplete) {                                        
                },

                /* complete call back */
                complete: function(data) {
                    console.log(data);
                    if(data.responseJSON.status=='ok')
                    {
                        $('#sellinfo-list thead').remove();
                        $('#sellinfo-list tbody').remove();
                        $('#pagination').empty();
                        
                        $('#total-view').html(data.responseJSON.total);                        
                        
                        if(data.responseJSON.results.length>0)
                        {
                            $('<thead/>',{}).appendTo('#sellinfo-list');
                            $('<tr/>',{}).appendTo('#sellinfo-list thead');

                            <?php foreach($fields as $key=>$value):if(!isset($value[2]))$value[2]='left;padding-right:10px';?>
                            $('<th/>',{style:'text-align:<?php echo $value[2];?>;width:<?php echo $value[1];?>%;'}).append(document.createTextNode("<?php echo $value[0];?>")).appendTo('#sellinfo-list thead tr');
                            <?php endforeach;?>

                            results=data.responseJSON.results;

                            $('<tbody/>',{}).appendTo('#sellinfo-list');
                            for(i=0;i<results.length;i++)
                            {
                                $('<tr/>',{id:'stock-'+results[i].oid
                                    <?php if(user_can('EDIT_INVOICE') && user_can('REMOVE_INVOICE')):?>
                                        ,onclick:"window.open('<?php echo site_url();?>invoices/edit/"+results[i].invoice+"', '_blank');"
                                    <?php endif;?>
                                }).appendTo('#sellinfo-list tbody');
                                
                                $('<td/>',{}).append(document.createTextNode(results[i].barcode)).appendTo('#stock-'+results[i].oid);
                                $('<td/>',{}).append(document.createTextNode(results[i].sku)).appendTo('#stock-'+results[i].oid);
                                $('<td/>',{}).append(document.createTextNode(results[i].name)).appendTo('#stock-'+results[i].oid);
                                $('<td/>',{}).append(document.createTextNode(results[i].department)).appendTo('#stock-'+results[i].oid);
                                $('<td/>',{}).append(document.createTextNode(results[i].supplier)).appendTo('#stock-'+results[i].oid);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].total_cost)).appendTo('#stock-'+results[i].oid);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].total_sale)).appendTo('#stock-'+results[i].oid);
                                $('<td/>',{style:'text-align:center;'}).append(document.createTextNode(results[i].quantity)).appendTo('#stock-'+results[i].oid);
                                $('<td/>',{style:'text-align:center;'}).append(document.createTextNode(results[i].time)).appendTo('#stock-'+results[i].oid);
                                
                            }
                            
                            $("#sellinfo-list").tablesorter({widgets: ['zebra']});                        

                            /* Pagination */                            

                            //console.log(data.responseJSON.total);
                            pages=Math.ceil(data.responseJSON.total/$('#limit').val());
                            //console.log(pages);
                            for(i=0;i<pages;i++)
                            {
                                $('<div/>',{class:'pages',id:'page-'+i,onclick:"$('#page').val("+i+");$('#sellinfo-fetch').submit();"}).append(document.createTextNode(i)).appendTo('#pagination');
                            }                            
                            scrollx=$('#page-'+data.responseJSON.page).offset().left - $('#pagination').offset().left - $('#pagination').width()/2;
                            
                            $('#pagination').animate({scrollLeft:scrollx},200);
                            $('#page-'+data.responseJSON.page).addClass('active');
                            $('#limit-view').html(data.responseJSON.limit);
                            $('#total-view').html(data.responseJSON.total);
                            
                            $('#total_total_cost').html(Number(data.responseJSON.total_total_cost).toLocaleString('en-IN',{maximumFractionDigits: 2,minimumFractionDigits: 2}));
                            $('#total_total_sale').html(Number(data.responseJSON.total_total_sale).toLocaleString('en-IN',{maximumFractionDigits: 2,minimumFractionDigits: 2}));
                        }
                        else
                        {
                            $('#total_total_cost').html('0.00');
                            $('#total_total_sale').html('0.00');
                        }
                    }
                    else
                    {
                    }
                    
                }
            });
            $('#sellinfo-fetch').submit();
            
            /*
            $('#filter').bind("keydown", function(e) {
                var code = e.keyCode || e.which;
                if (code  == 13)
                {
                    $('#page').val(0);
                    $('#sellinfo-fetch').submit();
                }
            });
            */
            
            var lastValue_1 = '';
            setInterval(function(){
                var presentValue=$('#filter_1').val();
                if($('#active_1').is(':checked'))
                {
                    if(presentValue!=lastValue_1)
                    {
                        lastValue_1=presentValue;
                        $('#page').val(0);
                        $('#sellinfo-fetch').submit();
                    }
                }

            },200);
            var lastValue_2 = '';
            setInterval(function(){
                var presentValue=$('#filter_2').val();
                if($('#active_2').is(':checked'))
                {
                    if(presentValue!=lastValue_2)
                    {
                        lastValue_2=presentValue;
                        $('#page').val(0);
                        $('#sellinfo-fetch').submit();
                    }
                }

            },200);
            var lastValue_3 = '';
            setInterval(function(){
                var presentValue=$('#filter_3').val();
                if($('#active_3').is(':checked'))
                {
                    if(presentValue!=lastValue_3)
                    {
                        lastValue_3=presentValue;
                        $('#page').val(0);
                        $('#sellinfo-fetch').submit();
                    }
                }

            },200);
            
        });
    </script>
</form>