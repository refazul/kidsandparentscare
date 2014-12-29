<div style='width:300px;font-size:40px;margin:10px auto 30px;text-align: center'>Current Stock</div>

<div style="width:300px;margin:auto;border-bottom: 2px solid #bbb;padding-bottom: 10px;">
    <div class='unit'>
        <div class="left">Total Cost</div><div class='right red' style="font-size:25px;font-family: 'verdana';" id="total_cost"></div>
        <div style='clear:both;'></div>
    </div>
    <div class="unit">
        <div class="left">Total Sale</div><div class='right green' style="font-size:25px;font-family: 'verdana';" id="total_sale"></div>
        <div style='clear:both;'></div>
    </div>    
    <div style="clear:both"></div>
</div>
<div style="width:300px;margin:10px auto 50px;">
    <div class='left'>Potential Profit</div><div class='right' style="font-size:25px;font-family: 'verdana';" id="profit"></div>
    <div style="clear:both"></div>
</div>

<form action="<?php echo site_url()?>reports/fetch" method="POST" id="currentstock-fetch">
    <input type="hidden" name="type" value="currentstock"/>    
    <script type="text/javascript">
        function numberWithCommas(x) {
            return x.toLocaleString('bn-BD');
            //return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        $(document).ready(function(){
            $('#currentstock-fetch').ajaxForm({
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
                        $('#total_cost').html(numberWithCommas(Math.round(data.responseJSON.total_cost)));
                        $('#total_sale').html(numberWithCommas(Math.round(data.responseJSON.total_sale)));                        
                        $('#profit').html(numberWithCommas(Math.round(data.responseJSON.total_sale-data.responseJSON.total_cost)));
                        
                        if(data.responseJSON.total_sale > data.responseJSON.total_cost)
                            $('#profit').addClass('green');
                        else
                            $('#profit').addClass('red');
                    }
                    
                }
            });
            $('#currentstock-fetch').submit();
        });
    </script>    
</form>
<div style="width:300px;margin:auto;text-align: right;">
    <button type="button" class="btn btn-success" id="mark">Mark</button>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#mark').click(function(){
            $.ajax({
                url:'<?php echo site_url();?>reports/mark',
            })
            .success(function(data){
                console.log(data);
                $('#currentstocks-fetch').submit();
                if(data.status=='update')
                {
                }
                else if(data.status=='insert')
                {
                }
            });
        });
    });
</script>

<form action="<?php echo site_url();?>reports/currentstocks" method="POST" id="currentstocks-fetch" style="margin:30px auto;width:60%;">
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
            $('#currentstocks-fetch').submit();
        },
        slide: function(event,ui){
            $('#limit-view').html(ui.value);
        }
    });
    
});
</script>

<div class='table-wrapper' style="height:200px;">
    <table id="currentstocks-list" class="tablesorter">
        <thead>
        </thead>
        <tbody>
        </tbody>
    </table>    
</div>
<div style="float:left;font-size: 12px;margin-top: 18px;">Entries per page : <div style="display:inline-block;" id="limit-view"><?php echo $limit;?></div></div>
<div style="clear:left;float:left;font-size: 12px;margin-top: 18px;">Matched Entries : <div style="display:inline-block;" id="total-view"></div></div>    
<div id="pagination" class="middle"></div>
<script type="text/javascript">     

    $(document).ready(function()
    {   
        $('#currentstocks-fetch').ajaxForm({

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
                    $('#currentstocks-list thead').remove();
                    $('#currentstocks-list tbody').remove();
                    $('#pagination').empty();

                    $('#total-view').html(data.responseJSON.total);                        

                    if(data.responseJSON.results.length>0)
                    {
                        console.log(data.responseJSON);

                        $('<thead/>',{}).appendTo('#currentstocks-list');
                        $('<tr/>',{}).appendTo('#currentstocks-list thead');

                        <?php foreach($fields as $key=>$value):if(!isset($value[2]))$value[2]='right;';?>
                        $('<th/>',{style:'text-align:<?php echo $value[2];?>;width:<?php echo $value[1];?>%;'}).append(document.createTextNode("<?php echo $value[0];?>")).appendTo('#currentstocks-list thead tr');
                        <?php endforeach;?>

                        results=data.responseJSON.results;

                        $('<tbody/>',{}).appendTo('#currentstocks-list');
                        for(i=0;i<results.length;i++)
                        {
                            $('<tr/>',{id:'currentstocks-'+results[i].id}).appendTo('#currentstocks-list tbody');

                            $('<td/>',{}).append(document.createTextNode(results[i].time)).appendTo('#currentstocks-'+results[i].id);
                            $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(Math.round(results[i].total_cost).toLocaleString('en-US'))).appendTo('#currentstocks-'+results[i].id);
                            $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(Math.round(results[i].total_sale).toLocaleString('en-US'))).appendTo('#currentstocks-'+results[i].id);
                            $('<td/>',{style:'text-align:right;'}).append(document.createTextNode(Math.round(results[i].potential_profit).toLocaleString('en-US'))).appendTo('#currentstocks-'+results[i].id);

                            
                        }

                        $("#currentstocks-list").tablesorter({widgets: ['zebra']});                        

                        /* Pagination */                            

                        //console.log(data.responseJSON.total);
                        pages=Math.ceil(data.responseJSON.total/$('#limit').val());
                        //console.log(pages);
                        for(i=0;i<pages;i++)
                        {
                            $('<div/>',{class:'pages',id:'page-'+i,onclick:"$('#page').val("+i+");$('#currentstocks-fetch').submit();"}).append(document.createTextNode(i)).appendTo('#pagination');
                        }                            
                        scrollx=$('#page-'+data.responseJSON.page).offset().left - $('#pagination').offset().left - $('#pagination').width()/2;

                        $('#pagination').animate({scrollLeft:scrollx},200);
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
        $('#currentstocks-fetch').submit();

                    /*
        $('#filter').bind("keydown", function(e) {
            var code = e.keyCode || e.which;
            if (code  == 13)
            {
                                    $('#page').val(0);
                $('#products-fetch').submit();
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
                $('#currentstocks-fetch').submit();
            }

        },200);


    });
</script>
</form>