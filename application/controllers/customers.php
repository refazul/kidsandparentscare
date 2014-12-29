<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * must     -       cuid,code,name,created_by
 * optional -       address,city,phone,cell,email,fax,description,created_on
 */

class Customers extends CI_Controller {
	
	public function index()
	{
            $this->all();
	}
        public function create()
        {
            if(user_logged_in() && user_can('CREATE_CUSTOMER'))
            {
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('customers/create');
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }        
        public function edit()
        {
            if(user_logged_in() && user_can('EDIT_CUSTOMER'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $cuid=$this->uri->segment(3);
                    $this->db->where('cuid',$cuid);
                    $customer=$this->db->get('customers')->row_array();

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('customers/edit',$customer);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');
                }
            }
        }
        public function miniedit()
        {
            if(user_logged_in() && user_can('EDIT_CUSTOMER'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $cuid=$this->uri->segment(3);
                    $this->db->where('cuid',$cuid);
                    $customer=$this->db->get('customers')->row_array();

                    $this->load->view('header');
                    $this->load->view('customers/edit',$customer);
                    $this->load->view('miniedit');
                    $this->load->view('footer');
                }
            }
        }
        public function fetch()
        {
            if(user_logged_in() && user_can('EDIT_CUSTOMER'))
            {
                header('Content-type: application/json');
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'cuid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, array('cuid','point','name','address','city','phone','cell','email')))
                {
                    $data['total']=$this->db->get('customers')->num_rows();
                    
                        /* search */
                        if(in_array($this->input->get_post('filter_by'),array('cuid','name','address','city','country','phone','cell','email')))
                        {
                            if($this->input->get_post('filter') && strlen($this->input->get_post('filter'))>0)
                            {
                                $this->db->order_by($sort_by, $order);
                                $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                                $data['total']=$this->db->get('customers')->num_rows();

                                $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                            }
                        }
						/*
                        else if(in_array($this->input->get_post('filter_by'),array('code')))
                        {
                            if(strlen($this->input->get_post('filter'))>0)
                            {
                                $this->db->order_by($sort_by, $order);
                                $this->db->where($this->input->get_post('filter_by'),$this->input->get_post('filter'));
                                $data['total']=$this->db->get('customers')->num_rows();                                

                                $this->db->where($this->input->get_post('filter_by'),$this->input->get_post('filter'));
                            }
                            else
                            {
                                $data['total']=0;                                
                                $this->db->where($this->input->get_post('filter_by'),'-1234567890');
                            }                            
                        }
                                                 * 
                                                 */
                    $this->db->order_by($sort_by, $order);
                    $data['results']=$this->db->get('customers',$limit,$limit*$page)->result_array();
                    $data['page']=$page;
                    $data['limit']=$limit;
                    $data['status']='ok';

                    foreach($data['results'] as $key=>$value)
                    {
						/*
                        $this->db->where('key','POINT_TO_CASH_RATIO');
                        $ratio=$this->db->get('config')->row(0,'object')->value;
                        
                        $data['results'][$key]['amount']=round($data['results'][$key]['points']/$ratio,2);
						*/
                    }
                    echo json_encode($data);
                    die();
                }
                else
                {
                    $data['status']='invalid_sort_by';
                    echo json_encode($data);
                    die();
                }
            }
            else
                echo '<h1>Bad Request</h1>';
            die();
        }
        public function all()
        {
            if(user_logged_in() && user_can('EDIT_CUSTOMER'))
            {                
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'cuid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('customers')))
                {
                    $data['fields']=array(
                        'cuid'=>array('Customer ID','10'),
                        'name'=>array('Customer Name','25'),
                        /*'point'=>array('Point','7','center'),*/
                        'address'=>array('Address','25'),
                        'city'=>array('City','5'),
                        'phone'=>array('Phone','10'),
                        'cell'=>array('Cell','10'),
                        'email'=>array('Email','15')                        
                    );
                    $data['search_fields']=array(
                        'name'=>'Customer Name',
                        'cuid'=>'Customer ID',
                        'address'=>'Address',
                        'city'=>'City',                        
                        'phone'=>'Phone',
                        'cell'=>'Cell',
                        'email'=>'Email'                        
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );
                    
                    $data['sort_by']='cuid';
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;                    

                    //echo '<pre>';print_r($data);echo '</pre>';die();          

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('customers/all',$data);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');                    
                }
            }
            else            
                echo '<h1>Bad Request</h1>';
        }
        public function ajax()
        {
            header('Content-type: application/json');
            if($this->input->get_post('intent'))
            {
                $intent=$this->input->get_post('intent');
                if($intent=='create' && user_logged_in() && user_can('CREATE_CUSTOMER'))
                {
                    if($this->input->post('customer_name') && $this->input->post('customer_code'))
                    {
                        $data['name']=$this->input->post('customer_name');
                        $data['code']=$this->input->post('customer_code');
                        
                        if(intval($data['code'])<10000000)
                        {
                            $json['status']='invalid_code';
                            echo json_encode($json);
                            die();
                        }
                        $this->db->where('code',$data['code']);
                        if($this->db->get('codepool')->num_rows()==0)
                        {
                            $json['status']='invalid_code';
                            echo json_encode($json);
                            die();
                        }
                        
                        
                        $this->load->model('Customer');
                        if($this->Customer->getBy('code',$data['code'])===0)
                        {
                            $data['address']=$this->input->post('customer_address')?$this->input->post('customer_address'):'';
                            $data['city']=$this->input->post('customer_city')?$this->input->post('customer_city'):'';
                            $data['phone']=$this->input->post('customer_phone')?$this->input->post('customer_phone'):'';
                            $data['cell']=$this->input->post('customer_cell')?$this->input->post('customer_cell'):'';
                            $data['email']=$this->input->post('customer_email')?$this->input->post('customer_email'):'';
                            $data['fax']=$this->input->post('customer_fax')?$this->input->post('customer_fax'):'';
                            $data['description']=$this->input->post('customer_description')?$this->input->post('customer_description'):'';

                            $cuid=$this->Customer->create($data);
                            if($cuid===false)
                            {
                                $json['status']='unknown_error';
                                echo json_encode($json);
                                die();
                            }

                            $json['cuid']=$cuid;
                            $json['status']='ok';
                            echo json_encode($json);
                            die();
                        }
                        else
                        {
                            $json['status']='already_exists';
                            echo json_encode($json);
                            die();
                        }
                    }
                    else
                    {
                        if(!$this->input->post('customer_name'))
                        {                            
                            $json['status']='no_name';
                            echo json_encode($json);
                            die();
                        }
                        if(!$this->input->post('customer_code'))
                        {                            
                            $json['status']='no_code';
                            echo json_encode($json);
                            die();
                        }
                    }
                }
                else if($intent=='edit' && user_logged_in() && user_can('EDIT_CUSTOMER'))
                {
                    $request_from=$_SERVER['HTTP_REFERER'];
                    $cuid=end(explode('/',$request_from));
                    
                    if(valid_integer($cuid))
                    {
                        if($this->input->post('customer_name'))
                        {
                            $name=$this->input->post('customer_name');
                            $data['name']=$name;
                            
                            $data['address']=$this->input->post('customer_address');
                            $data['city']=$this->input->post('customer_city');
                            $data['phone']=$this->input->post('customer_phone');
                            $data['cell']=$this->input->post('customer_cell');
                            $data['email']=$this->input->post('customer_email');
                            $data['fax']=$this->input->post('customer_fax');
                            $data['description']=$this->input->post('customer_description');
                            
                            $this->db->where('cuid',$cuid);
                            $this->db->update('customers',$data);

                            $json['status']='ok';
                            echo json_encode($json);
                        }
                        else
                        {
                            if(!$this->input->post('customer_name'))
                            {                            
                                $json['status']='no_name';
                                echo json_encode($json);
                                die();
                            }
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
        public function detect()
        {
            if(user_logged_in() && user_can('CREATE_INVOICE'))
            {
                if($this->input->post('filter_by')=='code')
                {
                    $code=$this->input->post('filter');
                    $this->load->model('customer');
                    $customer=$this->customer->getBy('code',$code);
                    if($customer===0 || $customer===false)
                    {
                        header('Content-type: application/json');
                        $json['status']='invalid';
                        echo json_encode($json);
                        die();
                    }
                    header('Content-type: application/json');
                    $json['status']='ok';
                    $json['customer']=$customer;
                    echo json_encode($json);
                    die();
                }
                header('Content-type: application/json');
                $json['status']='invalid';
                echo json_encode($json);
                die();
            }
        }
}

/* End of file customers.php */
/* Location: ./application/controllers/customers.php */