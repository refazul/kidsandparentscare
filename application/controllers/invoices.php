<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * must     -       invoice_id
 * optional -
 */

class Invoices extends CI_Controller {

	public function index()
	{
            $this->create();
	}
        public function pos()
        {
            if(user_logged_in() && user_can('CREATE_INVOICE'))
            {
                $data=$_POST['data'];
                $filename=  uniqid().'.txt';
                $file = fopen($filename,"w");
                fwrite($file,$data);
                fclose($file);
                echo $filename;
                die();
            }
        }
        public function fetch()
        {
            if(user_logged_in() && user_can('EDIT_INVOICE') && user_can('REMOVE_INVOICE'))
            {
                header('Content-type: application/json');
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'bill_time';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'desc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 50;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                /*
                 * SELECT invoice_id,U.full_name as billed_by,subtotal,vat,discount,extra_discount,total_bill,bill_time
                 * FROM invoices as I
                 * inner join users as U
                 * on I.billed_by=U.uid
                 * where bill_time>='2014-08-28' order by bill_time desc
                 */


                if($this->input->post('from'))$this->db->where('bill_time >=',$this->input->post('from'));
                if($this->input->post('to'))$this->db->where('bill_time <=',$this->input->post('to'));
                if((int)$this->input->post('active_2')==1)
                {
                    $terminal=$this->input->post('filter_2');
                    if($terminal=='knpc02')
                    {
                        $this->db->where('(billed_by=7 or billed_by=6)',NULL,FALSE);//rony or masudur
                    }
                    else if($terminal=='knpc01')
                    {
                        $this->db->where('billed_by',5);//rabeya
                    }
                    else if($terminal=='knpc03')
                    {
                        $this->db->where('billed_by',8);//ripon
                    }
                    else if($terminal=='knpc04')
                    {
                        $this->db->where('billed_by',12);//rumon
                    }
                }
                $data['total']=$this->db->get('invoices')->num_rows();

                if($this->input->post('from'))$this->db->where('bill_time >=',$this->input->post('from'));
                if($this->input->post('to'))$this->db->where('bill_time <=',$this->input->post('to'));
                if((int)$this->input->post('active_2')==1)
                {
                    $terminal=$this->input->post('filter_2');
                    if($terminal=='knpc02')
                    {
                        $this->db->where('(billed_by=7 or billed_by=6)',NULL,FALSE);//rony or masudur
                    }
                    else if($terminal=='knpc01')
                    {
                        $this->db->where('billed_by',5);//rabeya
                    }
                    else if($terminal=='knpc03')
                    {
                        $this->db->where('billed_by',8);//ripon
                    }
                    else if($terminal=='knpc04')
                    {
                        $this->db->where('billed_by',12);//rumon
                    }
                }
                $this->db->order_by($sort_by, $order);
                $data['results']=$this->db->get('invoices',$limit,$limit*$page)->result_array();
                $data['page']=$page;
                $data['limit']=$limit;
                $data['status']='ok';
                $data['total_total_cost']=0;

                foreach($data['results'] as $key=>$value)
                {
                    $this->db->where('uid',$data['results'][$key]['billed_by']);
                    $data['results'][$key]['billed_by']=$this->db->get('users')->row(0,'object')->full_name;

                    $data['results'][$key]['bill_time']=date('jS F, Y @ h:i A', strtotime($data['results'][$key]['bill_time']));
                    $data['results'][$key]['total_cost']=0;

                    $this->db->where('invoice_id',$data['results'][$key]['invoice_id']);
                    $orders=$this->db->get('orders')->result_array();

                    foreach($orders as $order)
                    {
                        $this->db->where('stid',$order['stid']);
                        $stock=$this->db->get('stocks')->row(0,'object');
                        if($stock)
                        	$data['results'][$key]['total_cost']+=$stock->unit_cost * $order['quantity'];
                    }
                }

                /* Totals */
                $this->db->select('sum(total_bill) as sale,sum(extra_discount+discount) as discount, sum(subtotal) as subtotal');
                $this->db->from('invoices');
                if((int)$this->input->post('active_2')==1)
                {
                    $terminal=$this->input->post('filter_2');
                    if($terminal=='knpc02')
                    {
                        $this->db->where('(billed_by=7 or billed_by=6)',NULL,FALSE);//rony or masudur
                    }
                    else if($terminal=='knpc01')
                    {
                        $this->db->where('billed_by',5);//rabeya
                    }
                    else if($terminal=='knpc03')
                    {
                        $this->db->where('billed_by',8);//ripon
                    }
                    else if($terminal=='knpc04')
                    {
                        $this->db->where('billed_by',12);//rumon
                    }
                }
                if($this->input->post('from'))$this->db->where('bill_time >=',$this->input->post('from'));
                if($this->input->post('to'))$this->db->where('bill_time <=',$this->input->post('to'));
                $total_total=$this->db->get()->row(0,'object');
                $data['total_total_subtotal'] = $total_total->subtotal;
                $data['total_total_sale'] = $total_total->sale;
                $data['total_total_discount'] = $total_total->discount;


                $this->db->select('sum(unit_cost * orders.quantity) as sum');
                $this->db->from('invoices');
                if($this->input->post('from'))$this->db->where('bill_time >=',$this->input->post('from'));
                if($this->input->post('to'))$this->db->where('bill_time <=',$this->input->post('to'));
                $this->db->join('orders', 'invoices.invoice_id = orders.invoice_id');
                $this->db->join('stocks', 'stocks.stid = orders.stid');
                $data['total_total_cost'] = $this->db->get()->row(0,'object')->sum;


                echo json_encode($data);
                die();
            }
            else
                echo '<h1>Bad Request</h1>';
            die();
        }
        public function all()
        {
            if(user_logged_in() && user_can('EDIT_INVOICE'))
            {
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'bill_time';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'desc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 50;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, $this->db->list_fields('invoices')))
                {
                    $data['fields']=array(
                        'generated_id'=>array('Invoice ID','8','left'),
                        'billed_by'=>array('Billed By','8','center'),
                        'customer_id'=>array('Billed To','5','center'),
                        'total_cost'=>array('Total Cost',5),
                        'subtotal'=>array('Subtotal',5),
                        'vat'=>array('VAT',4),
                        'discount'=>array('Discount',5),
                        'extra_discount'=>array('Extra Discount',8),
                        'discount_percent'=>array('Discount %',4),
                        'total_bill'=>array('Total Bill','5'),
                        'bill_time'=>array('Bill Time','15','center'),
                    );
                    $data['sort_fields']=array(
                        'generated_id'=>array('Invoice ID','8','left'),
                        'billed_by'=>array('Billed By','8','center'),
                        'customer_id'=>array('Billed To','5','center'),
                        'subtotal'=>array('Subtotal',5),
                        'vat'=>array('VAT',5),
                        'discount'=>array('Discount',5),
                        'extra_discount'=>array('Extra Discount',8),
                        'total_bill'=>array('Total Bill','5'),
                        'bill_time'=>array('Bill Time','15','center'),
                    );
                    $data['search_fields']=array(
                        'bill_time'=>'Bill Time',
                        'generated_id'=>'Invoice ID'
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );
                    $data['terminals']=array(
                        'knpc01'=>'KNPC - 01',
                        'knpc02'=>'KNPC - 02',
                        'knpc03'=>'KNPC - 03',
                        'knpc04'=>'KNPC - 04'
                    );

                    $data['sort_by']=$sort_by;
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;

                    //echo '<pre>';print_r($data);echo '</pre>';die();

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('invoices/all',$data);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');
                }
            }
            else
                echo '<h1>Bad Request</h1>';
        }
        public function create()
        {
            if(user_logged_in() && user_can('CREATE_INVOICE'))
            {
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'barcode';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 25;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                //if(in_array($sort_by, $this->db->list_fields('products')))
                {
                    $data['fields']=array(
                        'barcode'=>array('Barcode','15'),
                        'name'=>array('Name','15'),
                        'sku'=>array('SKU','10'),
                        'unit'=>array('Unit','5')
                    );
                    $data['search_fields']=array(
                        'barcode'=>'Barcode',
                        'name'=>'Name',
                        'sku'=>'SKU',
                        'department'=>'Department'
                    );
                    $data['visible_fields']=array(
                        'barcode'=>array('Barcode',10),
                        'name'=>array('Name',15),
                        'sku'=>array('SKU',5),
                        'unit'=>array('Unit',5),
                        'stock'=>array('Stock',5),
                        'price'=>array('Price',10)
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );

                    $data['banks']=array(
                        'DBBL'=>'DBBL',
                        'EBL'=>'EBL'
                        );

                    $this->db->where('key','VAT');
                    $vat=$this->db->get('config')->row(0,'object')->value;

                    $data['sort_by']=$sort_by;
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;
                    $data['vat']=$vat;

                    unset($temp);
                    $this->db->where('active',1);
                    $temp=$this->db->get('departments')->result_array();
                    foreach($temp as $key=>$value)
                        $data['departments'][$value['did']]=$value['name'];

                    //echo '<pre>';print_r($data);echo '</pre>';die();
                }
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('invoices/create',$data);
                $this->load->view('wrap_end');
                $this->load->view('footer');
            }
        }
        public function remove()
        {
            header('Content-type: application/json');
            if(user_logged_in() && $this->uri->segment(3))
            {
                $generated_id=$this->uri->segment(3);
                $this->db->where('generated_id',$generated_id);
                if($this->db->get('invoices')->num_rows()>0)
                {
                    $this->db->where('generated_id',$generated_id);
                    $invoice=$this->db->get('invoices')->row_array();
                    $invoice_id=$invoice['invoice_id'];

                    $time1=date_parse($invoice['bill_time']);
                    $time2=date_parse(date("Y-m-d H:i:s"));
                    if(($time1['day']==$time2['day'] && $invoice['billed_by']==$this->session->userdata('uid')) || user_can('REMOVE_INVOICE'))
                    {
                        $this->db->where('invoice_id',$invoice_id);
                        $orders=$this->db->get('orders')->result_array();
                        foreach($orders as $order)
                        {
                            $this->db->where('stid',$order['stid']);
                            $stock=$this->db->get('stocks')->row_array();
							unset($stock['expiry_date']);
                            $stock['quantity'] += $order['quantity'];

                            $this->db->where('stid',$order['stid']);
                            $this->db->update('stocks',$stock);
                        }
                        $this->db->delete('orders',array('invoice_id'=>$invoice_id));
                        $this->db->delete('invoices',array('invoice_id'=>$invoice_id));
                        $this->db->delete('supplementary_invoices',array('generated_id'=>$invoice['generated_id']));

                        //redirect(site_url().'invoices/create', 'refresh');
                        $json['status']='ok';
                        echo json_encode($json);
                        die();
                    }
                    else
                    {
                        $json['status']='unauthorized';
                        echo json_encode($json);
                        die();
                    }
                }

            }
        }
        public function recall()
        {
            header('Content-type: application/json');
            if(user_logged_in() && user_can('CREATE_INVOICE'))
            {
                if($this->input->post('recall_invoice'))
                {
                    $generated_id=$this->input->post('recall_invoice');
                    $this->db->where('generated_id',$generated_id);
                    if($this->db->get('invoices')->num_rows()>0)
                    {
                        $this->db->where('generated_id',$generated_id);
                        $invoice=$this->db->get('invoices')->row_array();

                        $time1=date_parse($invoice['bill_time']);
                        $time2=date_parse(date("Y-m-d H:i:s"));
                        if(($time1['day']==$time2['day'] && $invoice['billed_by']==$this->session->userdata('uid'))|| user_can('EDIT_INVOICE'))
                        {
                            $json['id']=$generated_id;
                            $json['status']='ok';
                            echo json_encode($json);
                            die();
                        }
                    }
                    $json['status']='invalid';
                    echo json_encode($json);
                    die();
                }
            }
        }
        public function get_next_stock($pid)
        {
            $this->db->where('pid',$pid);
            $this->db->where('quantity >',0);
            $this->db->order_by('stocked_on','asc');
            if($this->db->get('stocks')->num_rows()>0)
            {
                $this->db->where('pid',$pid);
                $this->db->where('quantity >',0);
                $this->db->order_by('stocked_on','asc');
                $stock = $this->db->get('stocks')->row_array();
				unset($stock['expiry_date']);
				return $stock;
            }
            else {
                return FALSE;
            }
        }
        public function commit()
        {
			$invoice = array();
			$data = array();
            if(user_logged_in() && user_can('CREATE_INVOICE'))
            {
                if($this->input->post('orders')):

                    $this->db->where('key','VAT');
                    $vat=$this->db->get('config')->row(0,'object')->value;

                    /* Create an Invoice */
                    $invoice['billed_by']=$this->session->userdata('uid');
                    $this->load->model('customer');
                    $customer=$this->customer->getBy('code',$this->input->post('customer'));
                    if($customer===0 || $customer===false)
                        $cuid=0;
                    else
                        $cuid=$customer['cuid'];
                    $invoice['customer_id']=$cuid;
                    $invoice['subtotal']=0;
                    $invoice['vat']=0;
                    $invoice['discount']=0;
                    $invoice['generated_id']=round(microtime(true) * 1000);//strtoupper(uniqid().'x'.$invoice['billed_by'].'y'.$invoice['customer_id']);
                    date_default_timezone_set('Asia/Dhaka');
                    $invoice['bill_time']=date('Y-m-d H:i:s');

                    $this->db->insert('invoices',$invoice);
                    $invoice_id=$this->db->insert_id();

                    foreach($this->input->post('orders') as $order)
                    {
                        $pid=$order['pid'];
                        $barcode=$order['barcode'];
                        $quantity=$order['quantity'];
                        $generated_order_id=strtoupper(uniqid().'x'.$pid.'y'.$quantity);

                        //echo '<pre>';print_r($order);echo '</pre>';die();

                        while($quantity > 0)
                        {
                            $stock=$this->get_next_stock($pid);
                            if($stock==FALSE)
                                break;

                            if($stock['quantity'] >= $quantity)
                            {
                                $stock['quantity'] -= $quantity;
                                $this->db->where('stid',$stock['stid']);
                                $this->db->update('stocks',$stock);

                                $this->db->where('stid',$stock['stid']);
                                $sale=$this->db->get('stocks')->row_array();

                                $data['invoice_id']=$invoice_id;
                                $data['stid']=$stock['stid'];
                                $data['quantity']=$quantity;

                                if($sale['discount_type']=='percent')
                                    $sale['discount_amount']=$sale['unit_sale'] * $sale['discount_amount'] / 100;

                                $data['unit_sale']=$sale['unit_sale'];
                                $data['total_sale']=$sale['unit_sale'] * $quantity;
                                $data['total_discount']=$sale['discount_amount'] * $quantity;
                                $data['generated_id']=$generated_order_id;

                                $this->db->insert('orders',$data);

                                $invoice['subtotal'] += $data['total_sale'];
                                $invoice['discount'] += $data['total_discount'];

                                break;
                            }
                            else
                            {
                                $remainder = $stock['quantity'];
                                $quantity -= $stock['quantity'];
                                $stock['quantity'] = 0;
                                $this->db->where('stid',$stock['stid']);
                                $this->db->update('stocks',$stock);

                                $this->db->where('stid',$stock['stid']);
                                $sale=$this->db->get('stocks')->row_array();

                                $data['invoice_id']=$invoice_id;
                                $data['stid']=$stock['stid'];
                                $data['quantity']=$remainder;

                                if($sale['discount_type']=='percent')
                                    $sale['discount_amount']=$sale['unit_sale'] * $sale['discount_amount'] / 100;

                                $data['unit_sale']=$sale['unit_sale'];
                                $data['total_sale']=$sale['unit_sale'] * $remainder;
                                $data['total_discount']=$sale['discount_amount'] * $remainder;
                                $data['generated_id']=$generated_order_id;

                                $this->db->insert('orders',$data);

                                $invoice['subtotal'] += $data['total_sale'];
                                $invoice['discount'] += $data['total_discount'];
                            }
                        }
                    }
                    $invoice['vat'] = $invoice['subtotal'] * $vat/100;
                    $invoice['extra_discount']=$this->input->post('extra_discount');
                    $invoice['total_bill'] = round($invoice['subtotal'] + $invoice['vat'] - $invoice['discount'] - $invoice['extra_discount']);

                    $this->customer->addPoint($cuid,$invoice['total_bill']*0.02);

                    $invoice['payment_method'] = $this->input->post('payment_method');
                    if($invoice['payment_method']=='card')
                        $invoice['bank']=$this->input->post('bank');

                    $this->db->where('invoice_id',$invoice_id);
                    $this->db->update('invoices',$invoice);

                    $invoice['invoice']=$invoice;

                    //redirect(site_url().'invoices/copy/'.$invoice['generated_id'], 'refresh');
                    $json['id']=$invoice['generated_id'];
                    $json['status']='ok';
                    echo json_encode($json);
                    die();
                endif;
            }
        }
        public function edit()
        {
			$invoice = array();
			$data = array();
            if(user_logged_in() && user_can('CREATE_INVOICE'))
            {
                if($this->uri->segment(3))
                {
                    $generated_id=$this->uri->segment(3);
                    $this->db->where('generated_id',$generated_id);
                    if($this->db->get('invoices')->num_rows()>0)
                    {
                        $this->db->where('generated_id',$generated_id);
                        $invoice=$this->db->get('invoices')->row_array();

                        $this->db->where('invoice_id',$invoice['invoice_id']);
                        $orders=$this->db->get('orders')->result_array();
                        foreach($orders as $key=>$value)
                        {
                            $this->db->where('stid',$value['stid']);
                            $pid=$this->db->get('stocks')->row(0,'object')->pid;

                            $this->db->where('pid',$pid);
                            $product=$this->db->get('products')->row(0,'object');

                            $orders[$key]['pid']=$product->pid;
                            $orders[$key]['barcode']=$product->barcode;
                            $orders[$key]['name']=$product->name;
                            $orders[$key]['discount']=$value['total_discount'];
                        }
                        $invoice['orders']=$orders;

                        $time1=date_parse($invoice['bill_time']);
                        $time2=date_parse(date("Y-m-d H:i:s"));

                        if(($time1['day']==$time2['day'] && $invoice['billed_by']==$this->session->userdata('uid')) || user_can('EDIT_INVOICE'))
                        {
                            $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'pid';
                            $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                            $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 25;
                            $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                            if(in_array($sort_by, $this->db->list_fields('products')))
                            {
                                $data['fields']=array(
                                    'barcode'=>array('Barcode','15'),
                                    'name'=>array('Name','15'),
                                    'sku'=>array('SKU','10'),
                                    'unit'=>array('Unit','5')
                                );
                                $data['search_fields']=array(
                                    'barcode'=>'Barcode',
                                    'name'=>'Name',
                                    'sku'=>'SKU',
                                    'department'=>'Department'
                                );
                                $data['visible_fields']=array(
                                    'barcode'=>array('Barcode',10),
                                    'name'=>array('Name',15),
                                    'sku'=>array('SKU',5),
                                    'unit'=>array('Unit',5),
                                    'stock'=>array('Stock',5),
                                    'price'=>array('Price',10)
                                );
                                $data['orders']=array(
                                    'asc'=>'Ascending',
                                    'desc'=>'Descending'
                                );

                                $data['banks']=array(
                                    'DBBL'=>'DBBL',
                                    'EBL'=>'EBL'
                                    );

                                $this->db->where('key','VAT');
                                $vat=$this->db->get('config')->row(0,'object')->value;

                                $data['sort_by']='barcode';
                                $data['order']=$order;
                                $data['limit']=$limit;
                                $data['page']=$page;
                                $data['vat']=$vat;

                                $this->db->where('generated_id',$invoice['generated_id']);
                                if($this->db->get('supplementary_invoices')->num_rows()>0)
                                {
                                    $this->db->where('generated_id',$invoice['generated_id']);
                                    $supplementary=$this->db->get('supplementary_invoices')->row_array();
                                    $invoice['supplementary']['cash']=$supplementary['mergeable_cash'];
                                    $invoice['supplementary']['time']=date('jS F, Y @ h:i A', strtotime($supplementary['time']));
                                }
                                $this->db->where('uid',$invoice['billed_by']);
                                $invoice['billed_by_name']=$this->db->get('users')->row(0,'object')->full_name;

                                $data['invoice']=$invoice;

                                //echo '<pre>';print_r($data);echo '</pre>';die();
                            }
                            unset($temp);
                            $this->db->where('active',1);
                            $temp=$this->db->get('departments')->result_array();
                            foreach($temp as $key=>$value)
                                $data['departments'][$value['did']]=$value['name'];

                            $this->load->view('header');
                            $this->load->view('menu');
                            $this->load->view('wrap_begin');
                            $this->load->view('invoices/edit',$data);
                            $this->load->view('wrap_end');
                            $this->load->view('footer');
                        }
                    }
                }
            }
        }
        public function copy()
        {
            if($this->uri->segment(3))
            {
                $generated_id=$this->uri->segment(3);
                $this->db->where('generated_id',$generated_id);
                if($this->db->get('invoices')->num_rows()>0)
                {
                    $this->db->where('generated_id',$generated_id);
                    $invoice=$this->db->get('invoices')->row_array();

                    $this->db->where('invoice_id',$invoice['invoice_id']);
                    $orders=$this->db->get('orders')->result_array();
                    $invoice['orders']=$orders;

                    $data['invoice']=$invoice;

                    $this->load->view('invoices/print',$data);
                }
            }
        }

        public function ajax()
        {
            header('Content-type: application/json');
            if($this->input->get_post('intent'))
            {
                $intent=$this->input->get_post('intent');
                if($intent=='edit' && user_logged_in() && user_can('CREATE_INVOICE'))
                {



                    /* Fetch Invoice ID */
                    $request_from=$_SERVER['HTTP_REFERER'];
                    $generated_id=end(explode('/',$request_from));
                    $this->db->where('generated_id',$generated_id);
                    $invoice=$this->db->get('invoices')->row_array();
                    $invoice_id=$invoice['invoice_id'];



                    /* Allow & Restrict */
                    $previous_total=$invoice['total_bill'];
                    $present_total=$this->input->post('t');
                    $time1=date_parse($invoice['bill_time']);
                    $time2=date_parse(date("Y-m-d H:i:s"));



                    /* Restrict Rule 1 */
                    if(($present_total < $previous_total) && ($time1['day'] != $time2['day']))
                    {
                        $json['threshold']=$previous_total;
                        $json['status']='invalid';
                        $json['rule']=1;
                        echo json_encode($json);
                        die();
                    }



                    /* Restrict Rule 2 */
                    if( !(($time1['day']==$time1['day'] && $invoice['billed_by']==$this->session->userdata('uid')) || user_can('EDIT_INVOICE')) )
                    {
                        $json['status']='unauthorized';
                        $json['rule']=2;
                        echo json_encode($json);
                        die();
                    }



                    /* Reset */
                    $invoice['subtotal']=0;
                    $invoice['vat']=0;
                    $invoice['discount']=0;
                    $invoice['total_bill']=0;
                    $invoice['extra_discount']=$this->input->post('extra_discount');
                    $invoice['payment_method'] = $this->input->post('payment_method');
                    if($invoice['payment_method']=='card')$invoice['bank']=$this->input->post('bank');



                    if($this->input->post('orders'))
                    {
                        /* VAT */
                        $this->db->where('key','VAT');
                        $vat=$this->db->get('config')->row(0,'object')->value;



                        /* Restore the Stocks */
                        $this->db->where('invoice_id',$invoice['invoice_id']);
                        $orders=$this->db->get('orders')->result_array();
                        foreach($orders as $order)
                        {
                            $this->db->where('stid',$order['stid']);
                            $stock=$this->db->get('stocks')->row_array();
							unset($stock['expiry_date']);
                            $stock['quantity'] += $order['quantity'];

                            $this->db->where('stid',$order['stid']);
                            $this->db->update('stocks',$stock);

                            $this->db->delete('orders',array('order_id'=>$order['order_id']));
                        }



                        /* Create new Bill */
                        foreach($this->input->post('orders') as $order)
                        {
                            $pid=$order['pid'];
                            $barcode=$order['barcode'];
                            $quantity=$order['quantity'];
                            $generated_order_id=strtoupper(uniqid().'x'.$pid.'y'.$quantity);

                            //echo '<pre>';print_r($order);echo '</pre>';

                            while($quantity > 0)
                            {
                                $stock=$this->get_next_stock($pid);
                                if($stock==FALSE)
                                    break;

                                if($stock['quantity'] >= $quantity)
                                {
                                    $stock['quantity'] -= $quantity;
                                    $this->db->where('stid',$stock['stid']);
                                    $this->db->update('stocks',$stock);

                                    $this->db->where('stid',$stock['stid']);
                                    $sale=$this->db->get('stocks')->row_array();

                                    $data['invoice_id']=$invoice_id;
                                    $data['stid']=$stock['stid'];
                                    $data['quantity']=$quantity;

                                    if($sale['discount_type']=='percent')
                                        $sale['discount_amount']=$sale['unit_sale'] * $sale['discount_amount'] / 100;

                                    $data['unit_sale']=$sale['unit_sale'];
                                    $data['total_sale']=$sale['unit_sale'] * $quantity;
                                    $data['total_discount']=$sale['discount_amount'] * $quantity;
                                    $data['generated_id']=$generated_order_id;

                                    $this->db->insert('orders',$data);

                                    $invoice['subtotal'] += $data['total_sale'];
                                    $invoice['discount'] += $data['total_discount'];

                                    break;
                                }
                                else
                                {
                                    $remainder = $stock['quantity'];
                                    $quantity -= $stock['quantity'];
                                    $stock['quantity'] = 0;
                                    $this->db->where('stid',$stock['stid']);
                                    $this->db->update('stocks',$stock);

                                    $this->db->where('stid',$stock['stid']);
                                    $sale=$this->db->get('stocks')->row_array();

                                    $data['invoice_id']=$invoice_id;
                                    $data['stid']=$stock['stid'];
                                    $data['quantity']=$remainder;

                                    if($sale['discount_type']=='percent')
                                        $sale['discount_amount']=$sale['unit_sale'] * $sale['discount_amount'] / 100;

                                    $data['unit_sale']=$sale['unit_sale'];
                                    $data['total_sale']=$sale['unit_sale'] * $remainder;
                                    $data['total_discount']=$sale['discount_amount'] * $remainder;
                                    $data['generated_id']=$generated_order_id;

                                    $this->db->insert('orders',$data);

                                    $invoice['subtotal'] += $data['total_sale'];
                                    $invoice['discount'] += $data['total_discount'];
                                }
                            }
                        }
                        $invoice['vat'] = $invoice['subtotal'] * $vat/100;
                        $invoice['total_bill'] = round($invoice['subtotal'] + $invoice['vat'] - $invoice['discount'] - $invoice['extra_discount']);
                        //$invoice['billed_by']=$this->session->userdata('uid');

                        $this->db->where('invoice_id',$invoice_id);
                        $this->db->update('invoices',$invoice);

                        if(($invoice['total_bill']-$previous_total)>0 && $time1['day']!=$time2['day'])
                        {
                            $supplementary['mergeable_cash']=$invoice['total_bill']-$previous_total;
                            $supplementary['generated_id']=$invoice['generated_id'];

                            $this->db->insert('supplementary_invoices',$supplementary);
                        }

                        $invoice['invoice']=$invoice;

                        //redirect(site_url().'invoices/copy/'.$invoice['generated_id'], 'refresh');
                        $json['id']=$invoice['generated_id'];
                        $json['status']='ok';
                        echo json_encode($json);
                        die();
                    }
                    else
                    {
                        $json['id']=$invoice['generated_id'];
                        $json['status']='ok';
                        echo json_encode($json);
                        die();
                    }
                }
                else
                {
                    $json['status']='unauthorized_access';
                    echo json_encode($json);
                }
            }
            else
            {
                $json['status']='no_intent';
                echo json_encode($json);
            }
            die();
        }
}

/* End of file invoices.php */
/* Location: ./application/controllers/departments.php */
