<?php

class Project extends CI_Model {

   var $_pk;
   var $_table;
   var $_model;

   function __construct()
   {
      // Call the Model constructor
      parent::__construct();
      $this->_pk='project_id';
      $this->_table='projects';
      $this->_model='project';
   }
   public function getBy($key,$value,$singular=true)
   {
      if(!in_array($key,$this->db->list_fields($this->_table)))
         return false;
      $this->db->where($key,$value);
      if($this->db->get($this->_table)->num_rows()>0)
      {
         $this->db->where($key,$value);
         if($singular)
             return $this->db->get($this->_table)->row_array();
         else
             return $this->db->get($this->_table)->result_array();
      }
      return false;
   }
   public function create($param)
   {
      $this->db->insert($this->_table,array('name'=>$param['name'],
                                            'buyer_id'=>$param['buyer_id'],
                                            'supplier_id'=>$param['supplier_id'],
                                            'sales_confirmation'=>$param['sales_confirmation'],
                                            'contract'=>$param['contract'],
                                            'performa_invoice'=>$param['performa_invoice'],
                                            'import_permit'=>$param['import_permit'],
                                            'lc'=>$param['lc'],
                                            'shipment'=>$param['shipment'],
                                            'nn_documents'=>$param['nn_documents'],
                                            'payment'=>$param['payment'],
                                            'controller'=>$param['controller'],
                                            'short_gain_weight_claim'=>$param['short_gain_weight_claim'],
                                            'quality_claim'=>$param['quality_claim'],
                                            'debit_note'=>$param['debit_note'],
                                            'carrying_charge'=>$param['carrying_charge'],
                                            'lc_amendment_charge'=>$param['lc_amendment_charge']
                                         ));
      $id=$this->db->insert_id();

      return $id;
   }
   public function process($data){
      foreach ($data['results'] as $key => $value) {
         $this->db->select('name');
         $this->db->where('buyer_id',$data['results'][$key]['buyer_id']);
         $data['results'][$key]['buyer']=$this->db->get('buyers')->row()->name;

         $this->db->select('name');
         $this->db->where('supplier_id',$data['results'][$key]['supplier_id']);
         $data['results'][$key]['supplier']=$this->db->get('suppliers')->row()->name;
      }
      return $data;
   }
   public function fetch($project_id){
      $this->db->select('*');
      $this->db->from('projects');
      $this->db->where('project_id',$project_id);
      $project=$this->db->get()->row_array();

      unset($temp);
      $buyers=array();
      $temp=$this->db->get('buyers')->result_array();
      foreach($temp as $key=>$value)
          $buyers[$value['buyer_id']]=$value['name'];

      unset($temp);
      $suppliers=array();
      $temp=$this->db->get('suppliers')->result_array();
      foreach($temp as $key=>$value)
          $suppliers[$value['supplier_id']]=$value['name'];

      $project['domains']=json_decode(file_get_contents(FCPATH.'assets'.DIRECTORY_SEPARATOR.'template.json'));
      //$project['template']=preg_replace("/[\n\r]/","",file_get_contents(FCPATH.'assets'.DIRECTORY_SEPARATOR.'template.json'));

      $project['domains']->project_id=$project['project_id'];
      $project['domains']->project->fields->project_name->value=$project['name'];

      $project['domains']->buyer->fields->project_buyer->values=$buyers;
      $project['domains']->buyer->fields->project_buyer->value=$project['buyer_id'];

      $project['domains']->supplier->fields->project_supplier->values=$suppliers;
      $project['domains']->supplier->fields->project_supplier->value=$project['supplier_id'];

      $fields=array('sales_confirmation','contract','performa_invoice','import_permit','lc','shipment','nn_documents','payment','controller','short_gain_weight_claim','quality_claim','debit_note','carrying_charge','lc_amendment_charge');
      foreach($fields as $field){
         if($project[$field]!='' && $project[$field]!='0'){
            $existing=explode('&',$project[$field]);
            foreach($project['domains']->$field->fields as $key=>$value){
               foreach($existing as $e){
                  $parts=explode('=',$e);
                  if($value->id==$parts[0]){
                     if(isset($value->genre) && $value->genre=='calculative'){
                        $value->value=$this->calculate($value->id,$project);
                     }
                     else
                        $value->value=urldecode($parts[1]);
                  }
                  if(isset($value->unit) && $value->id.'_unit'==$parts[0]){
                     $value->unit->value=urldecode($parts[1]);
                  }
               }
            }
         }
      }

      $project['template']=json_encode($project['domains']);
      //echo '<pre>';print_r($project);die();

      return $project;
   }
   public function calculate($field,$project){
      date_default_timezone_set('UTC');
      if($field=='payment_maturity_due_date'){
         $shipment_actual_shipment_date=$this->extract('shipment_actual_shipment_date',$project);
         return date('Y-m-d', strtotime($shipment_actual_shipment_date. ' + 180 days'));;
      }
      else if($field=='i_p_expiry_date'){
         $i_p_date=$this->extract('i_p_date',$project);
         return date('Y-m-d', strtotime($i_p_date. ' + 90 days'));;
      }
      else if($field=='controller_short_gain_weight'){
         // Controller.Invoice_Weight - Controller.Landing_Weight

         return $this->calculate_short_gain_weight_claim_qty_lbs($project).' LBS';
      }
      else if($field=='s_g_w_c_short_gain_weight_claim_qty'){
         // Controller.Invoice_Weight - Controller.Landing_Weight

         return $this->calculate_short_gain_weight_claim_qty_lbs($project).' LBS';
      }
      else if($field=='s_g_w_c_short_gain_weight_claim_amount'){
         // (Controller.Invoice_Weight - Controller.Landing_Weight) * Proforma_Invoice.price

         return $this->calculate_short_gain_weight_claim_amount_usd($project).' USD';
      }
      else if($field=='debit_note_amount'){

         return $this->calculate_debit_amount_usd($project).' USD';
      }
   }
   public function extract($f,$project){
      $fields=array('sales_confirmation','contract','performa_invoice','import_permit','lc','shipment','nn_documents','payment','controller','short_gain_weight_claim','quality_claim','debit_note','carrying_charge','lc_amendment_charge');
      foreach($fields as $field){
         if($project[$field]!='' && $project[$field]!='0'){
            foreach($project['domains']->$field->fields as $key=>$value){
               if($value->id==$f)
                  return $value->value;
               if(isset($value->unit) && $value->id.'_unit'==$f)
                  return $value->unit->value;
            }
         }
      }
   }
   public function convert_to_lbs($base_unit,$base_value){
      if($base_unit=='mt')
         return $base_value * 2204.60;
      else if($base_unit=='kg')
         return $base_value/1000 * 2204.60;
      else if($base_unit=='lbs')
         return $base_value;
   }
   public function calculate_short_gain_weight_claim_qty_lbs($project){
      // Controller.Invoice_Weight - Controller.Landing_Weight

      // Controller.Invoice_Weight
      $controller_invoice_weight=$this->extract('controller_invoice_weight',$project);
      $controller_invoice_weight_unit=$this->extract('controller_invoice_weight_unit',$project);

      // Controller.Landing_Weight
      $controller_landing_weight=$this->extract('controller_landing_weight',$project);
      $controller_landing_weight_unit=$this->extract('controller_landing_weight_unit',$project);

      // Conversion
      $controller_invoice_weight=$this->convert_to_lbs($controller_invoice_weight_unit,$controller_invoice_weight);
      $controller_landing_weight=$this->convert_to_lbs($controller_landing_weight_unit,$controller_landing_weight);

      // Return in lbs
      return ($controller_invoice_weight - $controller_landing_weight);
   }
   public function calculate_short_gain_weight_claim_amount_usd($project){
      // (Controller.Invoice_Weight - Controller.Landing_Weight) * Proforma_Invoice.Price

      $s_g_w_c_short_gain_weight_claim_qty=$this->calculate_short_gain_weight_claim_qty_lbs($project);

      $s_c_price=$this->extract('s_c_price',$project);
      if($s_c_price=='')$s_c_price=0;

      $s_c_price_unit=$this->extract('s_c_price_unit',$project);
      if($s_c_price_unit=='usc')
         return $s_g_w_c_short_gain_weight_claim_qty * $s_c_price/100;
      if($s_c_price_unit=='usd')
         return $s_g_w_c_short_gain_weight_claim_qty * $s_c_price;
   }
   public function calculate_point_value($project){

      // Extract
      $s_c_quantity=$this->extract('s_c_quantity',$project);
      $s_c_quantity_unit=$this->extract('s_c_quantity_unit',$project);
      $s_c_commission_point=$this->extract('s_c_commission_point',$project);

      // Conversion
      $s_c_quantity=$this->convert_to_lbs($s_c_quantity_unit,$s_c_quantity);
      $s_c_quantity_unit='lbs';
      if($s_c_commission_point=='')$s_c_commission_point=0;

      // Return
      return ($s_c_quantity * $s_c_commission_point) / 10000;
   }
   public function calculate_debit_amount_usd($project){

      // Extract
      $s_c_price=$this->extract('s_c_price',$project);
      $s_c_price_unit=$this->extract('s_c_price_unit',$project);
      $p_i_quantity=$this->extract('p_i_quantity',$project);
      $p_i_quantity_unit=$this->extract('p_i_quantity_unit',$project);
      $s_c_commission_rate=$this->extract('s_c_commission_rate',$project);

      // Conversion
      $p_i_quantity=$this->convert_to_lbs($p_i_quantity_unit,$p_i_quantity);
      $p_i_quantity_unit='lbs';
      if($s_c_price_unit=='usc'){
         $s_c_price=$s_c_price/100;
         $s_c_price_unit='usd';
      }
      if($s_c_commission_rate=='')$s_c_commission_rate=0;

      // Return
      return ($p_i_quantity * $s_c_price * $s_c_commission_rate/100) + $this->calculate_point_value($project);
   }
}
