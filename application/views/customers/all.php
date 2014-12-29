<form action="<?php echo site_url();?>customers/fetch" method="POST" id="customers-fetch" class="major-form">
    <div class="sort_by-wrapper">        
        <div class="select-wrap">
            <select name="sort_by" id="sort_by" onchange="$('#customers-fetch').submit();">
                <?php foreach($fields as $key=>$value):?>
                <option <?php if($key==$sort_by)echo 'selected="selected"'?> value="<?php echo $key;?>"><?php echo $value[0];?></option>
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
            min: 1,
            max: 100,
            value: <?php echo $limit;?>,
            change: function( event, ui ) {
                $("#limit").val( ui.value );            
                $('#page').val(0);
                $('#customers-fetch').submit();
            },
            slide: function(event,ui){
                $('#limit-view').html(ui.value);
            }
        });

        $('input[type="radio"]').click(function(){$('#customers-fetch').submit();});
    });
    </script>

    <div class='table-wrapper'>
        <table id="customers-list" class="tablesorter">
            <thead>
            </thead>
            <tbody>
            </tbody>
        </table>    
    </div>
    <div style="float:left;font-size: 12px;margin-top: 18px;">Entries per page : <div style="display:inline-block;" id="limit-view"><?php echo $limit;?></div></div>
    <div style="clear:left;float:left;font-size: 12px;margin-top: 18px;">Matched Entries : <div style="display:inline-block;" id="total-view"></div></div>

    <div class="filter-wrapper">
        <div class="select-wrap">
            <select name="filter_by" id="filter_by">
                <?php foreach($search_fields as $key=>$value):?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php endforeach;?>
            </select>        
        </div>
    </div>
    <input style="float:right;margin-right:5px;margin-top:10px;" class='form-control' autocomplete="off" type='text' id='filter' name='filter'/>
    <div style="float:right;margin:15px 10px 0px 0px;font-weight:bold;">Search :</div>

    
    <div id="pagination" class="middle"></div>
    

    <script type="text/javascript">     

        $(document).ready(function()
        {   
            $('#customers-fetch').ajaxForm({

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
                        $('#customers-list thead').remove();
                        $('#customers-list tbody').remove();
                        $('#pagination').empty();
                        
                        if(data.responseJSON.results.length>0)
                        {

                            $('<thead/>',{}).appendTo('#customers-list');
                            $('<tr/>',{}).appendTo('#customers-list thead');

                            <?php foreach($fields as $key=>$value):?>
                            $('<th/>',{style:'width:<?php echo $value[1];?>%;text-align:<?php if(isset($value[2]))echo $value[2];else echo 'left'?>;'}).append(document.createTextNode("<?php echo $value[0];?>")).appendTo('#customers-list thead tr');
                            <?php endforeach;?>

                            results=data.responseJSON.results;

                            $('<tbody/>',{}).appendTo('#customers-list');
                            for(i=0;i<results.length;i++)
                            {
                                $('<tr/>',{id:'customer-'+results[i].cuid
                                    <?php if(user_can('EDIT_CUSTOMER')):?>
                                        ,onclick:"loadPopupBox();$('#holder').attr('src','<?php echo site_url();?>customers/miniedit/"+results[i].cuid+"');"
                                    <?php endif;?>
                                }).appendTo('#customers-list tbody');

                                $('<td/>',{}).append(document.createTextNode(results[i].cuid)).appendTo('#customer-'+results[i].cuid);
                                $('<td/>',{}).append(document.createTextNode(results[i].name)).appendTo('#customer-'+results[i].cuid);
                                /*$('<td/>',{style:'text-align:center;'}).append(document.createTextNode(results[i].point)).appendTo('#customer-'+results[i].cuid);*/
                                $('<td/>',{}).append(document.createTextNode(results[i].address)).appendTo('#customer-'+results[i].cuid);
                                $('<td/>',{}).append(document.createTextNode(results[i].city)).appendTo('#customer-'+results[i].cuid);                                
                                $('<td/>',{}).append(document.createTextNode(results[i].phone)).appendTo('#customer-'+results[i].cuid);
                                $('<td/>',{}).append(document.createTextNode(results[i].cell)).appendTo('#customer-'+results[i].cuid);
                                $('<td/>',{}).append(document.createTextNode(results[i].email)).appendTo('#customer-'+results[i].cuid);                                
                            }                        
                            $("#customers-list").tablesorter({widgets: ['zebra']});                        

                            /* Pagination */
                            
                            console.log(data.responseJSON.total);
                            pages=parseInt(data.responseJSON.total/$('#limit').val(),10)+1;
                            console.log(pages);
                            for(i=0;i<pages;i++)
                            {
                                $('<div/>',{class:'pages',id:'page-'+i,onclick:"$('#page').val("+i+");$('#customers-fetch').submit();"}).append(document.createTextNode(i)).appendTo('#pagination');
                            }
                            $('#page-'+data.responseJSON.page).addClass('active');
                            $('#limit-view').html(data.responseJSON.limit);
                            $('#total-view').html(data.responseJSON.total);
                        }
                    }
                    else
                    {
                    }
                }
            });        
            $('#customers-fetch').submit();
            
            /*
			
            $('#filter').bind("keydown", function(e) {
                var code = e.keyCode || e.which;
                if (code  == 13)
                {
					$('#page').val(0);
                    $('#customers-fetch').submit();
                }
            });
            
            */

            var lastValue = '';
            setInterval(function(){
                var presentValue=$('#filter').val();
                if(presentValue!=lastValue)
                {
                    lastValue=presentValue;
                    $('#page').val(0);
                    $('#customers-fetch').submit();
                }

            },200);
            
        });
    </script>
</form>