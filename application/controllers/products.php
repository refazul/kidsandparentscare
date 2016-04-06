<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * must     -       pid,barcode,name,unit
 * optional -       created_on,department,image
 */

class Products extends CI_Controller
{
    public function index()
    {
        $this->all();
    }
    public function fetch()
    {
        if (user_logged_in()) {
            if ($this->input->post('invoice')) {
                header('Content-type: application/json');
                $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'pid';
                $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 25;
                $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

                $this->db->select('A.pid as pid', false);
                $this->db->select('A.barcode as barcode', false);
                $this->db->select('A.name as name', false);
                $this->db->select('A.sku as sku', false);
                $this->db->select('A.unit as unit', false);
                $this->db->select('B.Q as stock', false);
                $this->db->select('B.P as price', false);
                $this->db->select('B.D as discount_amount', false);
                $this->db->select('B.T as discount_type', false);
                $this->db->from('products as A');
                $this->db->join('(select pid,sum(quantity)as Q,max(unit_sale) as P,max(discount_amount) as D,max(discount_type) as T from stocks group by pid having Q>0) as B', 'A.pid = B.pid');
                $this->db->where('A.review', 1);
                if (in_array($this->input->post('filter_by'), array('barcode', 'name', 'sku', 'unit'))) {
                    $this->db->like($this->input->post('filter_by'), $this->input->post('filter'), 'both');
                } elseif (in_array($this->input->post('filter_by'), array('department'))) {
                    $this->db->where($this->input->post('filter_by'), $this->input->post('filter'));
                }
                $this->db->order_by($sort_by, $order);
                $data['total'] = $this->db->get()->num_rows();

                $this->db->select('A.pid as pid', false);
                $this->db->select('A.barcode as barcode', false);
                $this->db->select('A.name as name', false);
                $this->db->select('A.sku as sku', false);
                $this->db->select('A.unit as unit', false);
                $this->db->select('B.Q as stock', false);
                $this->db->select('B.P as price', false);
                $this->db->select('B.D as discount_amount', false);
                $this->db->select('B.T as discount_type', false);
                $this->db->from('products as A');
                $this->db->join('(select pid,sum(quantity)as Q,max(unit_sale) as P,max(discount_amount) as D,max(discount_type) as T from stocks group by pid having Q>0) as B', 'A.pid = B.pid');
                $this->db->where('A.review', 1);
                if (in_array($this->input->post('filter_by'), array('barcode', 'name', 'sku', 'unit'))) {
                    $this->db->like($this->input->post('filter_by'), $this->input->post('filter'), 'both');
                } elseif (in_array($this->input->post('filter_by'), array('department'))) {
                    $this->db->where($this->input->post('filter_by'), $this->input->post('filter'));
                }
                $this->db->order_by($sort_by, $order);
                $this->db->limit($limit, $limit * $page);
                $data['results'] = $this->db->get()->result_array();
                $data['page'] = $page;
                $data['limit'] = $limit;
                $data['status'] = 'ok';

                    /*
                    $this->db->where('review',1);
                    $data['total']=$this->db->get('products')->num_rows();

                    if(in_array($this->input->get_post('filter_by'),array('barcode','name','sku','unit')))
                    {
                        $this->db->order_by($sort_by, $order);
                        $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                        $this->db->where('review',1);
                        $data['total']=$this->db->get('products')->num_rows();

                        $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                    }
                    else if(in_array($this->input->get_post('filter_by'),array('review','category','department')))
                    {
                        $this->db->order_by($sort_by, $order);
                        $this->db->where($this->input->get_post('filter_by'),$this->input->get_post('filter'));
                        $this->db->where('review',1);
                        $data['total']=$this->db->get('products')->num_rows();

                        $this->db->where($this->input->get_post('filter_by'),$this->input->get_post('filter'));
                    }

                    $this->db->where('review',1);
                    $this->db->order_by($sort_by, $order);
                    $temp=$this->db->get('products',$limit,$limit*$page)->result_array();
                    $data['page']=$page;
                    $data['limit']=$limit;
                    $data['status']='ok';

                    $data['results']=array();

                    foreach($temp as $key=>$value)
                    {
                        $cid=$temp[$key]['category'];
                        $this->db->where('cid',$cid);
                        $category=$this->db->get('categories')->row(0,'object')->name;

                        $did=$temp[$key]['department'];
                        $this->db->where('did',$did);
                        $department=$this->db->get('departments')->row(0,'object')->name;

                        $temp[$key]['category']=$category;
                        $temp[$key]['department']=$department;

                        $pid=$temp[$key]['pid'];
                        $this->db->where('pid',$pid);
                        $this->db->order_by('stocked_on','desc');

                        if($this->db->get('stocks')->num_rows()>0)
                        {
                            $this->db->where('pid',$pid);
                            $this->db->order_by('stocked_on','desc');
                            $sample_stock=$this->db->get('stocks')->row(0,'object');

                            $this->db->select_sum('quantity');
                            $this->db->where('pid',$pid);
                            $quantity=$this->db->get('stocks')->row()->quantity;

                            $this->db->select_max('unit_sale');
                            $this->db->where('pid',$pid);
                            $unit_sale=$this->db->get('stocks')->row()->unit_sale;

                            if($quantity>0)
                            {
                                $temp[$key]['stock']=$quantity;
                                $temp[$key]['price']=$unit_sale;
                                $temp[$key]['discount_amount']=$sample_stock->discount_amount;
                                $temp[$key]['discount_type']=$sample_stock->discount_type;

                                array_push($data['results'],$temp[$key]);
                            }
                            else
                            {
                                $data['total']--;
                            }
                        }
                        else
                        {
                            $data['total']--;
                        }
                    }
                     *
                     */
                    echo json_encode($data);
                die();
            }

            header('Content-type: application/json');
            $data = $this->product->get_all_products();
            $data['status'] = 'ok';
            echo json_encode($data);
            die();
        } else {
            echo '<h1>Bad Request</h1>';
        }
        die();
    }
    public function all()
    {
        if (user_logged_in()) {
            $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'pid';
            $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
            $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 25;
            $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

            if (in_array($sort_by, $this->db->list_fields('products'))) {
                $data['fields'] = array(
                        'barcode' => array('Barcode', '10', 'left'),
                        'name' => array('Name', '15', 'left'),
                        'sku' => array('SKU', '8', 'left'),
                        'unit' => array('Unit', '5', 'left'),
                        'department' => array('Department', '10', 'left'),
                        'category' => array('Category', '10', 'left'),
                        'stock' => array('Stock', '4'),
                        'price' => array('Price', '5'),
                    );
                $data['search_fields'] = array(
                        'sku' => 'SKU',
                        'barcode' => 'Barcode',
                        'name' => 'Name',
                        'category' => 'Category',
                        'department' => 'Department',
                        'review' => 'Reviewed',
                    );
                $data['orders'] = array(
                        'asc' => 'Ascending',
                        'desc' => 'Descending',
                    );

                $data['sort_by'] = 'pid';
                $data['order'] = $order;
                $data['limit'] = $limit;
                $data['page'] = $page;

                $data['reviews'] = array(
                        '1' => 'Yes',
                        '0' => 'No',
                    );

                unset($temp);
                $this->db->where('active', 1);
                $temp = $this->db->get('departments')->result_array();
                foreach ($temp as $key => $value) {
                    $data['departments'][$value['did']] = $value['name'];
                }

                unset($temp);
                $this->db->where('active', 1);
                $temp = $this->db->get('categories')->result_array();
                foreach ($temp as $key => $value) {
                    $data['categories'][$value['cid']] = $value['name'];
                }

                    //echo '<pre>';print_r($data);echo '</pre>';die();

                    $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('products/all', $data);
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        } else {
            echo '<h1>Bad Request</h1>';
        }
    }
    public function create()
    {
        if (user_logged_in() && user_can('CREATE_PRODUCT')) {
            $product['departments'] = array();
            $product['categories'] = array();
            unset($temp);
            $this->db->where('active', 1);
            $temp = $this->db->get('departments')->result_array();
            foreach ($temp as $key => $value) {
                $product['departments'][$value['did']] = $value['name'];
            }

            unset($temp);
            $temp = $this->db->get('categories')->result_array();
            foreach ($temp as $key => $value) {
                $product['categories'][$value['cid']] = $value['name'];
            }

            $this->load->view('header');
            $this->load->view('menu');
            $this->load->view('wrap_begin');
            $this->load->view('products/create', $product);
            $this->load->view('wrap_end');
            $this->load->view('footer');
        }
    }
    public function edit()
    {
        if (user_logged_in() && user_can('EDIT_PRODUCT')) {
            if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {

                /* Basic Product */
                $pid = $this->uri->segment(3);
                $this->db->where('pid', $pid);
                $product = $this->db->get('products')->row_array();

                /* Price */
                $this->db->where('pid', $pid);
                $this->db->order_by('stocked_on', 'desc');
                if ($this->db->get('stocks')->num_rows() > 0) {
                    $this->db->select_max('unit_sale');
                    $this->db->where('pid', $pid);
                    $unit_sale = $this->db->get('stocks')->row()->unit_sale;
                    $product['price'] = $unit_sale;
                } else {
                    $product['price'] = '0.00';
                }

                $product['suppliers'] = array();
                $product['departments'] = array();
                $product['categories'] = array();

                /* Suppliers */
                unset($temp);
                $temp = $this->db->get('suppliers')->result_array();
                foreach ($temp as $key => $value) {
                    $product['suppliers'][$value['sid']] = $value['name'];
                }

                    /* Departments & Categories */
                    unset($temp);
                $this->db->where('active', 1);
                $temp = $this->db->get('departments')->result_array();
                foreach ($temp as $key => $value) {
                    $product['departments'][$value['did']] = $value['name'];
                }

                unset($temp);
                $temp = $this->db->get('categories')->result_array();
                foreach ($temp as $key => $value) {
                    $product['categories'][$value['cid']] = $value['name'];
                }

                    /* Review */
                    $product['reviews'][0] = 'No';
                $product['reviews'][1] = 'Yes';

                    /* Preload Stocks*/
                    unset($temp);
                $this->db->where('pid', $pid);
                $preload = $this->db->get('stocks')->result_array();
                foreach ($preload as $key => $value) {
                    $preload[$key]['stock_id'] = $value['stid'];
                    $preload[$key]['stock_buy'] = $value['unit_cost'];
                    $preload[$key]['stock_sell'] = $value['unit_sale'];
                    $preload[$key]['stock_quantity'] = $value['quantity'];
                    $preload[$key]['stock_store_quantity'] = $value['store_quantity'];
                    $preload[$key]['stock_discount_type'] = $value['discount_type'];
                    $preload[$key]['stock_discount_amount'] = $value['discount_amount'];
                    $preload[$key]['stock_stocked_on'] = $value['stocked_on'];

                    $preload[$key]['stock_supplier'] = $product['suppliers'][$value['sid']];
                }
                $product['preload'] = $preload;

                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('products/edit', $product);
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }
    }
    public function miniedit()
    {
        if (user_logged_in() && user_can('EDIT_PRODUCT')) {
            if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {
                $pid = $this->uri->segment(3);
                $this->db->where('pid', $pid);
                $product = $this->db->get('products')->row_array();

                $this->db->where('pid', $pid);
                $this->db->order_by('stocked_on', 'desc');

                if ($this->db->get('stocks')->num_rows() > 0) {
                    $this->db->select_max('unit_sale');
                    $this->db->where('pid', $pid);
                    $unit_sale = $this->db->get('stocks')->row()->unit_sale;

                    $product['price'] = $unit_sale;
                } else {
                    $product['price'] = '0.00';
                }

                unset($temp);
                $temp = $this->db->get('suppliers')->result_array();
                foreach ($temp as $key => $value) {
                    $product['suppliers'][$value['sid']] = $value['name'];
                }

                unset($temp);
                $this->db->where('active', 1);
                $temp = $this->db->get('departments')->result_array();
                foreach ($temp as $key => $value) {
                    $product['departments'][$value['did']] = $value['name'];
                }

                unset($temp);
                $temp = $this->db->get('categories')->result_array();
                foreach ($temp as $key => $value) {
                    $product['categories'][$value['cid']] = $value['name'];
                }

                $product['reviews'][0] = 'No';
                $product['reviews'][1] = 'Yes';

                unset($temp);
                $this->db->where('pid', $pid);
                $preload = $this->db->get('stocks')->result_array();
                foreach ($preload as $key => $value) {
                    //echo '<pre>';print_r($key);print_r($value);echo '</pre>';
                        $preload[$key]['stock_id'] = $value['stid'];
                    $preload[$key]['stock_buy'] = $value['unit_cost'];
                    $preload[$key]['stock_sell'] = $value['unit_sale'];
                    $preload[$key]['stock_quantity'] = $value['quantity'];
                    $preload[$key]['stock_store_quantity'] = $value['store_quantity'];
                    $preload[$key]['stock_discount_type'] = $value['discount_type'];
                    $preload[$key]['stock_discount_amount'] = $value['discount_amount'];
                    $preload[$key]['stock_stocked_on'] = $value['stocked_on'];

                    $preload[$key]['stock_supplier'] = $product['suppliers'][$value['sid']];
                }
                    //echo '<pre>';print_r($preload);echo '</pre>';

                    if (isset($preload)) {
                        $product['preload'] = $preload;
                    }

                $this->load->view('header');
                $this->load->view('products/edit', $product);
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
            if ($intent == 'create' && user_logged_in() && user_can('CREATE_PRODUCT')) {
                if ($this->input->post('product_barcode') && $this->input->post('product_name') && $this->input->post('product_sku')) {

                        /* Check for uniqueness - barcode */
                        $barcode = $this->input->post('product_barcode');
                    $this->db->where('barcode', $barcode);
                    if ($this->db->get('products')->num_rows() > 0) {
                        $json['status'] = 'barcode_already_exists';
                        echo json_encode($json);
                        die();
                    }

                        /* Check for uniqueness - sku */
                        $sku = $this->input->post('product_sku');
                    $this->db->where('sku', $sku);
                    if ($this->db->get('products')->num_rows() > 0) {
                        $json['status'] = 'sku_already_exists';
                        echo json_encode($json);
                        die();
                    }

                        /* Check for existence - department */
                        if (!valid_department($this->input->post('product_department'))) {
                            $json['status'] = 'invalid_department';
                            echo json_encode($json);
                            die();
                        }

                        /* Check for existence - category */
                        if (!valid_category($this->input->post('product_category'))) {
                            $json['status'] = 'invalid_category';
                            echo json_encode($json);
                            die();
                        }

                        /* Collect & Process */
                        $data['barcode'] = $this->input->post('product_barcode');
                    $data['sku'] = $this->input->post('product_sku');
                    $data['name'] = $this->input->post('product_name');
                    $data['unit'] = $this->input->post('product_unit') ? $this->input->post('product_unit') : 'pc';
                    $data['department'] = $this->input->post('product_department');
                    $data['category'] = $this->input->post('product_category');
                    $data['image'] = $this->input->post('product_image') ? $this->input->post('product_image') : '';
                    date_default_timezone_set('Asia/Dhaka');
                    $data['created_on'] = date('Y-m-d H:i:s');

                        /* Insert */
                        $this->db->insert('products', $data);
                    $pid = $this->db->insert_id();

                        /* Reply Positively */
                        $json['pid'] = $pid;
                    $json['status'] = 'ok';
                    echo json_encode($json);
                    die();
                } else {

                        /* Missing barcode or sku or name */
                        if (!$this->input->post('product_barcode')) {
                            $json['status'] = 'no_barcode';
                            echo json_encode($json);
                            die();
                        }
                    if (!$this->input->post('product_sku')) {
                        $json['status'] = 'no_sku';
                        echo json_encode($json);
                        die();
                    }
                    if (!$this->input->post('product_name')) {
                        $json['status'] = 'no_name';
                        echo json_encode($json);
                        die();
                    }
                }
            } elseif ($intent == 'edit' && user_logged_in() && user_can('EDIT_PRODUCT')) {
                $request_from = $_SERVER['HTTP_REFERER'];
                $pid = end(explode('/', $request_from));

                if (valid_integer($pid)) {
                    if ($this->input->post('product_barcode') && $this->input->post('product_name') && $this->input->post('product_sku')) {

                            /* Check for uniqueness - barcode */
                            $barcode = $this->input->post('product_barcode');
                        $this->db->where('barcode', $barcode);
                        if ($this->db->get('products')->num_rows() > 0) {
                            $this->db->where('barcode', $barcode);
                            $db_pid = $this->db->get('products')->row(0, 'object')->pid;
                            if ($db_pid != $pid) {
                                $json['status'] = 'barcode_already_exists';
                                echo json_encode($json);
                                die();
                            }
                        }

                            /* Check for uniqueness - sku */
                            $sku = $this->input->post('product_sku');
                        $this->db->where('sku', $sku);
                        if ($this->db->get('products')->num_rows() > 0) {
                            $this->db->where('sku', $sku);
                            $db_pid = $this->db->get('products')->row(0, 'object')->pid;
                            if ($db_pid != $pid) {
                                $json['status'] = 'sku_already_exists';
                                echo json_encode($json);
                                die();
                            }
                        }

                            /* Check for existence - department */
                            if (!valid_department($this->input->post('product_department'))) {
                                $json['status'] = 'invalid_department';
                                echo json_encode($json);
                                die();
                            }

                            /* Check for existence - category */
                            if (!valid_category($this->input->post('product_category'))) {
                                $json['status'] = 'invalid_category';
                                echo json_encode($json);
                                die();
                            }

                            /* Collect & Process */
                            $data['barcode'] = $barcode;
                        $data['sku'] = $sku;
                        $data['name'] = $this->input->post('product_name');
                        $data['unit'] = $this->input->post('product_unit') ? $this->input->post('product_unit') : 'pc';
                        $data['department'] = $this->input->post('product_department');
                        $data['category'] = $this->input->post('product_category');
                        $data['review'] = $this->input->post('product_review');
                        $data['image'] = $this->input->post('product_image');

                            /* Update */
                            $this->db->where('pid', $pid);
                        $this->db->update('products', $data);

                            /* Reply Positively */
                            $json['status'] = 'ok';
                        echo json_encode($json);
                        die();
                    } else {

                            /* Missing barcode or sku or name */
                            if (!$this->input->post('product_barcode')) {
                                $json['status'] = 'no_barcode';
                                echo json_encode($json);
                                die();
                            }
                        if (!$this->input->post('product_sku')) {
                            $json['status'] = 'no_sku';
                            echo json_encode($json);
                            die();
                        }
                        if (!$this->input->post('product_name')) {
                            $json['status'] = 'no_name';
                            echo json_encode($json);
                            die();
                        }
                    }
                }
            } elseif ($intent == 'search' && user_logged_in() && user_can('CREATE_INVOICE')) {
                header('Content-type: application/json');
                $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'pid';
                $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 25;
                $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if (in_array($sort_by, $this->db->list_fields('products'))) {
                    $data['total'] = $this->db->get('products')->num_rows();

                    if ($this->input->get_post('filter') && strlen($this->input->get_post('filter')) > 0 && in_array($this->input->get_post('filter_by'), $this->db->list_fields('products'))) {
                        /* search */
                            if (in_array($this->input->get_post('filter_by'), array('barcode', 'name', 'sku', 'unit'))) {
                                $this->db->order_by($sort_by, $order);
                                $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
                                $data['total'] = $this->db->get('products')->num_rows();

                                $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
                            }
                    }

                    $this->db->order_by($sort_by, $order);
                    $data['results'] = $this->db->get('products', $limit, $limit * $page)->result_array();
                    $data['page'] = $page;
                    $data['limit'] = $limit;
                    $data['status'] = 'ok';

                    foreach ($data['results'] as $key => $value) {
                        $pid = $data['results'][$key]['pid'];

                        $this->db->where('pid', $pid);
                        $this->db->order_by('stocked_on', 'desc');

                        if ($this->db->get('stocks')->num_rows() > 0) {
                            $this->db->where('pid', $pid);
                            $this->db->order_by('stocked_on', 'desc');
                            $sample_stock = $this->db->get('stocks')->row(0, 'object');

                            $this->db->select_sum('quantity');
                            $this->db->where('pid', $pid);
                            $quantity = $this->db->get('stocks')->row()->quantity;

                            $this->db->select_max('unit_sale');
                            $this->db->where('pid', $pid);
                            $unit_sale = $this->db->get('stocks')->row()->unit_sale;

                            $data['results'][$key]['stock'] = $quantity;
                            $data['results'][$key]['price'] = $unit_sale;
                            $data['results'][$key]['discount_amount'] = $sample_stock->discount_amount;
                            $data['results'][$key]['discount_type'] = $sample_stock->discount_type;
                        } else {
                            $data['results'][$key]['stock'] = 0;
                            $data['results'][$key]['price'] = 0;
                            $data['results'][$key]['discount_amount'] = 0;
                            $data['results'][$key]['discount_type'] = 'absolute';
                        }
                    }
                    echo json_encode($data);
                } else {
                    $json['results'] = array();
                    $json['status'] = 'ok';
                    echo json_encode($json);
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
