<div style='width:300px;font-size:40px;margin:10px auto 30px;text-align: center'>Sales Report</div>

<div style="width:300px;margin:auto;border-bottom: 2px solid #bbb;padding-bottom: 10px;">
    <div class='unit'>
        <div class="left">Total Cost</div><div class='right red' id="total_cost"></div>
        <div style='clear:both;'></div>
    </div>
    <div class="unit">
        <div class="left">Gross Sale</div><div class='right' id="total_subtotal"></div>
        <div style='clear:both;'></div>
    </div>
    <div class="unit">
        <div class="left">Total Vat (+)</div><div class='right' id="total_vat"></div>
        <div style='clear:both;'></div>
    </div>
    <div class="unit" style='border-bottom: 1px solid #bbb;padding-bottom: 10px;'>
        <div class="left">Total Discount (-)</div><div class='right red' id="total_discount"></div>
        <div style='clear:both;'></div>
    </div>    
    <div class="unit">
        <div class="left">Net Sale</div><div class='right' id="total_bill"></div>
        <div style='clear:both;'></div>
    </div>
    <div class="unit">
        <div class="left">Cash Paid</div><div class='right' id="total_paid"></div>
    </div>
    <div style="clear:both"></div>
</div>
<div style="width:300px;margin:10px auto 150px;">
    <div class='left' >Profit</div><div class='right' id="profit"></div>
    <div style="clear:both"></div>
</div>

<form action="<?php echo site_url()?>reports/fetch" method="POST" id="profit-fetch">
    <input type="hidden" name="type" value="profit"/>    
    <script type="text/javascript">
        function numberWithCommas(x) {
            return x.toLocaleString('bn-BD');
            //return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        $(document).ready(function(){
            $('#profit-fetch').ajaxForm({
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
                        $('#total_cost').html(numberWithCommas(data.responseJSON.total_cost));
                        $('#total_subtotal').html(numberWithCommas(Math.round(data.responseJSON.total_subtotal)));
                        $('#total_discount').html(numberWithCommas(Math.round(data.responseJSON.total_discount)));
                        $('#total_vat').html(numberWithCommas(Math.round(data.responseJSON.total_vat)));
                        $('#total_bill').html(numberWithCommas(data.responseJSON.total_bill));
                        $('#total_paid').html(numberWithCommas(data.responseJSON.total_paid));
                        $('#profit').html(numberWithCommas(data.responseJSON.total_bill-data.responseJSON.total_cost));
                        
                        if(data.responseJSON.total_bill > data.responseJSON.total_cost)
                            $('#profit').addClass('green');
                        else
                            $('#profit').addClass('red');
                    }
                }
            });
            $('#profit-fetch').submit();
        });
    </script>
    <div id="filter_by_time" style="width:auto;margin-right:5px;" class="filter-wrapper">
        <label for='_from'>From</label>
        <input type="text" id="_from" class="form-control">
        <input type="text" style='display:none;' id="from" name="from" class="form-control">
        
        <label for='_to'>Before</label>
        <input type="text" id="_to" class="form-control">
        <input type="text" style='display:none;' id="to" name="to" class="form-control">
    </div>
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
                    $('#profit-fetch').submit();
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
                    $('#profit-fetch').submit();
                }
              });
            });
            
        });
    </script>
</form>