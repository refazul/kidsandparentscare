<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * must     -       rid,role
 * optional -       
 */

class Roles extends CI_Controller {
	
	public function index()
	{
            $this->all();
	}
        public function create()
        {
            if(user_logged_in() && user_can('CREATE_ROLE'))
            {
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('roles/create');
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }        
        public function edit()
        {
            if(user_logged_in() && user_can('EDIT_ROLE'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $rid=$this->uri->segment(3);
                    $this->db->where('rid',$rid);
                    $role=$this->db->get('roles')->row_array();
                    
                    $role['priv']=array();
                    $this->db->where('rid',$rid);                    
                    $temp=$this->db->get('role_privilege')->result_array();
                    foreach($temp as $t)
                        $role['priv'][]=$t['prid'];

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('roles/edit',$role);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');
                }
            }
        }
        public function miniedit()
        {
            if(user_logged_in() && user_can('EDIT_ROLE'))
            {
                if($this->uri->segment(3) && valid_integer($this->uri->segment(3)))
                {
                    $rid=$this->uri->segment(3);
                    $this->db->where('rid',$rid);
                    $role=$this->db->get('roles')->row_array();
                    
                    $role['priv']=array();
                    $this->db->where('rid',$rid);                    
                    $temp=$this->db->get('role_privilege')->result_array();
                    foreach($temp as $t)
                        $role['priv'][]=$t['prid'];
                    
                    $this->load->view('header');
                    $this->load->view('roles/edit',$role);
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
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'rid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 10;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('roles')))
                {
                    $data['total']=$this->db->get('roles')->num_rows();
                    
                    if($this->input->get_post('filter') && strlen($this->input->get_post('filter'))>0 && in_array($this->input->get_post('filter_by'), $this->db->list_fields('roles')))
                    {                        
                        /* search */
                        if(in_array($this->input->get_post('filter_by'),array('rid','role')))
                        {
                            $this->db->order_by($sort_by, $order);
                            $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                            $data['total']=$this->db->get('roles')->num_rows();
                            
                            $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                        }                        
                    }
                    
                    $this->db->order_by($sort_by, $order);
                    $data['results']=$this->db->get('roles',$limit,$limit*$page)->result_array();                    
                    $data['page']=$page;
                    $data['limit']=$limit;
                    $data['status']='ok';

                    foreach($data['results'] as $key=>$value)
                    {
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
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'rid';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 10;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('roles')))
                {
                    $data['fields']=array(
                        'rid'=>array('Role ID','10'),
                        'role'=>array('Role','30')
                    );
                    $data['search_fields']=array(
                        'rid'=>'Role ID',
                        'role'=>'Role'
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );
                    
                    $data['sort_by']='rid';
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;                    

                    //echo '<pre>';print_r($data);echo '</pre>';die();          

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('roles/all',$data);
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
                if($intent=='create' && user_logged_in() && user_can('CREATE_ROLE'))
                {
                    if($this->input->post('role_name'))
                    {
                        $name=strtolower($this->input->post('role_name'));
                        $this->db->where('role',$name);
                        if($this->db->get('roles')->num_rows()>0)
                        {                            
                            $json['status']='already_exists';
                            echo json_encode($json);
                        }
                        else
                        {
                            $data['role']=strtolower($this->input->post('role_name'));
                            
                            $priv=array();
                            
                            unset($temp);
                            if($this->input->post('product')):
                                $temp=$this->input->post('product');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=4;
                                    else if($t=='e')
                                        $priv[]=5;
                                    else if($t=='r')
                                        $priv[]=6;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('category')):
                                $temp=$this->input->post('category');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=22;
                                    else if($t=='e')
                                        $priv[]=23;
                                    else if($t=='r')
                                        $priv[]=24;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('department')):
                                $temp=$this->input->post('department');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=7;
                                    else if($t=='e')
                                        $priv[]=8;
                                    else if($t=='r')
                                        $priv[]=9;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('supplier')):
                                $temp=$this->input->post('supplier');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=13;
                                    else if($t=='e')
                                        $priv[]=14;
                                    else if($t=='r')
                                        $priv[]=15;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('manufacturer')):
                                $temp=$this->input->post('manufacturer');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=16;
                                    else if($t=='e')
                                        $priv[]=17;
                                    else if($t=='r')
                                        $priv[]=18;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('customer')):
                                $temp=$this->input->post('customer');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=25;
                                    else if($t=='e')
                                        $priv[]=26;
                                    else if($t=='r')
                                        $priv[]=27;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('stock')):
                                $temp=$this->input->post('stock');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=10;
                                    else if($t=='e')
                                        $priv[]=11;
                                    else if($t=='r')
                                        $priv[]=12;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('invoice')):
                                $temp=$this->input->post('invoice');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=19;
                                    else if($t=='e')
                                        $priv[]=20;
                                    else if($t=='r')
                                        $priv[]=21;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('user')):
                                $temp=$this->input->post('user');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=1;
                                    else if($t=='e')
                                        $priv[]=2;
                                    else if($t=='r')
                                        $priv[]=3;
                                }
                            endif;

                            $this->db->insert('roles',$data);
                            $rid=$this->db->insert_id();
                            
                            foreach($priv as $p)
                            {
                                $pr['rid']=$rid;
                                $pr['prid']=$p;                                
                                $this->db->insert('role_privilege',$pr);
                            }
                            
                            $json['rid']=$rid;
                            $json['status']='ok';
                            echo json_encode($json);
                        }
                    }
                    else
                    {
                        if(!$this->input->post('role_name'))
                        {                            
                            $json['status']='no_name';
                            echo json_encode($json);
                        }
                    }
                }
                else if($intent=='edit' && user_logged_in() && user_can('EDIT_ROLE'))
                {
                    $request_from=$_SERVER['HTTP_REFERER'];
                    $rid=end(explode('/',$request_from));
                    
                    if(valid_integer($rid))
                    {                        
                        if($this->input->post('role_name'))
                        {
                            $name=$this->input->post('role_name');                            
                            $this->db->where('role',$name);
                            if($this->db->get('roles')->num_rows()>0)
                            {
                                $this->db->where('role',$name);
                                $db_rid=$this->db->get('roles')->row(0,'object')->rid;
                                if($rid!=$db_rid)
                                {
                                    $json['status']='already_exists';
                                    echo json_encode($json);
                                    die();
                                }                                
                            }
                            $data['role']=$name;
                            
                            $priv=array();
                            
                            unset($temp);
                            if($this->input->post('product')):
                                $temp=$this->input->post('product');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=4;
                                    else if($t=='e')
                                        $priv[]=5;
                                    else if($t=='r')
                                        $priv[]=6;
                                }                                
                            endif;
                            
                            unset($temp);
                            if($this->input->post('category')):
                                $temp=$this->input->post('category');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=22;
                                    else if($t=='e')
                                        $priv[]=23;
                                    else if($t=='r')
                                        $priv[]=24;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('department')):
                                $temp=$this->input->post('department');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=7;
                                    else if($t=='e')
                                        $priv[]=8;
                                    else if($t=='r')
                                        $priv[]=9;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('supplier')):
                                $temp=$this->input->post('supplier');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=13;
                                    else if($t=='e')
                                        $priv[]=14;
                                    else if($t=='r')
                                        $priv[]=15;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('manufacturer')):
                                $temp=$this->input->post('manufacturer');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=16;
                                    else if($t=='e')
                                        $priv[]=17;
                                    else if($t=='r')
                                        $priv[]=18;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('customer')):
                                $temp=$this->input->post('customer');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=25;
                                    else if($t=='e')
                                        $priv[]=26;
                                    else if($t=='r')
                                        $priv[]=27;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('stock')):
                                $temp=$this->input->post('stock');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=10;
                                    else if($t=='e')
                                        $priv[]=11;
                                    else if($t=='r')
                                        $priv[]=12;
                                }
                            endif;
                            
                            unset($temp);
                            if($this->input->post('invoice')):
                                $temp=$this->input->post('invoice');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=19;
                                    else if($t=='e')
                                        $priv[]=20;
                                    else if($t=='r')
                                        $priv[]=21;
                                }                                
                            endif;
                            
                            unset($temp);
                            if($this->input->post('user')):
                                $temp=$this->input->post('user');
                                foreach($temp as $t)
                                {
                                    if($t=='c')
                                        $priv[]=1;
                                    else if($t=='e')
                                        $priv[]=2;
                                    else if($t=='r')
                                        $priv[]=3;
                                }
                            endif;
                            
                            $this->db->where('rid',$rid);
                            $this->db->update('roles',$data);
                                                        
                            $this->db->delete('role_privilege',array('rid'=>$rid));
                            
                            foreach($priv as $p)
                            {                                
                                $pr['rid']=$rid;
                                $pr['prid']=$p;                                
                                $this->db->insert('role_privilege',$pr);
                            }

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

/* End of file roles.php */
/* Location: ./application/controllers/departments.php */