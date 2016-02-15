<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index(){
      if(user_logged_in()){
          redirect(site_url().'buyers', 'refresh');
          die();
      }
      else{
			$this->load->view('login');
      }
	}
	public function authenticate(){

		header('Content-type: application/json');
		if($this->input->get_post('user') && $this->input->get_post('pass')){
			$user=$this->input->get_post('user');
			$pass=$this->input->get_post('pass');

			$this->db->where('user',$user);
			if($this->db->get('users')->num_rows()>0){
				$this->db->where('user',$user);
				$actual_pass=$this->db->get('users')->row(0,'object')->pass;
				if($pass==$actual_pass){
					$this->db->where('user',$user);
					$uid=$this->db->get('users')->row(0,'object')->uid;

					$this->db->where('user',$user);
					$this->session->set_userdata(array('uid'=>$uid,'user'=>$user));

					$json['msg']='OK';
					$json['status']='ok';
				}
				else{
					$json['msg']='Wrong Password';
					$json['status']='wrong_password';
				}
			}
			else{
				$json['msg']='No Such User';
				$json['status']='no_such_user';
			}
		}
		else{
			$json['msg']='Bad Request';
			$json['status']='bad_request';
		}
		echo json_encode($json);
		die();
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
