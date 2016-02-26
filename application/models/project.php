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
                                            'nn_documents'=>$param['nn_documents'],
                                            'payment'=>$param['payment'],
                                            'controller'=>$param['controller'],
                                            'short_gain_weight_claim'=>$param['short_gain_weight_claim'],
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

      if($project['sales_confirmation']!='' && $project['sales_confirmation']!='0'){
         $merged=array();
         $existing=json_decode($project['sales_confirmation']);
         foreach($project['domains']->sales_confirmation->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->sales_confirmation=$merged;
      }

      if($project['contract']!='' && $project['contract']!='0'){
         $merged=array();
         $existing=json_decode($project['contract']);
         foreach($project['domains']->contract->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->contract=$merged;
      }

      if($project['performa_invoice']!='' && $project['performa_invoice']!='0'){
         $merged=array();
         $existing=json_decode($project['performa_invoice']);
         foreach($project['domains']->performa_invoice->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->performa_invoice=$merged;
      }

      if($project['import_permit']!='' && $project['import_permit']!='0'){
         $merged=array();
         $existing=json_decode($project['import_permit']);
         foreach($project['domains']->import_permit->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->import_permit=$merged;
      }

      if($project['lc']!='' && $project['lc']!='0'){
         $merged=array();
         $existing=json_decode($project['lc']);
         foreach($project['domains']->lc->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->lc->fields=$merged;
      }

      if($project['shipment']!='' && $project['shipment']!='0'){
         $merged=array();
         $existing=json_decode($project['shipment']);
         foreach($project['domains']->shipment->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->shipment=$merged;
      }

      if($project['nn_documents']!='' && $project['nn_documents']!='0'){
         $merged=array();
         $existing=json_decode($project['nn_documents']);
         foreach($project['domains']->nn_documents->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->nn_documents=$merged;
      }

      if($project['payment']!='' && $project['payment']!='0'){
         $merged=array();
         $existing=json_decode($project['payment']);
         foreach($project['domains']->payment->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->payment=$merged;
      }

      if($project['controller']!='' && $project['controller']!='0'){
         $merged=array();
         $existing=json_decode($project['controller']);
         foreach($project['domains']->controller->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->controller=$merged;
      }

      if($project['short_gain_weight_claim']!='' && $project['short_gain_weight_claim']!='0'){
         $merged=array();
         $existing=json_decode($project['short_gain_weight_claim']);
         foreach($project['domains']->short_gain_weight_claim->fields as $key=>$value){
            if(isset($existing->fields->$key))
               $merged[$key]=$existing->fields->$key;
            else
               $merged[$key]=$value;
         }
         $project['domains']->short_gain_weight_claim=$merged;
      }

      $project['template']=json_encode($project['domains']);
      //echo '<pre>';print_r($project);die();

      return $project;
   }
}
