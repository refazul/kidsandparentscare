<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * must     -       uid,rid,user,pass
 * optional -       created_on,last_login,full_name,contact,image
 */

class Users extends CI_Controller
{
    public function index()
    {
        $this->all();
    }
    public function create()
    {
        if (user_logged_in() && user_can('CREATE_USER')) {
            $this->load->view('header');
            $this->load->view('menu');
            $this->load->view('wrap_begin');
            $this->load->view('users/create');
            $this->load->view('wrap_end');
            $this->load->view('footer');
        }
    }
    public function edit()
    {
        if (user_logged_in() && user_can('CREATE_USER')) {
            if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {
                $uid = $this->uri->segment(3);

                $this->db->where('uid', $uid);
                $user = $this->db->get('users')->row_array();

                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('users/edit', $user);
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }
    }
    public function miniedit()
    {
        if (user_logged_in() && user_can('CREATE_USER')) {
            if ($this->uri->segment(3) && valid_integer($this->uri->segment(3))) {
                $uid = $this->uri->segment(3);

                $this->db->where('uid', $uid);
                $user = $this->db->get('users')->row_array();

                $this->load->view('header');
                $this->load->view('users/edit', $user);
                $this->load->view('miniedit');
                $this->load->view('footer');
            }
        }
    }
    public function fetch()
    {
        if (user_logged_in() && user_can('CREATE_USER')) {
            header('Content-type: application/json');
            $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'uid';
            $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
            $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
            $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

            if (in_array($sort_by, $this->db->list_fields('users'))) {
                $data['total'] = $this->db->get('users')->num_rows();

                if ($this->input->get_post('filter') && strlen($this->input->get_post('filter')) > 0 && in_array($this->input->get_post('filter_by'), $this->db->list_fields('users'))) {
                    /* search */
                    if (in_array($this->input->get_post('filter_by'), array('uid', 'user', 'full_name', 'contact', 'email', 'address'))) {
                        $this->db->order_by($sort_by, $order);
                        $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
                        $data['total'] = $this->db->get('users')->num_rows();

                        $this->db->like($this->input->get_post('filter_by'), $this->input->get_post('filter'), 'both');
                    }
                }

                $this->db->order_by($sort_by, $order);
                $results = $this->db->get('users', $limit, $limit * $page)->result_array();
                foreach ($results as &$result) {
                    unset($result['pass']);
                }
                $data['results'] = $results;
                $data['page'] = $page;
                $data['limit'] = $limit;
                $data['status'] = 'ok';
            } else {
                $data['status'] = 'invalid_sort_by';
            }

            echo json_encode($data);
        } else {
            echo '<h1>Bad Request</h1>';
        }
        die();
    }
    public function all()
    {
        if (user_logged_in() && user_can('CREATE_USER')) {
            $sort_by = $this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'uid';
            $order = $this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
            $limit = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
            $page = $this->input->get_post('page') ? $this->input->get_post('page') : 0;

            if (in_array($sort_by, $this->db->list_fields('users'))) {
                $data['fields'] = array(
                    'uid' => array('User ID', '5'),
                    'user' => array('User', '15'),
                    'full_name' => array('Name', '15'),
                    'contact' => array('Contact', '10'),
                    'email' => array('Email', '15'),
                    'address' => array('Address', '20'),
                );
                $data['search_fields'] = array(
                    'uid' => 'User ID',
                    'user' => 'User',
                    'full_name' => 'Name',
                    'contact' => 'Contact',
                    'email' => 'Email',
                    'address' => 'Address',
                );
                $data['orders'] = array(
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                );

                $data['sort_by'] = 'uid';
                $data['order'] = $order;
                $data['limit'] = $limit;
                $data['page'] = $page;

                //echo '<pre>';print_r($data);echo '</pre>';die();

                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('users/all', $data);
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        } else {
            echo '<h1>Bad Request</h1>';
        }
    }

    public function ajax()
    {
        header('Content-type: application/json');
        if ($this->input->get_post('intent')) {
            $intent = $this->input->get_post('intent');
            if ($intent == 'create' && user_logged_in() && user_can('CREATE_USER')) {
                if ($this->input->post('user_name') && $this->input->post('user_pass')) {
                    $user = $this->input->post('user_name');
                    $this->db->where('user', $user);
                    if ($this->db->get('users')->num_rows() > 0) {
                        $json['status'] = 'already_exists';
                        echo json_encode($json);
                    } else {
                        $data['user'] = $this->input->post('user_name');
                        $data['pass'] = $this->input->post('user_pass');

                        $data['full_name'] = $this->input->post('user_fullname') ? $this->input->post('user_fullname') : '';
                        $data['contact'] = $this->input->post('user_contact') ? $this->input->post('user_contact') : '';
                        $data['email'] = $this->input->post('user_email') ? $this->input->post('user_email') : '';
                        $data['address'] = $this->input->post('user_address') ? $this->input->post('user_address') : '';
                        $data['rid'] = 2;

                        $this->db->insert('users', $data);
                        $uid = $this->db->insert_id();

                        $json['uid'] = $uid;
                        $json['status'] = 'ok';
                        echo json_encode($json);
                    }
                } else {
                    if (!$this->input->post('user_name')) {
                        $json['status'] = 'no_user';
                        echo json_encode($json);
                    } elseif (!$this->input->post('user_pass')) {
                        $json['status'] = 'no_pass';
                        echo json_encode($json);
                    }
                }
            } elseif ($intent = 'edit' && user_logged_in() && user_can('CREATE_USER')) {
                $request_from = $_SERVER['HTTP_REFERER'];
                $uid = end(explode('/', $request_from));

                if (valid_integer($uid)) {
                    if ($this->input->post('user_pass')) {
                        $data['pass'] = $this->input->post('user_pass');
                    }
                    if ($this->input->post('user_name')) {
                        $user = $this->input->post('user_name');
                        $this->db->where('user', $user);
                        if ($this->db->get('users')->num_rows() > 0) {
                            $this->db->where('user', $user);
                            $db_uid = $this->db->get('users')->row(0, 'object')->uid;
                            if ($uid != $db_uid) {
                                $json['status'] = 'already_exists';
                                echo json_encode($json);
                                die();
                            }
                        }
                        $data['user'] = $this->input->post('user_name');
                    }
                    $data['full_name'] = $this->input->post('user_fullname') ? $this->input->post('user_fullname') : '';
                    $data['contact'] = $this->input->post('user_contact') ? $this->input->post('user_contact') : '';
                    $data['email'] = $this->input->post('user_email') ? $this->input->post('user_email') : '';
                    $data['address'] = $this->input->post('user_address') ? $this->input->post('user_address') : '';

                    $this->db->where('uid', $uid);
                    $this->db->update('users', $data);

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
