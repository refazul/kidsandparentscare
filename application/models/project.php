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
                                            's_c_origin'=>$param['s_c_origin'],
                                            's_c_specification'=>$param['s_c_specification'],
                                            's_c_quantity'=>$param['s_c_quantity'],
                                            's_c_price'=>$param['s_c_price'],
                                            's_c_commission_rate'=>$param['s_c_commission_rate'],
                                            's_c_commission_point'=>$param['s_c_commission_point'],
                                            's_c_shipment'=>$param['s_c_shipment'],
                                            's_c_payment'=>$param['s_c_payment'],
                                            's_c_latest_date_of_lc_opening'=>$param['s_c_latest_date_of_lc_opening'],
                                            's_c_path'=>$param['s_c_path']
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
}
