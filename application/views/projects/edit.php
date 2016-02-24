<div style="width:550px;clear:both;margin:10px auto 20px;">

    <?php

    $form=array(
        'id'=>'project_edit_form',
        'value_width'=>300,
        'value_height'=>28,
        'action'=>site_url().'projects/ajax'
    );

    $fields=array(
        'project_name'=>array(
            'id'=>'project_name',
            'type'=>'text',
            'value'=>$name,
            'title'=>'FILE NUMBER'
        ),
        'project_buyer'=>array(
            'id'=>'buyer_id',
            'type'=>'select',
            'values'=>$buyers,
            'selected'=>$buyer_id,
            'title'=>'BUYER NAME'
        ),
        'project_suppliers'=>array(
            'id'=>'supplier_id',
            'type'=>'select',
            'values'=>$suppliers,
            'selected'=>$supplier_id,
            'title'=>'SUPPLIER NAME'
        ),

        'sales_confirmation_origin'=>array(
            'id'=>'s_c_origin',
            'type'=>'text',
            'value'=>$s_c_origin,
            'title'=>'ORIGIN'
        ),
        'sales_confirmation_specification'=>array(
            'id'=>'s_c_specification',
            'type'=>'text',
            'value'=>$s_c_specification,
            'title'=>'SPECIFICATION'
        ),
        'sales_confirmation_quantity'=>array(
            'id'=>'s_c_quantity',
            'type'=>'text',
            'value'=>$s_c_quantity,
            'title'=>'QUANTITY'
        ),
        'sales_confirmation_price'=>array(
            'id'=>'s_c_price',
            'type'=>'text',
            'value'=>$s_c_price,
            'title'=>'PRICE'
        ),
        'sales_confirmation_commission_rate'=>array(
            'id'=>'s_c_commission_rate',
            'type'=>'text',
            'value'=>$s_c_commission_rate,
            'title'=>'COMMISSION RATE (%)'
        ),
        'sales_confirmation_commission_point'=>array(
            'id'=>'s_c_commission_point',
            'type'=>'text',
            'value'=>$s_c_commission_point,
            'title'=>'COMMISSION RATE (POINT)'
        ),
        'sales_confirmation_shipment'=>array(
            'id'=>'s_c_shipment',
            'type'=>'text',
            'value'=>$s_c_shipment,
            'title'=>'SHIPMENT'
        ),
        'sales_confirmation_payment'=>array(
            'id'=>'s_c_payment',
            'type'=>'select',
            'values'=>array('at_sight'=>'AT SIGHT','deferred'=>'DEFERRED','upass'=>'UPASS'),
            'selected'=>$s_c_payment,
            'title'=>'PAYMENT'
        ),
        'sales_confirmation_latest_date_of_lc_opening'=>array(
            'id'=>'s_c_latest_date_of_lc_opening',
            'type'=>'text',
            'value'=>$s_c_latest_date_of_lc_opening,
            'title'=>'LATEST DATE OF LC OPENING'
        ),
        'sales_confirmation_path'=>array(
            'id'=>'s_c_path',
            'type'=>'hidden',
            'value'=>$s_c_path,
            'title'=>''
        ),
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
                    <div class="select-wrap" style="height:100%;">
                        <select name="<?php echo $field['id'];?>" style="height:100%;">
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

        <script type="text/javascript">
            $(function() {
               $( "#s_c_latest_date_of_lc_opening" ).datepicker({
                  dateFormat: 'yy-mm-dd',
                  defaultDate: "+0w",
                  changeMonth: true,
                  numberOfMonths: 1,
                  onSelect: function( selectedDate ){
                     /*
                     var date=new moment(selectedDate);
                     $("#s_c_latest_date_of_lc_opening").val(date.format('Do MMM, YYYY'));
                     $("#from").val(selectedDate);
                     $('#page').val(0);
                     $('#invoices-fetch').submit();
                     */
                  }
               });
            });
            $(document).ready(function()
            {
               $('#<?php echo $form['id'];?>').ajaxForm({
                  /* set data type json */
                  dataType:  'json',

                  /* reset before submitting */
                  beforeSend: function(arr, $form, options){
                     console.log(arr, $form, options);
                     return false;
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
                        if(data.responseJSON.status=='no_name')
                        {
                           $('#msgholder-project_name').parent().css('height','auto');
                           $('#msgholder-project_name').html('').html('Please Give it a Name!').slideDown();
                        }
                     }
                  }
               });
               $('#project_edit').unbind('click');
               $('#project_edit').click(function(){
                  $('#project_edit_form').submit();
               });
            });
        </script>
        <div style="clear:both;"></div>
    </form>
    <div style="clear:both;margin:10px auto 20px;">

        <?php

        $upload_form =  uniqid().'_'.time();

        $this->load->vars(
                        array(
                            'form_id'=>$upload_form,
                            '_scope'=>'sales_confirmation',
                            '_name'=>uniqid().'_'.time(),
                            'destination_form_id'=>'project_edit_form',
                            'destination_hook_id'=>'s_c_path',
                            'DEFAULT_IMG'=>asset_url().'images/alt.png',
                            'IMG'=>$s_c_path,
                            'LABEL'=>'UPLOAD SALES CONFIRMATION'
                        ));
        $this->load->view('general/upload');
        ?>

    </div>
    <button style="float:right;margin-top:10px;" id="project_edit" class="btn btn-default">Continue</button>
</div>
