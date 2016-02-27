<div style="width:550px;clear:both;margin:10px auto 20px;">

   <?php

   $form=array(
      'id'=>'project_create_form',
      'value_width'=>300,
      'value_height'=>28,
      'action'=>site_url().'projects/ajax'
   );

   ?>
   <script type='text/javascript'>
      var template=JSON.parse('<?php echo $template;?>');
   </script>

   <!-- START -->

      <?php foreach($domains as $root=>$domain): if(!isset($domain->fields))continue; $fields=$domain->fields;?>
      <div style='border: 2px dashed #ccc; padding: 10px;margin-bottom: 10px;'>
         <div style=''><?php echo $root; ?></div>
         <?php foreach($fields as $field):?>

            <?php if($field->type=='text' || $field->type=='number'): ?>
            <div class="part" id="<?php echo $field->id;?>-wrapper">
               <div class="field"><?php echo $field->title;?></div>
               <div class="seperator"></div>
               <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
                  <input type="text" id="<?php echo $field->id;?>" name="<?php echo $field->id;?>" autocomplete="off" class="form-control" value="<?php echo $field->value?$field->value:'';?>"/>
                  <div class="mini-status-after" id="msgholder-<?php echo $field->id;?>"></div>
               </div>
               <div class="end"></div>
            </div>

            <?php elseif($field->type=='password'): ?>
            <div class="part" id="<?php echo $field->id;?>-wrapper">
                <div class="field"><?php echo $field->title;?></div>
                <div class="seperator"></div>
                <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
                    <input type="password" id="<?php echo $field->id;?>" name="<?php echo $field->id;?>" autocomplete="off" class="form-control" value="<?php echo $field->value?$field->value:'';?>"/>
                    <div class="mini-status-after" id="msgholder-<?php echo $field->id;?>"></div>
                </div>
                <div class="end"></div>
            </div>

            <?php elseif($field->type=='select'): ?>
            <div class="part" id="<?php echo $field->id;?>-wrapper">
                <div class="field"><?php echo $field->title;?></div>
                <div class="seperator"></div>
                <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;margin-bottom:3px;">
                    <div class="select-wrap" style="height:100%;">
                        <select id="<?php echo $field->id;?>" name="<?php echo $field->id;?>" style="height:100%;">
                            <?php foreach($field->values as $value=>$key):?>
                            <option <?php if($value==$field->value)echo 'selected="selected"'?> value="<?php echo $value;?>"><?php echo $key;?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="end"></div>
            </div>
            <?php endif; ?>

            <?php if($field->type=='hidden' || $field->type=='date' || $field->type=='file'): ?>
               <input type='hidden' id='<?php echo $field->id;?>' name='<?php echo $field->id;?>' value='<?php echo $field->value?$field->value:'';?>'/>
            <?php endif; ?>

            <?php if($field->type=='date'): ?>
            <div class="part" id="<?php echo $field->id;?>-wrapper">
               <div class="field"><?php echo $field->title;?></div>
               <div class="seperator"></div>
               <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
                  <input data-target='<?php echo $field->id;?>' type="text" autocomplete="off" class="form-control date" value="<?php echo $field->value?$field->value:'';?>"/>
                  <div class="mini-status-after" id="msgholder-<?php echo $field->id;?>"></div>
               </div>
               <div class="end"></div>
            </div>
            <?php endif; ?>

            <?php if($field->type=='file'): ?>
            <div style="clear:both;margin:10px auto 20px;" id="<?php echo $field->id;?>-wrapper">

               <?php

                  $upload_form =  uniqid().'_'.time();

                  $this->load->vars(
                  array(
                     'form_id'=>$upload_form,
                     '_scope'=>$root,
                     '_name'=>uniqid().'_'.time(),
                     'destination_form_id'=>NULL,
                     'destination_hook_id'=>$field->id,
                     'DEFAULT_IMG'=>asset_url().'images/alt.png',
                     'IMG'=>NULL,
                     'LABEL'=>$field->title
                  ));
                  $this->load->view('general/upload');
               ?>

            </div>
            <?php endif; ?>

            <?php if(isset($field->conditional)):?>
               <?php foreach($field->conditional as $condition=>$value): ?>
                  <script type='text/javascript'>
                     $('#'+'<?php echo $condition;?>').change(function(){
                        console.log($(this).val());
                        if($(this).val()=='<?php echo $value;?>')
                           $('#'+'<?php echo $field->id; ?>'+'-wrapper').removeClass('hidden');
                        else
                           $('#'+'<?php echo $field->id; ?>'+'-wrapper').addClass('hidden');
                     });
                     $('#'+'<?php echo $condition;?>').change();
                  </script>
               <?php endforeach; ?>
            <?php endif; ?>

         <?php endforeach; ?>
      </div>
      <?php endforeach; ?>

      <script type="text/javascript">
         $(function() {
            $( ".date" ).each(function(){
               var selectedDate=$(this).val();
               if(selectedDate=='')return;

               var date=new moment(selectedDate);
               $(this).val(date.format('Do MMM, YYYY'));
            });
            $( ".date" ).datepicker({
               dateFormat: 'yy-mm-dd',
               defaultDate: "+0w",
               changeMonth: true,
               numberOfMonths: 1,
               onSelect: function( selectedDate )
               {
                  $('#'+$(this).attr('data-target')).val(selectedDate);
                  var date=new moment(selectedDate);
                  $(this).val(date.format('Do MMM, YYYY'));
               }
            });
         });
         function project_create_form_submit(){
            var final_string='';
            for(var domain in template){
               if(!template[domain].fields)
                  continue;

               var temp_string='';
               for(var field in template[domain].fields){
                  template[domain].fields[field].value=$('#'+template[domain].fields[field].id).val();
                  temp_string+='&'+template[domain].fields[field].id+'='+encodeURIComponent($('#'+template[domain].fields[field].id).val());
               }

               temp_string=temp_string.substring(1);
               temp_string='&'+domain+'='+encodeURIComponent(temp_string);
               final_string+=temp_string;
            }
            if(template.project_id)
               final_string='intent=edit'+final_string;
            else
               final_string='intent=create'+final_string;
            final_string+='&project_name='+encodeURIComponent($('#project_name').val());
            final_string+='&buyer_id='+encodeURIComponent($('#buyer_id').val());
            final_string+='&supplier_id='+encodeURIComponent($('#supplier_id').val());
            $.ajax({
               url: "<?php echo site_url().'projects/ajax';?>",
               method: 'POST',
               data: final_string,
               success: function(response){
                  if(response.status=='ok')
                     window.location='<?php echo site_url();?>projects/edit/'+response.project_id;
               }
            });
         }
         $(document).ready(function(){
            $('#project_create').unbind('click');
            $('#project_create').click(function(){
               project_create_form_submit();
            });
         });
      </script>
      <div style="clear:both;"></div>

   <!-- END -->
   <button style="float:right;margin-top:10px;" id="project_create" class="btn btn-default">Continue</button>
</div>
