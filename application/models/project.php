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
      $this->db->insert($this->_table,array('name'=>$param['name'],'buyer_id'=>$param['buyer_id'],'supplier_id'=>$param['supplier_id']));
      $id=$this->db->insert_id();

      return $id;
   }
}
