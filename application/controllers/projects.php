<?php

if (!defined('BASEPATH')) {
   exit('No direct script access allowed');
}

/*
 * must     -       project_id,code,name,created_by
 * optional -       address,city,phone,cell,email,fax,description,created_on
 */

class Projects extends CI_Controller
{
   public function index()
   {
      $this->all();
   }
   public function create()
   {
      if (user_logged_in()) {

         unset($temp);
         $temp=$this->db->get('buyers')->result_array();
         foreach($temp as $key=>$value)
             $project['buyers'][$value['buyer_id']]=$value['name'];

         unset($temp);
         $temp=$this->db->get('suppliers')->result_array();
         foreach($temp as $key=>$value)
             $project['suppliers'][$value['supplier_id']]=$value['name'];

         $this->load->view('header');
         $this->load->view('menu');
         $this->load->view('wrap_begin');
         $this->load->view('projects/create', $project);
         $this->load->view('wrap_end');
         $this->load->view('footer');
      }
   }
   public function edit()
   {
      if (user_logged_in()) {
         if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {
            $project_id = $this->uri->segment(3);
            $this->db->where('project_id', $project_id);
            $project = $this->db->get('projects')->row_array();

            unset($temp);
            $temp=$this->db->get('buyers')->result_array();
            foreach($temp as $key=>$value)
                $project['buyers'][$value['buyer_id']]=$value['name'];

            unset($temp);
            $temp=$this->db->get('suppliers')->result_array();
            foreach($temp as $key=>$value)
                $project['suppliers'][$value['supplier_id']]=$value['name'];

            $this->load->view('header');
            $this->load->view('menu');
            $this->load->view('wrap_begin');
            $this->load->view('projects/edit', $project);
            $this->load->view('wrap_end');
            $this->load->view('footer');
         }
      }
   }
   public function miniedit()
   {
      if (user_logged_in()) {
         if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {
            $project_id = $this->uri->segment(3);
            $this->db->where('project_id', $project_id);
            $project = $this->db->get('projects')->row_array();

            unset($temp);
            $temp=$this->db->get('buyers')->result_array();
            foreach($temp as $key=>$value)
                $project['buyers'][$value['buyer_id']]=$value['name'];

            unset($temp);
            $temp=$this->db->get('suppliers')->result_array();
            foreach($temp as $key=>$value)
                $project['suppliers'][$value['supplier_id']]=$value['name'];

            $this->load->view('header');
            $this->load->view('projects/edit', $project);
            $this->load->view('miniedit');
            $this->load->view('footer');
         }
      }
   }
   public function fetch(){
      $this->load->model('project');
      if (user_logged_in()) {
         header('Content-type: application/json');
         $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'name';
         $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
         $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
         $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

         if (in_array($sort_by, array('name', 'buyer', 'supplier'))) {
            $data['status'] = 'ok';
            $data['page'] = $page;
            $data['limit'] = $limit;

            if($sort_by=='name')
               $sort_by='projects.name';
            else if($sort_by=='buyer')
               $sort_by='buyers.name';
            else if($sort_by=='supplier')
               $sort_by='suppliers.name';

            $this->db->select('project_id, projects.name as name, buyers.name as buyer, suppliers.name as supplier');
            $this->db->from('projects');
            $this->db->join('buyers','projects.buyer_id=buyers.buyer_id');
            $this->db->join('suppliers','projects.supplier_id=suppliers.supplier_id');
            $this->db->order_by($sort_by, $order);

            /* search */
            if (in_array($this->input->get_post('filter_by'), array('name', 'buyer', 'supplier'))) {
               if ($this->input->get_post('filter') && strlen($this->input->get_post('filter')) > 0) {

                  $filter_by=$this->input->get_post('filter_by');
                  if($filter_by=='name')
                     $filter_by='projects.name';
                  else if($filter_by=='buyer')
                     $filter_by='buyers.name';
                  else if($filter_by=='supplier')
                     $filter_by='suppliers.name';

                  $this->db->like($filter_by, $this->input->get_post('filter'), 'both');
                  $results = $this->db->get()->result_array();

                  $data['total'] = count($results);
                  $data['results'] = array_slice($results,$page,$limit);

                  echo json_encode($data);
                  die();
               }
            }
            $results = $this->db->get()->result_array();
            $data['total'] = count($results);
            $data['results'] = array_slice($results,$page,$limit);

            echo json_encode($data);
            die();
         }
         else {
            $data['status'] = 'invalid_sort_by';
            echo json_encode($data);
            die();
         }
      }
      else {
         echo '<h1>Bad Request</h1>';
      }
      die();
   }
   public function all()
   {
      if (user_logged_in()) {
         $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'name';
         $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
         $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
         $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

         if (in_array($sort_by, $this->db->list_fields('projects'))) {
            $data['fields'] = array(
               'name' => array('Project ID', '10'),
               'buyer' => array('Buyer', '45'),
               'supplier' => array('Supplier', '45')
            );
            $data['search_fields'] = array(
               'name' => 'Project ID',
               'buyer' => 'Buyer',
               'supplier' => 'Supplier'
            );
            $data['orders'] = array(
               'asc' => 'Ascending',
               'desc' => 'Descending',
            );

            $data['sort_by'] = 'name';
            $data['order'] = $order;
            $data['limit'] = $limit;
            $data['page'] = $page;

            //echo '<pre>';print_r($data);echo '</pre>';die();

            unset($temp);
            $temp=$this->db->get('buyers')->result_array();
            foreach($temp as $key=>$value)
                $data['buyers'][$value['buyer_id']]=$value['name'];

            unset($temp);
            $temp=$this->db->get('suppliers')->result_array();
            foreach($temp as $key=>$value)
                $data['suppliers'][$value['supplier_id']]=$value['name'];

            $this->load->view('header');
            $this->load->view('menu');
            $this->load->view('wrap_begin');
            $this->load->view('projects/all', $data);
            $this->load->view('wrap_end');
            $this->load->view('footer');
         }
      }
      else {
         echo '<h1>Bad Request</h1>';
      }
   }
   public function ajax()
   {
      header('Content-type: application/json');
      if ($this->input->get_post('intent')) {
         $intent = $this->input->get_post('intent');
         if ($intent == 'create' && user_logged_in()) {
            if ($this->input->post('project_name')) {
               $data['name'] = $this->input->post('project_name');

               $this->load->model('project');
               if (!$this->project->getBy('name', $data['name'])) {

                  $data['buyer_id']=$this->input->post('buyer_id');
                  $data['supplier_id']=$this->input->post('supplier_id');

                  $project_id = $this->project->create($data);
                  if ($project_id === false) {
                     $json['status'] = 'unknown_error';
                     echo json_encode($json);
                     die();
                  }

                  $json['project_id'] = $project_id;
                  $json['status'] = 'ok';
                  echo json_encode($json);
                  die();
               }
               else {
                  $json['status'] = 'already_exists';
                  echo json_encode($json);
                  die();
               }
            }
            else {
               if (!$this->input->post('project_name')) {
                  $json['status'] = 'no_name';
                  echo json_encode($json);
                  die();
               }
            }
         }
         elseif ($intent == 'edit' && user_logged_in()) {
            $request_from = $_SERVER['HTTP_REFERER'];
            $project_id = end(explode('/', $request_from));

            if (valid_integer($project_id)) {
               if ($this->input->post('project_name')) {
                  $name = $this->input->post('project_name');
                  $data['name'] = $name;
                  $data['buyer_id']=$this->input->post('buyer_id');
                  $data['supplier_id']=$this->input->post('supplier_id');

                  $this->db->where('project_id', $project_id);
                  $this->db->update('projects', $data);

                  $json['status'] = 'ok';
                  echo json_encode($json);
               }
               else {
                  if (!$this->input->post('project_name')) {
                     $json['status'] = 'no_name';
                     echo json_encode($json);
                     die();
                  }
               }
            }
         }
         else {
            $json['status'] = 'unauthorized_access';
            echo json_encode($json);
         }
      }
      else {
         $json['status'] = 'no_intent';
         echo json_encode($json);
      }
      die();
   }
   public function detect()
   {
      if (user_logged_in()) {
         if ($this->input->post('filter_by') == 'code') {
            $code = $this->input->post('filter');
            $this->load->model('project');
            $project = $this->project->getBy('code', $code);
            if ($project === 0 || $project === false) {
               header('Content-type: application/json');
               $json['status'] = 'invalid';
               echo json_encode($json);
               die();
            }
            header('Content-type: application/json');
            $json['status'] = 'ok';
            $json['project'] = $project;
            echo json_encode($json);
            die();
         }
         header('Content-type: application/json');
         $json['status'] = 'invalid';
         echo json_encode($json);
         die();
      }
   }
}

/* End of file projects.php */
/* Location: ./application/controllers/projects.php */
