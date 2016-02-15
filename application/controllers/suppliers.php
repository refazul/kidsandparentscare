<?php

if (!defined('BASEPATH')) {
   exit('No direct script access allowed');
}

/*
 * must     -       supplier_id,code,name,created_by
 * optional -       address,city,phone,cell,email,fax,description,created_on
 */

class Suppliers extends CI_Controller
{
   public function index()
   {
      $this->all();
   }
   public function create()
   {
      if (user_logged_in()) {
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
      if (user_logged_in()) {
         if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {
            $supplier_id = $this->uri->segment(3);
            $this->db->where('supplier_id', $supplier_id);
            $supplier = $this->db->get('suppliers')->row_array();

            $this->load->view('header');
            $this->load->view('menu');
            $this->load->view('wrap_begin');
            $this->load->view('suppliers/edit', $supplier);
            $this->load->view('wrap_end');
            $this->load->view('footer');
         }
      }
   }
   public function miniedit()
   {
      if (user_logged_in()) {
         if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {
            $supplier_id = $this->uri->segment(3);
            $this->db->where('supplier_id', $supplier_id);
            $supplier = $this->db->get('suppliers')->row_array();

            $this->load->view('header');
            $this->load->view('suppliers/edit', $supplier);
            $this->load->view('miniedit');
            $this->load->view('footer');
         }
      }
   }
   public function fetch()
   {
      if (user_logged_in()) {
         header('Content-type: application/json');
         $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'supplier_id';
         $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
         $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
         $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

         if (in_array($sort_by, array('supplier_id', 'name'))) {
            $data['total'] = $this->db->get('suppliers')->num_rows();

               /* search */
               if (in_array($this->input->get_post('filter_by'), array('supplier_id', 'name'))) {
                   if ($this->input->get_post('filter') && strlen($this->input->get_post('filter')) > 0) {
                       $this->db->order_by($sort_by, $order);
                       $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
                       $data['total'] = $this->db->get('suppliers')->num_rows();

                       $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
                   }
               }
               $this->db->order_by($sort_by, $order);
               $data['results'] = $this->db->get('suppliers', $limit, $limit * $page)->result_array();
               $data['page'] = $page;
               $data['limit'] = $limit;
               $data['status'] = 'ok';

               foreach ($data['results'] as $key => $value) {
                  /*
                  $this->db->where('key','POINT_TO_CASH_RATIO');
                  $ratio=$this->db->get('config')->row(0,'object')->value;

                  $data['results'][$key]['amount']=round($data['results'][$key]['points']/$ratio,2);
                  */
               }
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
         $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'supplier_id';
         $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
         $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
         $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

         if (in_array($sort_by, $this->db->list_fields('suppliers'))) {
            $data['fields'] = array(
               'supplier_id' => array('supplier ID', '10'),
               'name' => array('supplier Name', '90')
            );
            $data['search_fields'] = array(
               'name' => 'supplier Name',
               'supplier_id' => 'supplier ID'
            );
            $data['orders'] = array(
               'asc' => 'Ascending',
               'desc' => 'Descending',
            );

            $data['sort_by'] = 'supplier_id';
            $data['order'] = $order;
            $data['limit'] = $limit;
            $data['page'] = $page;

            //echo '<pre>';print_r($data);echo '</pre>';die();

            $this->load->view('header');
            $this->load->view('menu');
            $this->load->view('wrap_begin');
            $this->load->view('suppliers/all', $data);
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
            if ($this->input->post('supplier_name')) {
               $data['name'] = $this->input->post('supplier_name');

               $this->load->model('supplier');
               if (!$this->supplier->getBy('name', $data['name'])) {

                  $supplier_id = $this->supplier->create($data);
                  if ($supplier_id === false) {
                     $json['status'] = 'unknown_error';
                     echo json_encode($json);
                     die();
                  }

                  $json['supplier_id'] = $supplier_id;
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
               if (!$this->input->post('supplier_name')) {
                  $json['status'] = 'no_name';
                  echo json_encode($json);
                  die();
               }
            }
         }
         elseif ($intent == 'edit' && user_logged_in()) {
            $request_from = $_SERVER['HTTP_REFERER'];
            $supplier_id = end(explode('/', $request_from));

            if (valid_integer($supplier_id)) {
               if ($this->input->post('supplier_name')) {
                  $name = $this->input->post('supplier_name');
                  $data['name'] = $name;

                  $this->db->where('supplier_id', $supplier_id);
                  $this->db->update('suppliers', $data);

                  $json['status'] = 'ok';
                  echo json_encode($json);
               }
               else {
                  if (!$this->input->post('supplier_name')) {
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
            $this->load->model('supplier');
            $supplier = $this->supplier->getBy('code', $code);
            if ($supplier === 0 || $supplier === false) {
               header('Content-type: application/json');
               $json['status'] = 'invalid';
               echo json_encode($json);
               die();
            }
            header('Content-type: application/json');
            $json['status'] = 'ok';
            $json['supplier'] = $supplier;
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

/* End of file suppliers.php */
/* Location: ./application/controllers/suppliers.php */
