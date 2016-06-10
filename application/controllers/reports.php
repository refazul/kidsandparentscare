<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Reports extends CI_Controller
{
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
        if ($report_type == 'salesreport') {
            echo json_encode($response);
        }
    }
}
