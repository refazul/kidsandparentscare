<?php

class Customer extends CI_Model {

    var $_pk;
    var $_table;
    var $_model;
    var $code = '';
    var $name   = '';
    var $address   = '';
    var $city   = '';
    var $phone   = '';
    var $cell   = '';
    var $email   = '';
    var $fax   = '';
    var $description   = '';
    var $created_on   = '';
    var $created_by   = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->_pk='cuid';
        $this->_table='customers';
        $this->_model='Customer';
    }
    public function exists($pk)
    {
        $this->db->where($this->_pk,$pk);
        if($this->db->get($this->_table)->num_rows()>0)
            return true;
        return false;
    }
    public function __call($method,$args)
    {
        switch (substr($method, 0, 3)) {
            case 'get' :
                
                $key = strtolower(substr($method,3));
                if($this->exists($args[0]))
                {
                    $this->db->where($this->_pk,$args[0]);
                    $data=$this->db->get($this->_table)->row(0,'object');
                    if(property_exists($data, $key))
                        return $data->$key;
                    return false;
                }
                return false;
                
            case 'set' :
                
                if($this->exists($args[0]))
                {
                    $key = strtolower(substr($method,3));
                    $this->db->where($this->_pk,$args[0]);
                    $data=$this->db->get($this->_table)->row(0,'object');
                    if(property_exists($data, $key))
                    {
                        $this->db->where($this->_pk,$args[0]);
                        $data=$this->db->update($this->_table,array($key=>$args[1]));
                        return $data;
                    }
                    return false;
                }
                return false;
        }
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
        return 0;
    }
    
    public function setCode()
    {
        return false;
    }
    public function setPoint()
    {
        return false;
    }
    public function addPoint($cuid,$point)
    {
        if($cuid==0)
            return false;
        if($this->exists($cuid))
        {
            $current=$this->getPoint($cuid);
            $new=$current+$point;
            $this->db->where($this->_pk,$cuid);
            return $this->db->update($this->_table,array('point'=>$new));
        }
        return false;
    }
    public function create($param)
    {
        if(isset($param['code']) && strlen($param['code'])>=8)
        {
            if($this->getBy('code', $param['code'])===0)
            {
                $this->db->insert($this->_table,array('code'=>$param['code']));
                $id=$this->db->insert_id();
                
                unset($param['code']);
                date_default_timezone_set('Asia/Dhaka');
                $param['point']=0;
                $param['created_on']=date('Y-m-d H:i:s');
                $param['created_by']=$this->session->userdata('uid');
                $this->load->model($this->_model);
                
                foreach($param as $k=>$v)
                {
                    $method='set'.$k;
                    $this->Customer->$method($id,$v);
                }
                return $id;
            }
        }
        return false;
    }
}