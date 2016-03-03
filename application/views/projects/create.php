<div class='navigation'>
<?php foreach($domains as $root=>$domain): if(!isset($domain->fields))continue;?>
   <div class='anchor' data-target='<?php echo $root?>' title='<?php echo $domain->title;?>'>
      <?php echo $domain->title;?>
   </div>
<?php endforeach; ?>
</div>
<div class='project' style="width:550px;clear:both;margin:10px auto 20px;">

   <?php

   $form=array(
      'id'=>'project_create_form',
      'value_width'=>280,
      'value_height'=>28,
      'action'=>site_url().'projects/ajax'
   );

   ?>
   <script type='text/javascript'>
      var template=JSON.parse('<?php echo $template;?>');
   </script>

   <!-- START -->

      <?php foreach($domains as $root=>$domain): if(!isset($domain->fields))continue; $fields=$domain->fields; $title=$domain->title;?>
      <div id='<?php echo $root;?>' style='border: 2px dashed #ccc; padding: 10px;margin-bottom: 10px;'>
         <div class='title'><?php echo $title; ?></div>
         <?php foreach($fields as $field):?>

            <?php if($field->type=='text' || $field->type=='number'): ?>
            <div class="part" id="<?php echo $field->id;?>-wrapper">
               <div class="field"><?php echo $field->title;?></div>
               <div class="seperator"></div>
               <div class="value" style="width:<?php echo $form['value_width'];?>px;height:<?php echo $form['value_height'];?>px;">
                  <input type="text" <?php if(isset($field->genre) && $field->genre=='calculative')echo 'disabled'?> id="<?php echo $field->id;?>" name="<?php echo $field->id;?>" autocomplete="off" class="form-control <?php if($field->type=='text')echo 'text-box';?>" value="<?php echo $field->value?$field->value:'';?>"/>
                  <?php if($field->type=='number'): ?>
                     <script type='text/javascript'>
                        $('#'+'<?php echo $field->id;?>').on('input',function(){
                           var new_value=$(this).val().replace(/[^0-9. ,\-\/]/g,'');
                           var focus=$(this).getCursorPosition();
                           $(this).val(new_value);
                           $(this).focus();
                           $(this).selectRange(focus);
                        });
                     </script>
                  <?php endif; ?>
                  <?php if($field->type=='text'): ?>
                     <script type='text/javascript'>
                        $('#'+'<?php echo $field->id;?>').on('input',function(){
                           var new_value=$(this).val().toUpperCase();
                           var focus=$(this).getCursorPosition();
                           $(this).val(new_value);
                           $(this).focus();
                           $(this).selectRange(focus);
                        });
                        $('#'+'<?php echo $field->id;?>').val(decodeURIComponent(decodeURIComponent($('#'+'<?php echo $field->id;?>').val())));
                     </script>
                  <?php endif; ?>

                  <?php if(isset($field->unit)): ?>
                  <div class="select-wrap select-wrap-unit" style="height:100%;">
                     <select id="<?php echo $field->id;?>_unit" name="<?php echo $field->id;?>_unit" style="height:100%;">
                         <?php foreach($field->unit->values as $value=>$key):?>
                         <option <?php if($value==$field->unit->value)echo 'selected="selected"'?> value="<?php echo $value;?>"><?php echo $key;?></option>
                         <?php endforeach;?>
                     </select>
                  </div>
                  <?php endif ?>
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
                  <input data-target='<?php echo $field->id;?>' type="text" autocomplete="off" <?php if(isset($field->genre) && $field->genre=='calculative')echo 'disabled'?> class="form-control date <?php if(isset($field->genre) && $field->genre=='calculative')echo 'disabled'?>" value="<?php echo $field->value?$field->value:'';?>"/>
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
                     'DEFAULT_IMG'=>asset_url().'images/file-upload-2.png',
                     'IMG'=>NULL,
                     'VALUE'=>$field->value,
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
            $( ".date" ).not('.disabled').datepicker({
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
                  if(template[domain].fields.genre && template[domain].fields.genre=='calculative'){
                     template[domain].fields[field].value='';
                  }
                  else{
                     template[domain].fields[field].value=$('#'+template[domain].fields[field].id).val();
                  }
                  temp_string+='&'+template[domain].fields[field].id+'='+encodeURIComponent($('#'+template[domain].fields[field].id).val());
                  if(template[domain].fields[field].unit){
                     temp_string+='&'+template[domain].fields[field].id+'_unit='+encodeURIComponent($('#'+template[domain].fields[field].id+'_unit').val());
                  }
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

            $('.anchor').unbind('click');
            $('.anchor').click(function(){
               var target=$(this).attr('data-target');
               location.hash='#'+target;
            });
         });
      </script>
      <div style="clear:both;"></div>

   <!-- END -->
   <button style="position:fixed;z-index:100;bottom: 0px;right: 20px;padding: 10px 30px;" id="project_create" class="btn btn-default">Continue</button>
</div>
