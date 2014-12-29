<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * must     -       order_id,name,active,created_by
 * optional -       created_on,description
 */

class Orders extends CI_Controller {
	
	public function index()
	{
            $this->create();
	}
        public function fetch()
        {
            if(user_logged_in())
            {
                header('Content-type: application/json');
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'order_id';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('orders')))
                {
                    $data['total']=$this->db->get('orders')->num_rows();
                    
                    if($this->input->get_post('filter') && strlen($this->input->get_post('filter'))>0 && in_array($this->input->get_post('filter_by'), $this->db->list_fields('orders')))
                    {                        
                        /* search */
                        if(in_array($this->input->get_post('filter_by'),array('order_id','name','description')))
                        {
                            $this->db->order_by($sort_by, $order);
                            $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                            $data['total']=$this->db->get('orders')->num_rows();
                            
                            $this->db->like($this->input->get_post('filter_by'),$this->input->get_post('filter'),'both');
                        }                        
                    }
                    
                    $this->db->order_by($sort_by, $order);
                    $data['results']=$this->db->get('orders',$limit,$limit*$page)->result_array();                    
                    $data['page']=$page;
                    $data['limit']=$limit;
                    $data['status']='ok';

                    foreach($data['results'] as $key=>$value)
                    {
                        
                    }
                }
                else                
                    $data['status']='invalid_sort_by';
                
                echo json_encode($data);
            }
            else
                echo '<h1>Bad Request</h1>';
            die();
        }
        public function all()
        {
            if(user_logged_in())
            {                
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'order_id';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('orders')))
                {
                    $data['fields']=array(
                        'order_id'=>array('Order ID','10'),
                        'name'=>array('Order','30'),
                        'active'=>array('Active','10'),
                        'created_on'=>array('Created On','15'),
                        'created_by'=>array('Created By','20'),
                        'description'=>array('Description','15')
                    );
                    $data['search_fields']=array(
                        'order_id'=>'Order ID',
                        'name'=>'Order',
                        'description'=>'Description'
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );
                    
                    $data['sort_by']='name';
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;                    

                    //echo '<pre>';print_r($data);echo '</pre>';die();          

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('orders/all',$data);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');                    
                }
            }
            else            
                echo '<h1>Bad Request</h1>';
        }
        
        
        
        public function remove()
        {
            header('Content-type: application/json');
            if(user_logged_in() && user_can('REMOVE_ORDER'))
            {
                if($this->uri->segment(3))
                {
                    $this->db->where('key','POINT_TO_CASH_RATIO');
                    $ratio=$this->db->get('config')->row(0,'object')->value;
                    
                    $this->db->where('key','VAT');
                    $vat=$this->db->get('config')->row(0,'object')->value;                    
                    
                    $generated_id=$this->uri->segment(3);
                    $this->db->where('generated_id',$generated_id);
                    $orders=$this->db->get('orders')->result_array();
                    
                    foreach($orders as $order)
                    {
                        /* Restore Stock */
                        $this->db->where('stid',$order['stid']);
                        $stock=$this->db->get('stocks')->row_array();
                        
                        $stock['quantity'] += $order['quantity'];
                        
                        $this->db->where('stid',$order['stid']);
                        $this->db->update('stocks',$stock);
                        /* End */
                        
                        /* Remove Order */
                        $this->db->delete('orders',array('order_id'=>$order['order_id']));
                        
                        
                        /* Restore Invoice */
                        $invoice_id=$order['invoice_id'];
                        $this->db->where('invoice_id',$invoice_id);
                        $invoice=$this->db->get('invoices')->row_array();
                        
                        $invoice['total_bill'] = $invoice['total_bill'] - $invoice['vat'] + $invoice['discount'] + $invoice['extra_discount'];
                        $invoice['total_bill'] = $invoice['total_bill'] - ($order['total_sale'] + $order['total_discount']);
                        
                        $invoice['vat']=$invoice['total_bill']*$vat/100;
                        
                        $invoice['discount'] -= $order['total_discount'];
                        $invoice['total_bill'] = $invoice['total_bill'] + $invoice['vat'] - $invoice['discount'];
                        
                        $invoice['extra_discount']=0;
                        
                        if($invoice['total_bill']==0)                        
                        {
                            //Good Bye
                            $this->db->delete('invoices',array('invoice_id'=>$invoice['invoice_id']));
                            $data['msg']='Removed Invoice';
                            $data['status']='ok';
                            echo json_encode($data);
                            die();
                        }                        
                        
                        if($invoice['cash_paid'] >= $order['total_sale'])
                        {
                            $invoice['cash_paid'] -= $order['total_sale'];
                            $invoice['change']=$invoice['cash_paid']-$invoice['total_bill'];
                        }
                        else
                        {                            
                            if($invoice['customer_id'] > 0)
                            {
                                $points=($order['total_sale']-$invoice['cash_paid'])*$ratio;
                                
                                $this->db->where('cuid',$invoice['customer_id']);
                                $customer=$this->db->get('customers')->row_array();
                                $customer['points'] += $points;
                                
                                $invoice['points'] -= $points;
                                $invoice['points_converted_to_cash'] = $invoice['points']/$ratio;
                                
                                $this->db->where('cuid',$invoice['customer_id']);
                                $this->db->update('customers',$customer);
                                $invoice['cash_paid']=0;
                                $invoice['change']=0;
                            }
                            else
                            {
                                $invoice['change']=$order['total_sale'] - $invoice['cash_paid'];
                                $invoice['cash_paid']=0;
                            }
                            
                        }
                        $this->db->where('invoice_id',$invoice['invoice_id']);
                        $this->db->update('invoices',$invoice);
                        
                        $data['msg']='Order Successfully Removed';
                        $data['status']='ok';
                        echo json_encode($data);
                        die();
                    }
                    
                }
            }
        }        
}

/* End of file suppliers.php */
/* Location: ./application/controllers/suppliers.php */