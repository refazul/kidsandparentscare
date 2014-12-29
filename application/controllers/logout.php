<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {
	
	public function index()
	{
            $this->session->unset_userdata('uid');
            
            $redirect_url=$this->input->get_post('redirect_url') ? $this->input->get_post('redirect_url') : site_url();
            redirect($redirect_url, 'refresh');
            die();
        }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */