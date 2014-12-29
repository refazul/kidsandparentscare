<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * must     -       mid,name,active,created_by
 * optional -       created_on,description
 */

class Manufacturers extends CI_Controller {
	
	public function index()
	{
            $this->all();
	}
        public function create()
        {
            if(user_logged_in() && user_can('CREATE_MANUFACTURER'))
            {
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('manufacturers/create');
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }        
        public function edit()
        {
            if(user_logged_in() && user_can('EDIT_MANUFACTURER'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $mid=$this->uri->segment(3);
                    $this->db->where('mid',$mid);
                    $manufacturer=$this->db->get('manufacturers')->row_array();                

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('manufacturers/edit',$manufacturer);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');
                }
            }
        }
        public function miniedit()
        {
            if(user_logged_in() && user_can('EDIT_MANUFACTURER'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $mid=$this->uri->segment(3);
                    $this->db->where('mid',$mid);
                    $manufacturer=$this->db->get('manufacturers')->row_array();                

                    $this->load->view('header');
                    $this->load->view('manufacturers/edit',$manufacturer);
                    $this->load->view('miniedit');
                    $this->load->view('footer');
                }
            }
        }
        public function fetch()
        {
            if(user_logged_in())
            {
                header('Content-type: application/json');
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'mid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('manufacturers')))
                {
                    $data['total']=$this->db->get('manufacturers')->num_rows();
                    
                    if($this->input->get_post('filter') && strlen($this->input->get_post('filter'))>0 && in_array($this->input->get_post('filter_by'), $this->db->list_fields('manufacturers')))
                    {                        
                        /* search */
                        if(in_array($this->input->get_post('filter_by'),array('mid','name','description')))
                        {
                            $this->db->order_by($sort_by, $order);
                            $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                            $data['total']=$this->db->get('manufacturers')->num_rows();
                            
                            $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                        }                        
                    }
                    
                    $this->db->order_by($sort_by, $order);
                    $data['results']=$this->db->get('manufacturers',$limit,$limit*$page)->result_array();                    
                    $data['page']=$page;
                    $data['limit']=$limit;
                    $data['status']='ok';

                    foreach($data['results'] as $key=>$value)
                    {
                        if($data['results'][$key]['active']==0)
                            $data['results'][$key]['active']='No';
                        else
                            $data['results'][$key]['active']='Yes';

                        $created_by=$data['results'][$key]['created_by'];
                        $this->db->where('uid',$created_by);
                        $full_name=$this->db->get('users')->row(0,'object')->full_name;

                        $data['results'][$key]['created_by']=$full_name;
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
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'mid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('manufacturers')))
                {
                    $data['fields']=array(
                        'mid'=>array('Manufacturer ID','10'),
                        'name'=>array('Manufacturer','30'),
                        'active'=>array('Active','10'),
                        'created_on'=>array('Created On','15'),
                        'created_by'=>array('Created By','20'),
                        'description'=>array('Description','15')
                    );
                    $data['search_fields']=array(
						'name'=>'Manufacturer',
                        'mid'=>'Manufacturer ID',
                        'description'=>'Description'
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );
                    
                    $data['sort_by']='mid';
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;                    

                    //echo '<pre>';print_r($data);echo '</pre>';die();          

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('manufacturers/all',$data);
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
                if($intent=='create' && user_logged_in() && user_can('CREATE_MANUFACTURER'))
                {                    
                    if($this->input->post('manufacturer_name'))
                    {
                        $name=strtolower($this->input->post('manufacturer_name'));
                        $this->db->where('name',$name);
                        if($this->db->get('manufacturers')->num_rows()>0)
                        {                            
                            $json['status']='already_exists';
                            echo json_encode($json);
                        }
                        else
                        {                            
                            $data['name']=strtolower($this->input->post('manufacturer_name'));
                            $data['active']=$this->input->post('manufacturer_active')?$this->input->post('manufacturer_active'):1;
                            $data['created_by']=$this->session->userdata('uid');
                                                        
                            $data['description']=$this->input->post('manufacturer_description')?$this->input->post('manufacturer_description'):'';                            

                            $this->db->insert('manufacturers',$data);
                            $mid=$this->db->insert_id();
                            
                            $json['mid']=$mid;
                            $json['status']='ok';
                            echo json_encode($json);
                        }
                    }
                    else
                    {
                        if(!$this->input->post('manufacturer_name'))
                        {                            
                            $json['status']='no_name';
                            echo json_encode($json);
                        }
                    }
                }
                else if($intent=='edit' && user_logged_in() && user_can('EDIT_MANUFACTURER'))
                {
                    $request_from=$_SERVER['HTTP_REFERER'];
                    $mid=end(explode('/',$request_from));
                    
                    if(valid_integer($mid))
                    {
                        if($this->input->post('manufacturer_name'))
                        {
                            $name=$this->input->post('manufacturer_name');
                            $this->db->where('name',$name);
                            if($this->db->get('manufacturers')->num_rows()>0)
                            {
                                $this->db->where('name',$name);
                                $db_mid=$this->db->get('manufacturers')->row(0,'object')->mid;
                                if($mid!=$db_mid)
                                {
                                    $json['status']='already_exists';
                                    echo json_encode($json);
                                    die();
                                }                                
                            }
                            $data['name']=$name;
                            
                            if(!valid_yesno($this->input->post('manufacturer_active')))
                            {
                                $json['status']='invalid_value';
                                echo json_encode($json);
                                die();
                            }
                            $data['active']=$this->input->post('manufacturer_active');
                            $data['description']=$this->input->post('manufacturer_description');
                            
                            $this->db->where('mid',$mid);
                            $this->db->update('manufacturers',$data);

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

/* End of file manufacturers.php */
/* Location: ./application/controllers/manufacturers.php */