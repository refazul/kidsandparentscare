<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * sandbox
 */

class Sandbox extends CI_Controller
{
    public function test()
    {
        $this->db->where('generated_id','1410755476639');
        $invoice=$this->db->get('invoices')->row_array();
        $invoice_id=$invoice['invoice_id'];
        date_default_timezone_set('Asia/Dhaka');
        $time1=date_parse($invoice['bill_time']);
        $time2=date_parse(date("Y-m-d H:i:s"));
        print_r($time1);
        print_r($time2);
    }
    public function index()
    {
        $this->load->model('Customer');
        echo $this->Customer->getName(1);
        echo $this->Customer->setName(1,'Refazul');
    }
    public function z1()
    {
        $this->db->select('A.barcode as barcode',FALSE);
        $this->db->select('A.name as name',FALSE);
        $this->db->select('A.sku as sku',FALSE);
        $this->db->select('A.unit as unit',FALSE);
        $this->db->select('B.Q as stock',FALSE);
        $this->db->select('B.P as price',FALSE);
        $this->db->from('products as A');
        $this->db->join('(select pid,sum(quantity)as Q,max(unit_sale) as P from stocks group by pid having Q>0) as B','A.pid = B.pid');
        $this->db->where('A.review',1);
        $this->db->order_by('barcode','asc');
        $temp=$this->db->get()->result_array();
        echo count($temp);
        echo '<pre>';print_r($temp);echo '</pre>';
    }
    public function z2()
    {
        $this->db->where('barcode','');
        $products=$this->db->get('products')->result_array();
        $start=substr(round(microtime(true) * 1000),0,12);
        foreach($products as $key=>$value)
        {
            $products[$key]['barcode']=$start++;
            $this->db->where('pid',$products[$key]['pid']);
            unset($products[$key]['pid']);
            $this->db->update('products',$products[$key]);
        }
        echo '<pre>';
        print_r($products);
        echo '</pre>';

    }
}

/* End of file sandbox.php */
/* Location: ./application/controllers/sandbox.php */