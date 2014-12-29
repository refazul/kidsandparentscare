<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('asset_url'))
{
	function asset_url()
	{		
		return base_url().'assets/';
	}
}
if ( ! function_exists('temp_dir'))
{
	function temp_dir()
	{		
		return str_replace('\\', '/', FCPATH.'assets/temp/');
	}
}