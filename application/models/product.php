<?php

class Product extends CI_Model
{
    public $title = '';
    public $content = '';
    public $date = '';

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function get_sellable_products()
    {
        if (user_logged_in()) {
            $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'pid';
            $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
            $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 25;
            $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

            $this->db->where('review', 1);
            $data['total'] = $this->db->get('products')->num_rows();

            /* search */
            if (in_array($this->input->get_post('filter_by'), array('barcode', 'name', 'sku', 'unit'))) {
                $this->db->order_by($sort_by, $order);
                $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
                $this->db->where('review', 1);
                $data['total'] = $this->db->get('products')->num_rows();

                $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
            } elseif (in_array($this->input->get_post('filter_by'), array('review', 'category', 'department'))) {
                $this->db->order_by($sort_by, $order);
                $this->db->where($this->input->get_post('filter_by'), $this->input->get_post('filter'));
                $this->db->where('review', 1);
                $data['total'] = $this->db->get('products')->num_rows();

                $this->db->where($this->input->get_post('filter_by'), $this->input->get_post('filter'));
            }

            $this->db->where('review', 1);
            $this->db->order_by($sort_by, $order);
            $data['results'] = $this->db->get('products', $limit, $limit * $page)->result_array();
            $data['page'] = $page;
            $data['limit'] = $limit;
            $data['status'] = 'ok';

            foreach ($data['results'] as $key => $value) {
                $cid = $data['results'][$key]['category'];
                $this->db->where('cid', $cid);
                $category = $this->db->get('categories')->row(0, 'object')->name;

                $did = $data['results'][$key]['department'];
                $this->db->where('did', $did);
                $department = $this->db->get('departments')->row(0, 'object')->name;

                $data['results'][$key]['category'] = $category;
                $data['results'][$key]['department'] = $department;

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

                    if ($quantity > 0) {
                        $data['results'][$key]['stock'] = $quantity;
                        $data['results'][$key]['price'] = $unit_sale;
                        $data['results'][$key]['discount_amount'] = $sample_stock->discount_amount;
                        $data['results'][$key]['discount_type'] = $sample_stock->discount_type;
                    } else {
                        unset($data['results'][$key]);
                        --$data['total'];
                    }
                } else {
                    unset($data['results'][$key]);
                    --$data['total'];
                }
            }

            return $data;
        }

        return array();
    }
    public function get_all_products()
    {
        if (user_logged_in()) {
            $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'pid';
            $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
            $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 25;
            $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

            $data['total'] = $this->db->get('products')->num_rows();

            /* search */
            if (in_array($this->input->get_post('filter_by'), array('barcode', 'name', 'sku', 'unit'))) {
                $this->db->order_by($sort_by, $order);
                $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
                $data['total'] = $this->db->get('products')->num_rows();

                $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
            } elseif (in_array($this->input->get_post('filter_by'), array('review', 'category', 'department'))) {
                $this->db->order_by($sort_by, $order);
                $this->db->where($this->input->get_post('filter_by'), $this->input->get_post('filter'));
                $data['total'] = $this->db->get('products')->num_rows();

                $this->db->where($this->input->get_post('filter_by'), $this->input->get_post('filter'));
            }
            $this->db->order_by($sort_by, $order);
            $data['results'] = $this->db->get('products', $limit, $limit * $page)->result_array();
            $data['page'] = $page;
            $data['limit'] = $limit;
            $data['status'] = 'ok';

            foreach ($data['results'] as $key => $value) {
                $cid = $data['results'][$key]['category'];
                $this->db->where('cid', $cid);
                $category = $this->db->get('categories')->row(0, 'object')->name;

                $did = $data['results'][$key]['department'];
                $this->db->where('did', $did);
                $department = $this->db->get('departments')->row(0, 'object')->name;

                $data['results'][$key]['category'] = $category;
                $data['results'][$key]['department'] = $department;

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

                    $data['results'][$key]['quantity'] = $quantity;
                } else {
                    $data['results'][$key]['quantity'] = 0;
                }
            }

            return $data;
        }//user_logged_in()
        return array();
    }//get_all_products()
}
