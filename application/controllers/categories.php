<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * must     -       cid,name,active,created_by
 * optional -       created_on,description
 */

class Categories extends CI_Controller {
	
	public function index()
	{
            $this->all();
	}
        public function fetch()
        {
            if(user_logged_in())
            {
                header('Content-type: application/json');
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'cid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('categories')))
                {
                    $data['total']=$this->db->get('categories')->num_rows();
                    
                    /* search */
                    if(in_array($this->input->get_post('filter_by'),array('cid','name','description')))
                    {
                        $this->db->order_by($sort_by, $order);
                        $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                        $data['total']=$this->db->get('categories')->num_rows();

                        $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                    }
                    else if(in_array($this->input->get_post('filter_by'),array('active')))
                    {
                        $this->db->order_by($sort_by, $order);
                        $this->db->where($this->input->get_post('filter_by'),$this->input->get_post('filter'));
                        $data['total']=$this->db->get('categories')->num_rows();

                        $this->db->where($this->input->get_post('filter_by'),$this->input->get_post('filter'));
                    }
                    
                    $this->db->order_by($sort_by, $order);
                    $data['results']=$this->db->get('categories',$limit,$limit*$page)->result_array();                    
                    $data['page']=$page;
                    $data['limit']=$limit;
                    $data['status']='ok';

                    foreach($data['results'] as $key=>$value)
                    {
                        $this->db->where('category',$data['results'][$key]['cid']);
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
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'cid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('categories')))
                {
                    $data['fields']=array(
                        'cid'=>array('Category ID','10'),
                        'name'=>array('Category','15'),
                        'active'=>array('Active','10','center'),
                        'products'=>array('Number of Products','15','center'),
                        'created_on'=>array('Created On','15'),
                        'created_by'=>array('Created By','15'),
                        'description'=>array('Description','10')
                    );
                    $data['search_fields']=array(
                        'name'=>'Category',
                        'cid'=>'Category ID',
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
                    
                    $data['sort_by']='cid';
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;                    

                    //echo '<pre>';print_r($data);echo '</pre>';die();          

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('categories/all',$data);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');                    
                }
            }
            else            
                echo '<h1>Bad Request</h1>';
        }
        public function create()
        {
            if(user_logged_in() && user_can('CREATE_CATEGORY'))
            {
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('categories/create');
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }        
        public function edit()
        {
            if(user_logged_in() && user_can('EDIT_CATEGORY'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $cid=$this->uri->segment(3);
                    $this->db->where('cid',$cid);
                    $category=$this->db->get('categories')->row_array();                

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('categories/edit',$category);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');
                }
            }
        }
        public function miniedit()
        {
            if(user_logged_in() && user_can('EDIT_CATEGORY'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $cid=$this->uri->segment(3);
                    $this->db->where('cid',$cid);
                    $category=$this->db->get('categories')->row_array();                    
                    
                    $this->load->view('header');
                    $this->load->view('categories/edit',$category);
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
                if($intent=='create' && user_logged_in() && user_can('CREATE_CATEGORY'))
                {
                    if($this->input->post('category_name'))
                    {
                        $name=strtolower($this->input->post('category_name'));
                        $this->db->where('name',$name);
                        if($this->db->get('categories')->num_rows()>0)
                        {                            
                            $json['status']='already_exists';
                            echo json_encode($json);
                        }
                        else
                        {                            
                            $data['name']=strtolower($this->input->post('category_name'));
                            $data['active']=$this->input->post('category_active')?$this->input->post('category_active'):1;
                            $data['created_by']=$this->session->userdata('uid');
                                                        
                            $data['description']=$this->input->post('category_description')?$this->input->post('category_description'):'';                            

                            $this->db->insert('categories',$data);
                            $cid=$this->db->insert_id();
                            
                            $json['cid']=$cid;
                            $json['status']='ok';
                            echo json_encode($json);
                        }
                    }
                    else
                    {
                        if(!$this->input->post('category_name'))
                        {                            
                            $json['status']='no_name';
                            echo json_encode($json);
                        }
                    }
                }
                else if($intent=='edit' && user_logged_in() && user_can('EDIT_CATEGORY'))
                {
                    $request_from=$_SERVER['HTTP_REFERER'];
                    $cid=end(explode('/',$request_from));
                    
                    if(valid_integer($cid))
                    {                        
                        if($this->input->post('category_name'))
                        {
                            $name=$this->input->post('category_name');                            
                            $this->db->where('name',$name);
                            if($this->db->get('categories')->num_rows()>0)
                            {
                                $this->db->where('name',$name);
                                $db_cid=$this->db->get('categories')->row(0,'object')->cid;
                                if($cid!=$db_cid)
                                {
                                    $json['status']='already_exists';
                                    echo json_encode($json);
                                    die();
                                }                                
                            }
                            $data['name']=$name;                            
                            
                            if(!valid_yesno($this->input->post('category_active')))
                            {
                                $json['status']='invalid_value';
                                echo json_encode($json);
                                die();
                            }                            
                            
                            $data['active']=$this->input->post('category_active');
                            $data['description']=$this->input->post('category_description');                            
                            
                            $this->db->where('cid',$cid);
                            $this->db->update('categories',$data);

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

/* End of file categories.php */
/* Location: ./application/controllers/departments.php */