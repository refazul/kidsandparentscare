<form action="<?php echo site_url();?>reports/fetch" method="POST" id="stockentry-fetch">
    <input type="hidden" name="type" value="stockentry"/>
    
    <div class="sort_by-wrapper">        
        <div class="select-wrap">
            <select name="sort_by" id="sort_by" onchange="$('#stockentry-fetch').submit();">
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
        <input type="radio" name="order" style="float:left;" id='order-<?php echo $key?>' <?php if($key==$order)echo 'checked'?> value="<?php echo $key;?>"/>
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
                $('#stockentry-fetch').submit();
            },
            slide: function(event,ui){
                $('#limit-view').html(ui.value);
            }
        });

        $('input[type="radio"]').click(function(){$('#stockentry-fetch').submit();});
    });
    </script>

    <div class='table-wrapper'>
        <table id="stockentry-list" class="tablesorter">
            <thead>
            </thead>
            <tbody>
            </tbody>
        </table>    
    </div>
    <div style='float:right;margin-top:20px;margin-bottom: 10px;font-family:monospace;'>
        <div style='display:inline-block;width:156px;text-align:right;float:right;' id="total_total_cost"></div><div style="display:inline-block;float:right;">Total Cost:</div><br/><br/>
        <div style='display:inline-block;width:156px;text-align:right;float:right;' id="total_total_price"></div><div style="display:inline-block;float:right;">Total Price:</div><br/>
    </div>
    
    <div style="float:left;font-size: 12px;margin-top: 18px;">Entries per page : <div style="display:inline-block;" id="limit-view"><?php echo $limit;?></div></div>
    <div style="clear:left;float:left;font-size: 12px;margin-top: 18px;">Matched Entries : <div style="display:inline-block;" id="total-view"></div></div>
    
    <button type="button" class="btn btn-danger" style="display:block;float:left;clear:left;margin-top:10px;" id="generate">Generate (With Cost)</button>
<button type="button" class="btn btn-success" style="display:block;float:left;clear:left;margin-top:10px;" id="generate-withoutcost">Generate (Without Cost)</button>

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
    <input style="float:right;display:none;margin-right:5px;margin-top:10px;" value="1" class='form-control' autocomplete="off" type='text' id='filter_1' name='filter_1'/>
    <div id="filter_by_did_1" style="margin-right:5px;" class="filter-wrapper filter_select_1">
        <div class="select-wrap">
            <select class="filter_by_onchange_1">
                <?php foreach($departments as $key=>$value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>        
        </div>
    </div>
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
                <?php foreach($suppliers as $key=>$value):?>
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
    <div id="filter_by_time" style="width:auto;margin-right:5px;" class="filter-wrapper">
        <label for='_from'>From</label>
        <input type="text" id="_from" class="form-control">
        <input type="text" style='display:none;' value="" id="from" name="from" class="form-control">
        
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
                    $('#stockentry-fetch').submit();
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
                    $('#stockentry-fetch').submit();
                }
              });
            });
            
            $('#filter_by_1').on('change',function(){
                var filter_by=$(this).val();
                if(filter_by=='sid')
                {
                    $('.filter_select_1').hide();
                    $('#filter_1').val('');
                    
                    $('#filter_by_sid_1').fadeIn('slow');
                    setTimeout(function(){$('#filter_1').val($('#filter_by_sid_1 option:first').val());},400);
                }
                else if(filter_by=='did')
                {
                    $('.filter_select_1').hide();
                    $('#filter_1').val('');
                    
                    $('#filter_by_did_1').fadeIn('slow');
                    setTimeout(function(){$('#filter').val($('#filter_by_did_1 option:first').val());},400);
                }
            });
            $('#filter_by_2').on('change',function(){
                var filter_by=$(this).val();
                if(filter_by=='sid')
                {
                    $('.filter_select_2').hide();
                    $('#filter_2').val('');
                    
                    $('#filter_by_sid_2').fadeIn('slow');
                    setTimeout(function(){$('#filter_2').val($('#filter_by_sid_2 option:first').val());},400);
                }
                else if(filter_by=='did')
                {
                    $('.filter_select_2').hide();
                    $('#filter_2').val('');
                    
                    $('#filter_by_did_2').fadeIn('slow');
                    setTimeout(function(){$('#filter').val($('#filter_by_did_2 option:first').val());},400);
                }
            });
            $('#active_1').click(function(){
                $('#page').val(0);
                $('#stockentry-fetch').submit();
            });
            $('#active_2').click(function(){
                $('#page').val(0);
                $('#stockentry-fetch').submit();
            });
            
            $('.filter_by_onchange_1').on('change',function(){
                $('#filter_1').val($(this).val());
            });
            $('.filter_by_onchange_2').on('change',function(){
                $('#filter_2').val($(this).val());
            });
            
        });
    </script>
    
    <div id="pagination" class="middle"></div>
    
    <div id="dialog-confirm1" title="This will generate a Report">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure?</p>
    </div>
    <div id="dialog-confirm2" title="This will generate a Report">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure?</p>
    </div>
    
    <script type="text/javascript">     

        $(document).ready(function()
        {
            $( "#dialog-confirm1" ).dialog({
                autoOpen: false,
                resizable: false,
                height:170,
                modal: true,
                buttons: {
                  Ok: function() {
                      
                    var data={};
                                
                    if($("#from").val().length>0)data['from']=$('#from').val();
                    if($("#to").val().length>0)data['to']=$('#to').val();
                    if($("#active_1").is(':checked')){data['active_1']=true;data['filter_1']=$('#filter_1').val()};
                    if($("#active_2").is(':checked')){data['active_2']=true;data['filter_2']=$('#filter_2').val()};
                    data['sort_by']=$('#sort_by').val();
                    data['order']=$('input[name="order"]:checked').val();
                    data['include_cost']=1;

                    var jqxhr = $.ajax({
                        url : '<?php echo site_url();?>reports/viewPdf',
                        method: 'POST',
                        data: data
                    })
                    .error(function(data){
                        console.log(data);
                    })
                    .done(function(data){
                        console.log(data);
                        var command='<?php echo asset_url();?>temp/'+data.file+'.html,/reports/'+data.file+'.pdf';
                        window.location='tango:'+command;
                    });
                    
                    $( this ).dialog( "close" );
                  },
                  Cancel: function() {
                    $( this ).dialog( "close" );
                  }
                }
            });
            $( "#dialog-confirm2" ).dialog({
                autoOpen: false,
                resizable: false,
                height:170,
                modal: true,
                buttons: {
                  Ok: function() {
                      
                    var data={};
                                
                    if($("#from").val().length>0)data['from']=$('#from').val();
                    if($("#to").val().length>0)data['to']=$('#to').val();
                    if($("#active_1").is(':checked')){data['active_1']=true;data['filter_1']=$('#filter_1').val()};
                    if($("#active_2").is(':checked')){data['active_2']=true;data['filter_2']=$('#filter_2').val()};
                    data['sort_by']=$('#sort_by').val();
                    data['order']=$('input[name="order"]:checked').val();

                    var jqxhr = $.ajax({
                        url : '<?php echo site_url();?>reports/viewPdf',
                        method: 'POST',
                        data: data
                    })
                    .error(function(data){
                        console.log(data);
                    })
                    .done(function(data){
                        console.log(data);
                        var command='<?php echo asset_url();?>temp/'+data.file+'.html,/reports/'+data.file+'.pdf';
                        window.location='tango:'+command;
                    });
                      
                    $( this ).dialog( "close" );
                  },
                  Cancel: function() {
                    $( this ).dialog( "close" );
                  }
                }
            });
            $('#stockentry-fetch').ajaxForm({

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
                        $('#stockentry-list thead').remove();
                        $('#stockentry-list tbody').remove();
                        $('#pagination').empty();
                        
                        $('#total-view').html(data.responseJSON.total);                        
                        
                        if(data.responseJSON.results.length>0)
                        {
                            $('<thead/>',{}).appendTo('#stockentry-list');
                            $('<tr/>',{}).appendTo('#stockentry-list thead');

                            <?php foreach($fields as $key=>$value):if(!isset($value[2]))$value[2]='left;padding-right:10px';?>
                            $('<th/>',{style:'text-align:<?php echo $value[2];?>;width:<?php echo $value[1];?>%;'}).append(document.createTextNode("<?php echo $value[0];?>")).appendTo('#stockentry-list thead tr');
                            <?php endforeach;?>

                            results=data.responseJSON.results;

                            $('<tbody/>',{}).appendTo('#stockentry-list');
                            for(i=0;i<results.length;i++)
                            {
                                $('<tr/>',{id:'stock-'+results[i].stid
                                    <?php if(user_can('DO_NOTHING')):?>
                                        ,onclick:"loadPopupBox();$('#holder').attr('src','<?php echo site_url();?>stocks/miniedit/"+results[i].stid+"');"
                                    <?php endif;?>
                                }).appendTo('#stockentry-list tbody');
                                
                                $('<td/>',{}).append(document.createTextNode(results[i].stid)).appendTo('#stock-'+results[i].stid);
                                $('<td/>',{}).append(document.createTextNode(results[i].sku)).appendTo('#stock-'+results[i].stid);
                                $('<td/>',{}).append(document.createTextNode(results[i].supplier)).appendTo('#stock-'+results[i].stid);
                                $('<td/>',{}).append(document.createTextNode(results[i].department)).appendTo('#stock-'+results[i].stid);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].unit_cost)).appendTo('#stock-'+results[i].stid);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(results[i].unit_sale)).appendTo('#stock-'+results[i].stid);
                                $('<td/>',{style:'text-align:center;'}).append(document.createTextNode(results[i].base_quantity)).appendTo('#stock-'+results[i].stid);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(Number(results[i].total_cost).toFixed(2))).appendTo('#stock-'+results[i].stid);
                                $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(Number(results[i].total_sale).toFixed(2))).appendTo('#stock-'+results[i].stid);
                                $('<td/>',{style:'text-align:center;'}).append(document.createTextNode(results[i].stocked_on)).appendTo('#stock-'+results[i].stid);
                                
                            }
                            
                            $("#stockentry-list").tablesorter({widgets: ['zebra']});                        

                            /* Pagination */                            

                            //console.log(data.responseJSON.total);
                            pages=Math.ceil(data.responseJSON.total/$('#limit').val());
                            //console.log(pages);
                            for(i=0;i<pages;i++)
                            {
                                $('<div/>',{class:'pages',id:'page-'+i,onclick:"$('#page').val("+i+");$('#stockentry-fetch').submit();"}).append(document.createTextNode(i)).appendTo('#pagination');
                            }                            
                            scrollx=$('#page-'+data.responseJSON.page).offset().left - $('#pagination').offset().left - $('#pagination').width()/2;
                            
                            $('#pagination').animate({scrollLeft:scrollx},200);
                            $('#page-'+data.responseJSON.page).addClass('active');
                            $('#limit-view').html(data.responseJSON.limit);
                            $('#total-view').html(data.responseJSON.total);
                            
                            $('#total_total_cost').html(Number(data.responseJSON.total_total_cost).toLocaleString('en-IN',{maximumFractionDigits: 2,minimumFractionDigits: 2}));
                            $('#total_total_price').html(Number(data.responseJSON.total_total_price).toLocaleString('en-IN',{maximumFractionDigits: 2,minimumFractionDigits: 2}));
                            
                            $('#generate').click(function(){
                                
                                $('#dialog-confirm1').dialog("open");
                                event.preventDefault();
                                
                            });
                            $('#generate-withoutcost').click(function(){
                                
                                $('#dialog-confirm2').dialog("open");
                                event.preventDefault();
                            });
                        }
                        else
                        {
                            $('#total_total_cost').html('0.00');
                            $('#total_total_price').html('0.00');
                            $('#generate').attr('onclick','').unbind('click');
                            $('#generate-withoutcost').attr('onclick','').unbind('click');
                        }
                    }
                    else
                    {
                    }
                }
            });
            //$('#stockentry-fetch').submit();
            
            /*
            $('#filter').bind("keydown", function(e) {
                var code = e.keyCode || e.which;
                if (code  == 13)
                {
                    $('#page').val(0);
                    $('#stockentry-fetch').submit();
                }
            });
            */
            
            var lastValue_1 = '';
            setInterval(function(){
                var presentValue=$('#filter_1').val();
                if(presentValue!=lastValue_1)
                {
                    lastValue_1=presentValue;
                    $('#page').val(0);
                    $('#stockentry-fetch').submit();
                }

            },200);
            var lastValue_2 = '';
            setInterval(function(){
                var presentValue=$('#filter_2').val();
                if(presentValue!=lastValue_2)
                {
                    lastValue_2=presentValue;
                    $('#page').val(0);
                    $('#stockentry-fetch').submit();
                }

            },200);            
            
        });
    </script>
</form>

