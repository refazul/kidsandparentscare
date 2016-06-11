<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reports extends CI_Controller
{
    public function index()
    {
        $this->salesreport();
    }
    public function salesreport()
    {
        $data['report_type'] = 'salesreport';
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('wrap_begin');
        $this->load->view('reports/template', $data);
        $this->load->view('wrap_end');
        $this->load->view('footer');
    }
    public function lcstatus()
    {
        $data['report_type'] = 'lcstatus';
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('wrap_begin');
        $this->load->view('reports/template', $data);
        $this->load->view('wrap_end');
        $this->load->view('footer');
    }
    public function ip()
    {
        $data['report_type'] = 'ip';
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('wrap_begin');
        $this->load->view('reports/template', $data);
        $this->load->view('wrap_end');
        $this->load->view('footer');
    }
    public function shipment()
    {
        $data['report_type'] = 'shipment';
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
        $this->db->from('buyers');
        $buyers = $this->db->get()->result();

        $this->db->select('*');
        $this->db->from('suppliers');
        $suppliers = $this->db->get()->result();

        $response['buyers'] = $buyers;
        $response['buyer'] = -1;
        $response['suppliers'] = $suppliers;
        $response['supplier'] = -1;

        $this->db->select('projects.*');
        $this->db->from('projects');
        if ($this->input->post('buyer') != -1) {
            $this->db->where('projects.buyer_id', $this->input->post('buyer'));
            $response['buyer'] = $this->input->post('buyer');
        }
        if ($this->input->post('supplier') != -1) {
            $this->db->where('projects.supplier_id', $this->input->post('supplier'));
            $response['supplier'] = $this->input->post('supplier');
        }
        $this->db->join('buyers', 'projects.buyer_id = buyers.buyer_id');
        $this->db->join('suppliers', 'projects.supplier_id = suppliers.supplier_id');
        $result = $this->db->get()->result();
        $response['result'] = $result;

        // Columns
        $columns = array();
        if ($report_type == 'salesreport') {
            $columns = array(
                array('fields' => array('passive' => 'project_id'), 'title' => 'PROJECT ID'),
                array('fields' => array('passive' => 'buyer'), 'title' => 'BUYER'),
                array('fields' => array('passive' => 'supplier'), 'title' => 'SUPPLIER'),
                array('fields' => array('active' => 'contract_number'), 'title' => 'CONTRACT NUMBER'),
                array('fields' => array('active' => 'contract_date'), 'title' => 'CONTRACT DATE'),
                array('fields' => array('extractable' => 's_c_origin'), 'title' => 'ORIGIN'),
                array('fields' => array('active' => 's_c_price', 'extractable' => 's_c_price_unit'), 'title' => 'PRICE'),
                array('fields' => array('extractable' => 's_c_payment'), 'title' => 'PAYMENT'),
                array('fields' => array('active' => 's_c_quantity', 'extractable' => 's_c_quantity_unit'), 'title' => 'QTY'),
            );
        } elseif ($report_type == 'lcstatus') {
            $columns = array(
                array('fields' => array('passive' => 'project_id'), 'title' => 'PROJECT ID'),
                array('fields' => array('passive' => 'buyer'), 'title' => 'BUYER'),
                array('fields' => array('passive' => 'supplier'), 'title' => 'SUPPLIER'),
                array('fields' => array('active' => 'contract_number'), 'title' => 'CONTRACT NUMBER'),
                array('fields' => array('active' => 'contract_date'), 'title' => 'CONTRACT DATE'),
                array('fields' => array('extractable' => 's_c_origin'), 'title' => 'ORIGIN'),
                array('fields' => array('active' => 's_c_quantity', 'extractable' => 's_c_quantity_unit'), 'title' => 'QTY'),
                array('fields' => array('active' => 'p_i_latest_date_of_lc_opening'), 'title' => 'LATEST DATE OF LC OPENING'),
                array('fields' => array('active' => 'lc_number'), 'title' => 'LC NO'),
                array('fields' => array('active' => 'lc_date_of_issue'), 'title' => 'DATE OF LC OPEN'),
            );
        } elseif ($report_type == 'ip') {
            $columns = array(
                array('fields' => array('passive' => 'project_id'), 'title' => 'PROJECT ID'),
                array('fields' => array('passive' => 'buyer'), 'title' => 'BUYER'),
                array('fields' => array('passive' => 'supplier'), 'title' => 'SUPPLIER'),
                array('fields' => array('active' => 'contract_number'), 'title' => 'CONTRACT NUMBER'),
                array('fields' => array('active' => 'contract_date'), 'title' => 'CONTRACT DATE'),
                array('fields' => array('extractable' => 's_c_origin'), 'title' => 'ORIGIN'),
                array('fields' => array('active' => 's_c_quantity', 'extractable' => 's_c_quantity_unit'), 'title' => 'QTY'),
                array('fields' => array('active' => 'p_i_latest_date_of_lc_opening'), 'title' => 'LATEST DATE OF LC OPENING'),
                array('fields' => array('active' => 'i_p_number'), 'title' => 'IP NO'),
                array('fields' => array('active' => 'i_p_date'), 'title' => 'IP DATE'),
                array('fields' => array('active' => 'i_p_expiry_date'), 'title' => 'IP EXPIRY DATE'),
            );
        } elseif ($report_type == 'shipment') {
            $columns = array(
                array('fields' => array('passive' => 'project_id'), 'title' => 'PROJECT ID'),
                array('fields' => array('passive' => 'buyer'), 'title' => 'BUYER'),
                array('fields' => array('passive' => 'supplier'), 'title' => 'SUPPLIER'),
                array('fields' => array('active' => 'contract_number'), 'title' => 'CONTRACT NUMBER'),
                array('fields' => array('active' => 'contract_date'), 'title' => 'CONTRACT DATE'),
                array('fields' => array('extractable' => 's_c_origin'), 'title' => 'ORIGIN'),
                array('fields' => array('active' => 's_c_quantity', 'extractable' => 's_c_quantity_unit'), 'title' => 'QTY'),
                array('fields' => array('active' => 'lc_number'), 'title' => 'LC NO'),
                array('fields' => array('active' => 'lc_date_of_issue'), 'title' => 'DATE OF LC OPEN'),
                array('fields' => array('active' => 'p_i_latest_date_of_shipment'), 'title' => 'LATEST DATE OF SHIPMENT'),
                array('fields' => array('extractable' => 'lc_port_of_loading'), 'title' => 'PORT OF LOADING'),
                array('fields' => array('active' => 'shipment_eta_date'), 'title' => 'ETA (CTG.)'),
            );
        }
        $response['columns'] = $columns;
        echo json_encode($response);
    }
}
