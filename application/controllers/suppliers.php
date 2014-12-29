<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * must     -       sid,name,active,created_by
 * optional -       created_on,description
 */

class Suppliers extends CI_Controller {
	
	public function index()
	{
            $this->all();
	}
        public function fetch()
        {
            if(user_logged_in())
            {
                header('Content-type: application/json');
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'sid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('suppliers')))
                {
                    $data['total']=$this->db->get('suppliers')->num_rows();
                    
                    /* search */
                    if(in_array($this->input->get_post('filter_by'),array('sid','name','address','city','country','phone','cell','email')))
                    {
                        $this->db->order_by($sort_by, $order);
                        $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                        $data['total']=$this->db->get('suppliers')->num_rows();

                        $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                    }
                    
                    $this->db->order_by($sort_by, $order);
                    $data['results']=$this->db->get('suppliers',$limit,$limit*$page)->result_array();                    
                    $data['page']=$page;
                    $data['limit']=$limit;
                    $data['status']='ok';

                    foreach($data['results'] as $key=>$value)
                    {
                        $this->db->where('sid',$data['results'][$key]['sid']);
                        $stocks=$this->db->get('stocks')->result_array();
                        
                        $data['results'][$key]['products']=0;
                        foreach($stocks as $stock)
                        {
                            $data['results'][$key]['products'] += $stock['quantity']+$stock['store_quantity'];
                        }
                        
                        if($data['results'][$key]['active']==0)
                            $data['results'][$key]['active']='No';
                        else
                            $data['results'][$key]['active']='Yes';
                    }
                }
                else                
                    $data['status']='invalid_sort_by';
                
                echo json_encode($data);
            }
            else
                echo '<h1>Bad Request</h1>';
            die();
        }
        public function all()
        {
            if(user_logged_in())
            {                
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'sid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('suppliers')))
                {
                    $data['fields']=array(
                        'sid'=>array('Supplier ID','8'),
                        'name'=>array('Supplier','12'),
                        'products'=>array('Number of Products','15','center'),
                        'address'=>array('Address','25'),
                        'city'=>array('City','5'),
                        'phone'=>array('Phone','10'),
                        'cell'=>array('Cell','10')
                    );
                    $data['search_fields']=array(
                        'name'=>'Supplier',
                        'sid'=>'Supplier ID',
                        'address'=>'Address',
                        'city'=>'City',
                        'country'=>'Country',
                        'phone'=>'Phone',
                        'cell'=>'Cell'
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );
                    
                    $data['sort_by']='sid';
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;                    

                    //echo '<pre>';print_r($data);echo '</pre>';die();          

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('suppliers/all',$data);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');                    
                }
            }
            else            
                echo '<h1>Bad Request</h1>';
        }
        public function create()
        {
            if(user_logged_in() && user_can('CREATE_SUPPLIER'))
            {
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('suppliers/create');
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }        
        public function edit()
        {
            if(user_logged_in() && user_can('EDIT_SUPPLIER'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $sid=$this->uri->segment(3);
                    $this->db->where('sid',$sid);
                    $supplier=$this->db->get('suppliers')->row_array();                

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('suppliers/edit',$supplier);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');
                }
            }
        }
        public function miniedit()
        {
            if(user_logged_in() && user_can('EDIT_SUPPLIER'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $sid=$this->uri->segment(3);
                    $this->db->where('sid',$sid);
                    $supplier=$this->db->get('suppliers')->row_array();                

                    $this->load->view('header');
                    $this->load->view('suppliers/edit',$supplier);
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
                if($intent=='create' && user_logged_in() && user_can('CREATE_SUPPLIER'))
                {                    
                    if($this->input->post('supplier_name'))
                    {
                        $name=strtolower($this->input->post('supplier_name'));
                        $this->db->where('name',$name);
                        if($this->db->get('suppliers')->num_rows()>0)
                        {                            
                            $json['status']='already_exists';
                            echo json_encode($json);
                        }
                        else
                        {                            
                            $data['name']=strtolower($this->input->post('supplier_name'));
                            $data['active']=$this->input->post('supplier_active')?$this->input->post('supplier_active'):1;
                            $data['created_by']=$this->session->userdata('uid');
                                                        
                            $data['address']=$this->input->post('supplier_address')?$this->input->post('supplier_address'):'';                            
                            $data['city']=$this->input->post('supplier_city')?$this->input->post('supplier_city'):'';                            
                            $data['country']=$this->input->post('supplier_country')?$this->input->post('supplier_country'):'';                            
                            $data['phone']=$this->input->post('supplier_phone')?$this->input->post('supplier_phone'):'';                            
                            $data['cell']=$this->input->post('supplier_cell')?$this->input->post('supplier_cell'):'';                            
                            $data['email']=$this->input->post('supplier_email')?$this->input->post('supplier_email'):'';                            
                            $data['fax']=$this->input->post('supplier_fax')?$this->input->post('supplier_fax'):'';                            
                            $data['description']=$this->input->post('supplier_description')?$this->input->post('supplier_description'):'';                            

                            $this->db->insert('suppliers',$data);
                            $sid=$this->db->insert_id();
                            
                            $json['sid']=$sid;
                            $json['status']='ok';
                            echo json_encode($json);
                        }
                    }
                    else
                    {
                        if(!$this->input->post('supplier_name'))
                        {                            
                            $json['status']='no_name';
                            echo json_encode($json);
                        }
                    }
                }
                else if($intent=='edit' && user_logged_in() && user_can('EDIT_SUPPLIER'))
                {
                    $request_from=$_SERVER['HTTP_REFERER'];
                    $sid=end(explode('/',$request_from));
                    
                    if(valid_integer($sid))
                    {
                        if($this->input->post('supplier_name'))
                        {
                            $name=$this->input->post('supplier_name');                            
                            $this->db->where('name',$name);
                            if($this->db->get('suppliers')->num_rows()>0)
                            {
                                $this->db->where('name',$name);
                                $db_sid=$this->db->get('suppliers')->row(0,'object')->sid;
                                if($sid!=$db_sid)
                                {
                                    $json['status']='already_exists';
                                    echo json_encode($json);
                                    die();
                                }                                
                            }
                            $data['name']=$name;
                            
                            if(!valid_yesno($this->input->post('supplier_active')))
                            {
                                $json['status']='invalid_value';
                                echo json_encode($json);
                                die();
                            }
                            $data['active']=$this->input->post('supplier_active');
                            $data['address']=$this->input->post('supplier_address');
                            $data['city']=$this->input->post('supplier_city');
                            $data['country']=$this->input->post('supplier_country');
                            $data['phone']=$this->input->post('supplier_phone');
                            $data['cell']=$this->input->post('supplier_cell');
                            $data['email']=$this->input->post('supplier_email');
                            $data['fax']=$this->input->post('supplier_fax');
                            $data['description']=$this->input->post('supplier_description');      
                            
                            $this->db->where('sid',$sid);
                            $this->db->update('suppliers',$data);

                            $json['status']='ok';
                            echo json_encode($json);
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

/* End of file suppliers.php */
/* Location: ./application/controllers/suppliers.php */