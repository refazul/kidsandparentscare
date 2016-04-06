<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * must     -       pid,barcode,name,unit
 * optional -       created_on,department,image
 */

class Stocks extends CI_Controller
{
    public function index()
    {
        die();
    }

    public function edit()
    {
        if (user_logged_in() && user_can('EDIT_STOCK')) {
            if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {
                $stid = $this->uri->segment(3);
                $this->db->where('stid', $stid);
                $stock = $this->db->get('stocks')->row_array();

                unset($temp);
                $temp = $this->db->get('suppliers')->result_array();
                foreach ($temp as $key => $value) {
                    $stock['suppliers'][$value['sid']] = $value['name'];
                }

                    //echo '<pre>';print_r($stock);echo '</pre>';die();

                    $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('stocks/edit', $stock);
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }
    }
    public function miniedit()
    {
        if (user_logged_in() && user_can('EDIT_STOCK')) {
            if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {
                $stid = $this->uri->segment(3);
                $this->db->where('stid', $stid);
                $stock = $this->db->get('stocks')->row_array();

                unset($temp);
                $temp = $this->db->get('suppliers')->result_array();
                foreach ($temp as $key => $value) {
                    $stock['suppliers'][$value['sid']] = $value['name'];
                }

                    //echo '<pre>';print_r($stock);echo '</pre>';die();

                    $this->load->view('header');
                $this->load->view('stocks/edit', $stock);
                $this->load->view('miniedit');
                $this->load->view('footer');
            }
        }
    }
    public function ajax()
    {
        header('Content-type: application/json');
        if ($this->input->get_post('intent')) {
            $intent = $this->input->get_post('intent');
            if ($intent == 'create' && user_logged_in() && user_can('CREATE_STOCK')) {
                if ($this->input->post('stock_pid') && $this->input->post('stock_buy') && $this->input->post('stock_quantity')) {
                    if (!valid_integer($this->input->post('stock_pid'))) {
                        $json['status'] = 'invalid_product_id';
                        echo json_encode($json);
                        die();
                    }
                    if (!valid_numeric($this->input->post('stock_buy'))) {
                        $json['status'] = 'invalid_buy';
                        echo json_encode($json);
                        die();
                    }
                    if (!valid_integer($this->input->post('stock_quantity'))) {
                        $json['status'] = 'invalid_quantity';
                        echo json_encode($json);
                        die();
                    }
                    if (!valid_supplier($this->input->post('stock_supplier'))) {
                        $json['status'] = 'invalid_supplier';
                        echo json_encode($json);
                        die();
                    }

                    $data['unit_cost'] = $this->input->post('stock_buy');
                    $data['quantity'] = $this->input->post('stock_quantity');

                    $data['pid'] = $this->input->post('stock_pid');
                    $data['sid'] = $this->input->post('stock_supplier');

                    date_default_timezone_set('Asia/Dhaka');
                    $data['stocked_on'] = date('Y-m-d H:i:s');

                    $this->db->insert('stocks', $data);
                    $this->db->insert_id();

                    $json['status'] = 'ok';
                    echo json_encode($json);
                } else {
                    if (!$this->input->post('stock_buy')) {
                        $json['status'] = 'no_buy';
                        echo json_encode($json);
                        die();
                    } elseif (!$this->input->post('stock_quantity')) {
                        $json['status'] = 'no_quantity';
                        echo json_encode($json);
                        die();
                    }
                }
            } elseif ($intent == 'edit' && user_logged_in() && user_can('EDIT_STOCK')) {
                $request_from = $_SERVER['HTTP_REFERER'];
                $stid = end(explode('/', $request_from));

                if (valid_integer($stid) && $this->input->post('stock_buy')) {

                    /* Check for validity */
                    if (!valid_numeric($this->input->post('stock_buy'))) {
                        $json['status'] = 'invalid_buy';
                        echo json_encode($json);
                        die();
                    }
                    if (!valid_supplier($this->input->post('stock_supplier'))) {
                        $json['status'] = 'invalid_supplier';
                        echo json_encode($json);
                        die();
                    }

                    /* Collect & Process */
                    $data['unit_cost'] = $this->input->post('stock_buy');
                    $data['sid'] = $this->input->post('stock_supplier');
                    $data['quantity'] = $this->input->post('stock_quantity');

                    /* Update */
                    $this->db->where('stid', $stid);
                    $this->db->update('stocks', $data);

                    /* Reply Positively */
                    $json['status'] = 'ok';
                    echo json_encode($json);
                    die();
                } else {
                    if (!$this->input->post('stock_buy')) {
                        $json['status'] = 'no_buy';
                        echo json_encode($json);
                        die();
                    } elseif (!$this->input->post('stock_quantity')) {
                        $json['status'] = 'no_quantity';
                        echo json_encode($json);
                        die();
                    }
                }
            } else {
                $json['status'] = 'unauthorized_access';
                echo json_encode($json);
            }
        } else {
            $json['status'] = 'no_intent';
            echo json_encode($json);
        }
        die();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
