<?php
if ( ! function_exists('valid_role'))
{
	function valid_role($role)
	{            
            if(in_array($role,array('2','3','4','5','6','7','8','9','10')))
                    return true;
            return false;
	}
}
if ( ! function_exists('valid_supplier'))
{
	function valid_supplier($supplier)
	{
            $CI = & get_instance();
            $CI->db->select('sid');
            $CI->db->where('active',1);
            $valid=array();
            foreach($CI->db->get('suppliers')->result_array() as $key=>$value)            
                array_push($valid, $value['sid']);
            
            if(in_array($supplier,$valid))
                    return true;
            return false;
	}
}
if ( ! function_exists('valid_manufacturer'))
{
	function valid_manufacturer($manufacturer)
	{
            $CI = & get_instance();
            $CI->db->select('mid');
            $CI->db->where('active',1);
            $valid=array();
            foreach($CI->db->get('manufacturers')->result_array() as $key=>$value)            
                array_push($valid, $value['mid']);
            
            if(in_array($manufacturer,$valid))
                    return true;
            return false;
	}
}
if ( ! function_exists('valid_department'))
{
	function valid_department($department)
	{
            $CI = & get_instance();
            $CI->db->select('did');
            $CI->db->where('active',1);
            $valid=array();
            foreach($CI->db->get('departments')->result_array() as $key=>$value)            
                array_push($valid, $value['did']);
            
            if(in_array($department,$valid))
                    return true;
            return false;
	}
}
if ( ! function_exists('valid_category'))
{
	function valid_category($category)
	{
            $CI = & get_instance();
            $CI->db->select('cid');
            $CI->db->where('active',1);
            $valid=array();
            foreach($CI->db->get('categories')->result_array() as $key=>$value)            
                array_push($valid, $value['cid']);
            
            if(in_array($category,$valid))
                    return true;
            return false;
	}
}
if ( ! function_exists('valid_yesno'))
{
	function valid_yesno($value)
	{        
            if(in_array($value,array('0','1')))
                    return true;
            return false;
	}
}
if ( ! function_exists('valid_absolutepercent'))
{
	function valid_absolutepercent($value)
	{        
            if(in_array($value,array('absolute','percent')))
                    return true;
            return false;
	}
}
if ( ! function_exists('valid_numeric'))
{
	function valid_numeric($value)
	{        
            if(is_numeric($value))
                    return true;
            return false;
	}
}
if ( ! function_exists('valid_integer'))
{
	function valid_integer($value)
	{        
            if(ctype_digit((string)$value))
                    return true;
            return false;
	}
}