<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * must     -       pid,barcode,name,unit
 * optional -       created_on,department,image
 */

class Stocks extends CI_Controller {
	
	public function index()
	{
            die();
	}
        
        public function edit()
        {
            if(user_logged_in() && user_can('EDIT_STOCK'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $stid=$this->uri->segment(3);
                    $this->db->where('stid',$stid);
                    $stock=$this->db->get('stocks')->row_array();

                    unset($temp);                
                    $temp=$this->db->get('suppliers')->result_array();
                    foreach($temp as $key=>$value)                
                        $stock['suppliers'][$value['sid']]=$value['name'];

                    //echo '<pre>';print_r($stock);echo '</pre>';die();

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('stocks/edit',$stock);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');
                }
            }
        }
        public function miniedit()
        {
            if(user_logged_in() && user_can('EDIT_STOCK'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $stid=$this->uri->segment(3);
                    $this->db->where('stid',$stid);
                    $stock=$this->db->get('stocks')->row_array();

                    unset($temp);                
                    $temp=$this->db->get('suppliers')->result_array();
                    foreach($temp as $key=>$value)                
                        $stock['suppliers'][$value['sid']]=$value['name'];

                    //echo '<pre>';print_r($stock);echo '</pre>';die();

                    $this->load->view('header');                
                    $this->load->view('stocks/edit',$stock);
                    $this->load->view('miniedit');
                    $this->load->view('footer');
                }
            }
        }        
        public function ajax()
        {
            header('Content-type: application/json');
            if($this->input->get_post('intent'))
            {
                $intent=$this->input->get_post('intent');
                if($intent=='create' && user_logged_in() && user_can('CREATE_STOCK'))
                {                    
                    if($this->input->post('stock_pid') && $this->input->post('stock_buy') && $this->input->post('stock_sell') && $this->input->post('stock_quantity'))
                    {
                        if(!valid_integer($this->input->post('stock_pid')))
                        {
                            $json['status']='invalid_product_id';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_numeric($this->input->post('stock_buy')))
                        {
                            $json['status']='invalid_buy';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_numeric($this->input->post('stock_sell')))
                        {
                            $json['status']='invalid_sell';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_integer($this->input->post('stock_quantity')))
                        {
                            $json['status']='invalid_quantity';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_supplier($this->input->post('stock_supplier')))
                        {
                            $json['status']='invalid_supplier';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_numeric($this->input->post('stock_discount_amount')))
                        {
                            $json['status']='invalid_discount_amount';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_absolutepercent($this->input->post('stock_discount_type')))
                        {
                            $json['status']='invalid_discount_type';
                            echo json_encode($json);
                            die();
                        }
                        
                        $data['unit_cost']=$this->input->post('stock_buy');
                        $data['unit_sale']=$this->input->post('stock_sell');                        
                        $data['quantity']=$this->input->post('stock_quantity');
                        $data['store_quantity']=$this->input->post('stock_store_quantity');
                        $data['base_quantity']=$data['quantity']+$data['store_quantity'];

                        $data['pid']=$this->input->post('stock_pid');
                        $data['sid']=$this->input->post('stock_supplier');
                        
                        $data['discount_amount']=$this->input->post('stock_discount_amount');
                        $data['discount_type']=$this->input->post('stock_discount_type');
                        date_default_timezone_set('Asia/Dhaka');
                        $data['stocked_on']=date('Y-m-d H:i:s');
                        
                        
                        $this->db->insert('stocks',$data);
                        $this->db->insert_id();
                        
                        /* ALL STOCK SELL PRICE AND DISCOUNT RULES MUST BE CONSISTENT */
                        $pid=$this->input->post('stock_pid');
                        
                        $all['unit_sale']=$data['unit_sale'];
                        $all['discount_amount']=$data['discount_amount'];
                        $all['discount_type']=$data['discount_type'];
                        
                        $this->db->where('pid',$pid);
                        $this->db->update('stocks',$all);
                        
                        $json['status']='ok';
                        echo json_encode($json);
                    }
                    else
                    {
                        if(!$this->input->post('stock_buy'))
                        {                            
                            $json['status']='no_buy';
                            echo json_encode($json);
                            die();
                        }
                        else if(!$this->input->post('stock_sell'))
                        {                            
                            $json['status']='no_sell';
                            echo json_encode($json);
                            die();
                        }
                        else if(!$this->input->post('stock_quantity'))
                        {                            
                            $json['status']='no_quantity';
                            echo json_encode($json);
                            die();
                        }                        
                    }
                }
                else if($intent=='edit' && user_logged_in() && user_can('EDIT_STOCK'))
                {
                    $request_from=$_SERVER['HTTP_REFERER'];
                    $stid=end(explode('/',$request_from));
                    
                    if(valid_integer($stid) && $this->input->post('stock_buy') && $this->input->post('stock_sell'))
                    {
                        
                        
                        
                        /* Check for validity */
                        if(!valid_numeric($this->input->post('stock_buy')))
                        {
                            $json['status']='invalid_buy';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_numeric($this->input->post('stock_sell')))
                        {
                            $json['status']='invalid_sell';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_supplier($this->input->post('stock_supplier')))
                        {
                            $json['status']='invalid_supplier';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_numeric($this->input->post('stock_discount_amount')))
                        {
                            $json['status']='invalid_discount_amount';
                            echo json_encode($json);
                            die();
                        }
                        if(!valid_absolutepercent($this->input->post('stock_discount_type')))
                        {
                            $json['status']='invalid_discount_type';
                            echo json_encode($json);
                            die();
                        }
                        
                        
                        
                        /* Collect & Process */
                        $data['unit_cost']=$this->input->post('stock_buy');
                        $data['unit_sale']=$this->input->post('stock_sell');
                        $data['sid']=$this->input->post('stock_supplier');
                        $data['quantity']=$this->input->post('stock_quantity');
                        $data['store_quantity']=$this->input->post('stock_store_quantity');
                        $data['base_quantity']=$data['quantity']+$data['store_quantity'];
                        $data['discount_amount']=$this->input->post('stock_discount_amount')?$this->input->post('stock_discount_amount'):'0';
                        $data['discount_type']=$this->input->post('stock_discount_type')?$this->input->post('stock_discount_type'):'absolute';
                        
                        
                        
                        /* Update */
                        $this->db->where('stid',$stid);
                        $this->db->update('stocks',$data);
                        
                        
                        
                        /* Make Consistent */
                        $pid=$this->input->post('stock_pid');
                        $all['unit_sale']=$data['unit_sale'];
                        $all['discount_amount']=$data['discount_amount'];
                        $all['discount_type']=$data['discount_type'];
                        $this->db->where('pid',$pid);
                        $this->db->update('stocks',$all);
                        
                        
                        
                        /* Reply Positively */
                        $json['status']='ok';                        
                        echo json_encode($json);
                        die();
                        
                        
                        
                    }
                    else
                    {
                        
                        
                        
                        if(!$this->input->post('stock_buy'))
                        {                            
                            $json['status']='no_buy';
                            echo json_encode($json);
                            die();
                        }
                        else if(!$this->input->post('stock_sell'))
                        {                            
                            $json['status']='no_sell';
                            echo json_encode($json);
                            die();
                        }
                        else if(!$this->input->post('stock_quantity'))
                        {                            
                            $json['status']='no_quantity';
                            echo json_encode($json);
                            die();
                        }
                        
                        
                        
                    }
                }
                else
                {
                    $json['status']='unauthorized_access';
                    echo json_encode($json);
                }
            }
            else
            {
                $json['status']='no_intent';
                echo json_encode($json);
            }
            die();
        }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */