<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reports extends CI_Controller
{
    public function index()
    {
        $this->stockentry();
    }
    public function stockentry()
    {
        $data['report_type'] = 'stockentry';
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('wrap_begin');
        $this->load->view('reports/template', $data);
        $this->load->view('wrap_end');
        $this->load->view('footer');
    }
    public function calc()
    {
        $report_type = $this->uri->segment(3, 0);
        $response = array();

        $this->db->select('*');
        $this->db->from('suppliers');
        $suppliers = $this->db->get()->result();

        $this->db->select('*');
        $this->db->from('departments');
        $departments = $this->db->get()->result();

        $response['suppliers'] = $suppliers;
        $response['supplier'] = -1;
        $response['departments'] = $departments;
        $response['department'] = -1;

        $this->db->select('stocks.*, products.*, suppliers.*, departments.*');
        $this->db->from('stocks');
        if ($this->input->post('supplier') != -1) {
            $this->db->where('stocks.sid', $this->input->post('supplier'));
            $response['supplier'] = $this->input->post('supplier');
        }
        if ($this->input->post('department') != -1) {
            $this->db->where('products.department', $this->input->post('department'));
            $response['department'] = $this->input->post('department');
        }
        $this->db->join('products', 'stocks.pid = products.pid');
        $this->db->join('suppliers', 'stocks.sid = suppliers.sid');
        $this->db->join('departments', 'products.department = departments.did');
        $result = $this->db->get()->result();
        $response['result'] = $result;
        $response['total'] = count($result);

        // Columns
        $columns = array();
        if ($report_type == 'stockentry') {
            $columns = array(
                array('fields' => array('passive' => 'stid'), 'title' => 'Stock ID'),
                array('fields' => array('passive' => 'sku'), 'title' => 'SKU'),
                array('fields' => array('passive' => 'supplier'), 'title' => 'Supplier'),
                array('fields' => array('passive' => 'department'), 'title' => 'Department'),
                array('fields' => array('passive' => 'unit_cost'), 'title' => 'Unit Cost'),
                array('fields' => array('passive' => 'quantity'), 'title' => 'Quantity'),
                array('fields' => array('passive' => 'gross_total_cost'), 'title' => 'Total Cost'),
                array('fields' => array('passive' => 'stocked_on'), 'title' => 'Entry Time'),
            );
        }
        $response['columns'] = $columns;
        echo json_encode($response);
    }
}

/* End of file reports.php */
/* Location: ./application/controllers/reports.php */
