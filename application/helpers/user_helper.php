<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('user_logged_in'))
{
	function user_logged_in()
	{
            $CI =& get_instance();
            if($CI->session->userdata('uid'))
                return true;
            return false;
	}
}
if ( ! function_exists('user_can'))
{
	function user_can($privilege)
	{
            $CI =& get_instance();
            if($CI->session->userdata('uid'))
            {
                $uid=$CI->session->userdata('uid');
                
                $CI->db->where('uid',$uid);
                if($CI->db->get('users')->num_rows()>0)
                {
                    $CI->db->where('uid',$uid);
                    $rid=$CI->db->get('users')->row(0,'object')->rid;

                    $CI->db->where('privilege',$privilege);
                    if($CI->db->get('privileges')->num_rows()>0)
                    {
                        $CI->db->where('privilege',$privilege);
                        $prid=$CI->db->get('privileges')->row(0,'object')->prid;

                        $CI->db->where('rid',$rid);
                        $CI->db->where('prid',$prid);                    
                        if($CI->db->get('role_privilege')->num_rows()>0)
                            return true;                    
                    }
                }
            }
            return false;
	}
}