<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * must     -       did,name,active,created_by
 * optional -       created_on,description
 */

class Departments extends CI_Controller {
	
	public function index()
	{
            $this->all();
	}
        public function fetch()
        {
            if(user_logged_in())
            {
                header('Content-type: application/json');
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'did';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('departments')))
                {
                    $data['total']=$this->db->get('departments')->num_rows();
                    
                    /* search */
                    if(in_array($this->input->get_post('filter_by'),array('did','name','description')))
                    {
                        $this->db->order_by($sort_by, $order);
                        $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                        $data['total']=$this->db->get('departments')->num_rows();

                        $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                    }
                    else if(in_array($this->input->get_post('filter_by'),array('active')))
                    {
                        $this->db->order_by($sort_by, $order);
                        $this->db->where($this->input->get_post('filter_by'),$this->input->get_post('filter'));
                        $data['total']=$this->db->get('departments')->num_rows();

                        $this->db->where($this->input->get_post('filter_by'),$this->input->get_post('filter'));
                    }
                    
                    $this->db->order_by($sort_by, $order);
                    $data['results']=$this->db->get('departments',$limit,$limit*$page)->result_array();                    
                    $data['page']=$page;
                    $data['limit']=$limit;
                    $data['status']='ok';

                    foreach($data['results'] as $key=>$value)
                    {
                        $this->db->where('department',$data['results'][$key]['did']);
                        $data['results'][$key]['products']=$this->db->get('products')->num_rows();
                        
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
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'did';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('departments')))
                {
                    $data['fields']=array(
                        'did'=>array('Department ID','10'),
                        'name'=>array('Department','15'),
                        'active'=>array('Active','10','center'),
                        'products'=>array('Number of Products','15','center'),
                        'created_on'=>array('Created On','15'),
                        'created_by'=>array('Created By','15'),
                        'description'=>array('Description','10')
                    );
                    $data['search_fields']=array(
                        'name'=>'Department',
                        'did'=>'Department ID',
                        'description'=>'Description',
                        'active'=>'Active'
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );
                    $data['active']=array(
                        '1'=>'Yes',
                        '0'=>'No'
                    );
                    
                    $data['sort_by']='did';
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;

                    //echo '<pre>';print_r($data);echo '</pre>';die();          

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('departments/all',$data);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');                    
                }
            }
            else            
                echo '<h1>Bad Request</h1>';
        }
        public function create()
        {
            if(user_logged_in() && user_can('CREATE_DEPARTMENT'))
            {
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('departments/create');
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }        
        public function edit()
        {
            if(user_logged_in() && user_can('EDIT_DEPARTMENT'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $did=$this->uri->segment(3);
                    $this->db->where('did',$did);
                    $department=$this->db->get('departments')->row_array();                

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('departments/edit',$department);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');
                }
            }
        }
        public function miniedit()
        {
            if(user_logged_in() && user_can('EDIT_DEPARTMENT'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $did=$this->uri->segment(3);
                    $this->db->where('did',$did);
                    $department=$this->db->get('departments')->row_array();                    
                    
                    $this->load->view('header');
                    $this->load->view('departments/edit',$department);
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
                if($intent=='create' && user_logged_in() && user_can('CREATE_DEPARTMENT'))
                {
                    if($this->input->post('department_name'))
                    {
                        $name=strtolower($this->input->post('department_name'));
                        $this->db->where('name',$name);
                        if($this->db->get('departments')->num_rows()>0)
                        {                            
                            $json['status']='already_exists';
                            echo json_encode($json);
                        }
                        else
                        {                            
                            $data['name']=strtolower($this->input->post('department_name'));
                            $data['active']=$this->input->post('department_active')?$this->input->post('department_active'):1;
                            $data['created_by']=$this->session->userdata('uid');
                                                        
                            $data['description']=$this->input->post('department_description')?$this->input->post('department_description'):'';                            

                            $this->db->insert('departments',$data);
                            $did=$this->db->insert_id();
                            
                            $json['did']=$did;
                            $json['status']='ok';
                            echo json_encode($json);
                        }
                    }
                    else
                    {
                        if(!$this->input->post('department_name'))
                        {                            
                            $json['status']='no_name';
                            echo json_encode($json);
                        }
                    }
                }
                else if($intent=='edit' && user_logged_in() && user_can('EDIT_DEPARTMENT'))
                {
                    $request_from=$_SERVER['HTTP_REFERER'];
                    $did=end(explode('/',$request_from));
                    
                    if(valid_integer($did))
                    {                        
                        if($this->input->post('department_name'))
                        {
                            $name=$this->input->post('department_name');                            
                            $this->db->where('name',$name);
                            if($this->db->get('departments')->num_rows()>0)
                            {
                                $this->db->where('name',$name);
                                $db_did=$this->db->get('departments')->row(0,'object')->did;
                                if($did!=$db_did)
                                {
                                    $json['status']='already_exists';
                                    echo json_encode($json);
                                    die();
                                }                                
                            }
                            $data['name']=$name;                            
                            
                            if(!valid_yesno($this->input->post('department_active')))
                            {
                                $json['status']='invalid_value';
                                echo json_encode($json);
                                die();
                            }                            
                            
                            $data['active']=$this->input->post('department_active');
                            $data['description']=$this->input->post('department_description');                            
                            
                            $this->db->where('did',$did);
                            $this->db->update('departments',$data);

                            $json['status']='ok';
                            echo json_encode($json);
                        }
                    }
                    else
                    {
                        $json['status']='check_url';
                        echo json_encode($json);                        
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

/* End of file departments.php */
/* Location: ./application/controllers/departments.php */