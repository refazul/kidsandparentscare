<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller {
	
        public function index()
	{
            $this->stockentry();
	}
        public function mark()
        {
            if(user_logged_in() && user_can('GENERATE_REPORT'))
            {
                date_default_timezone_set('Asia/Dhaka');
                $data['time']=date('Y-m-d H:i:s');
                $total_cost=$this->db->query('SELECT sum(unit_cost * (quantity+store_quantity)) as total_cost from stocks INNER JOIN products ON stocks.pid = products.pid WHERE products.review = 1')->row_array();
                $total_sale=$this->db->query('SELECT sum(unit_sale * (quantity+store_quantity)) as total_sale from stocks INNER JOIN products ON stocks.pid = products.pid WHERE products.review = 1')->row_array();

                $data['total_cost']=$total_cost['total_cost'];
                $data['total_sale']=$total_sale['total_sale'];
                $data['potential_profit']=$total_sale['total_sale']-$total_cost['total_cost'];
                print_r($data);
                
                /*
                
                $this->db->where('date(time) = curdate()');
                if($this->db->get('currentstock')->num_rows()>0)
                {
                    $this->db->where('date(time) = curdate()');
                    $id=$this->db->get('currentstock')->row(0,'object')->id;
                    
                    $this->db->where('id',$id);
                    $this->db->update('currentstock',$data);
                    
                    $json['id']=$id;
                    $json['status']='upate';
                    echo json_encode($json);
                    die();
                }
                else
                {
                 * 
                 */
                    $this->db->insert('currentstock',$data);
                    $new_id=$this->db->insert_id();
                    $json['id']=$new_id;
                    $json['status']='insert';
                    echo json_encode($json);
                    die();
                /*
                 * }
                 */
            }
        }
        public function viewPdf()
        {
            if(user_logged_in() && user_can('GENERATE_REPORT'))
            {
                $data['active_1']=$this->input->post('active_1');
                $data['active_2']=$this->input->post('active_2');
                $data['filter_1']=$this->input->post('filter_1');
                $data['filter_2']=$this->input->post('filter_2');
                $data['sort_by']=$this->input->post('sort_by');
                $data['order']=$this->input->post('order');
                $data['from']=$this->input->post('from');
                $data['to']=$this->input->post('to');
                //header('Content-type: application/json');
                //echo json_encode($data);
                //die();
                $this->db->select('*');
                $this->db->from('stocks');
                if($this->input->post('active_1')==true)$this->db->where('products.department',$this->input->post('filter_1'));
                if($this->input->post('active_2')==true)$this->db->where('stocks.sid',$this->input->post('filter_2'));
                if($this->input->post('from')!=false)$this->db->where('stocks.stocked_on >=',$this->input->post('from'));
                if($this->input->post('to')!=false)$this->db->where('stocks.stocked_on <=',$this->input->post('to'));                        
                $this->db->join('products', 'stocks.pid = products.pid');
                $this->db->join('departments', 'products.department = departments.did');
                $this->db->join('suppliers', 'stocks.sid = suppliers.sid');

                if($this->input->post('sort_by')=='sid')$this->db->order_by('suppliers.name',$this->input->post('order'));
                else if($this->input->post('sort_by')=='department')$this->db->order_by('departments.name',$this->input->post('order'));
                else $this->db->order_by($this->input->post('sort_by'),$this->input->post('order'));
                
                //$this->db->limit($limit, $limit*$page);
                $data['results'] = $this->db->get()->result_array();
                $data['total']=  sizeof($data['results']);
                $data['status']='ok';

                foreach($data['results'] as $key=>$value)
                {
                    $this->db->where('sid',$data['results'][$key]['sid']);
                    $data['results'][$key]['supplier']=$this->db->get('suppliers')->row(0,'object')->name;

                    $this->db->where('pid',$data['results'][$key]['pid']);
                    $product=$this->db->get('products')->row_array();
                    $data['results'][$key]['sku']=$product['sku'];

                    $this->db->where('did',$product['department']);
                    $data['results'][$key]['department']=$this->db->get('departments')->row(0,'object')->name;

                    $data['results'][$key]['total_cost']=number_format((float)$data['results'][$key]['unit_cost']*$data['results'][$key]['base_quantity'], 2, '.', '');
                    $data['results'][$key]['total_sale']=number_format((float)$data['results'][$key]['unit_sale']*$data['results'][$key]['base_quantity'], 2, '.', '');

                    $data['results'][$key]['stocked_on']=date('jS F, Y @ h:i A', strtotime($data['results'][$key]['stocked_on']));
                }
                
                /* Totals */
                
                $data['total_total_cost']=0;
                $data['total_total_price']=0;
                
                
                //$data['active_1']=$this->input->post('active_1');
                //$data['active_2']=$this->input->post('active_2');
                //$data['filter_1']=$this->input->post('filter_1');
                //$data['filter_2']=$this->input->post('filter_2');
                //$data['sort_by']=$this->input->post('sort_by');
                //$data['order']=$this->input->post('order');
                //$data['from']=$this->input->post('from');
                //$data['to']=$this->input->post('to');
                //header('Content-type: application/json');
                //echo json_encode($data);
                //die();
                if(sizeof($data['results']>0))
                {
                    $tempDir=FCPATH.'assets'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
                    $doc = new DOMDocument('1.0');
                    // we want a nice output
                    $doc->formatOutput = true;

                    $root = $doc->createElement('html');
                    $root = $doc->appendChild($root);

                    $head = $doc->createElement('head');
                    $head = $root->appendChild($head);

                    $layout = $doc->createElement('link');
                    $layout->setAttribute('rel', 'stylesheet');
                    $layout->setAttribute('href',asset_url().'css/layout.css');
                    $layout = $head->appendChild($layout);

                    $body = $doc->createElement('body');
                    $body = $root->appendChild($body);

                    //$div = $doc->createElement('div');
                    //$div = $body->appendChild($div);
                    //$div->setAttribute('style','font-size: 20px;font-family: monospace;text-align:right;float:right;clear:right;margin-right:5%;');
                    //$text = $doc->createTextNode($page);
                    //$text = $div->appendChild($text);

                    $div = $doc->createElement('div');
                    $div = $body->appendChild($div);
                    $div->setAttribute('style','font-size: 40px;font-family: monospace;text-align:center;font-weight:bold;');
                    $text = $doc->createTextNode('Kids & Parents Care');
                    $text = $div->appendChild($text);

                    $div = $doc->createElement('div');
                    $div = $body->appendChild($div);
                    $div->setAttribute('style','font-size: 30px;font-family: monospace;text-align:center;');
                    $text = $doc->createTextNode('Stock Entry Report');
                    $text = $div->appendChild($text);

                    $data['file']='';

                    if($this->input->post('from'))
                    {
                        $div = $doc->createElement('div');
                        $div = $body->appendChild($div);
                        $div->setAttribute('style','font-size: 15px;font-family: monospace;text-align:right;float:right;clear:right;margin-right:5%;margin-top:20px;');
                        $text = $doc->createTextNode('From : '.date('jS F, Y',strtotime($this->input->post('from'))));
                        $text = $div->appendChild($text);
                        
                        $data['file'].='_From_'.date('jSFY', strtotime($this->input->post('from')));
                    }
                    if($this->input->post('active_1')==true)
                    {
                        $div = $doc->createElement('div');
                        $div = $body->appendChild($div);
                        $div->setAttribute('style','font-size: 15px;font-family: monospace;text-align:left;float:left;clear:left;margin-left:5%;margin-top:20px;');
                        $text = $doc->createTextNode('Department : '.$data['results'][0]['department']);
                        $text = $div->appendChild($text);
                    }
                    if($this->input->post('to'))
                    {
                        $div = $doc->createElement('div');
                        $div = $body->appendChild($div);
                        $div->setAttribute('style','font-size: 15px;font-family: monospace;text-align:right;float:right;clear:right;margin-right:5%;');
                        $text = $doc->createTextNode('Before : '.date('jS F, Y',strtotime($this->input->post('to'))));
                        $text = $div->appendChild($text);
                        
                        $data['file'].='_Before_'.date('jSFY', strtotime($this->input->post('to')));
                    }                        
                    if($this->input->post('active_2')==true)
                    {
                        $div = $doc->createElement('div');
                        $div = $body->appendChild($div);
                        $div->setAttribute('style','font-size: 15px;font-family: monospace;text-align:left;float:left;clear:left;margin-left:5%;');
                        $text = $doc->createTextNode('Supplier : '.$data['results'][0]['supplier']);
                        $text = $div->appendChild($text);
                    }
                    $div = $doc->createElement('div');
                    $div = $body->appendChild($div);
                    $div->setAttribute('style','height:20px;clear:both;');


                    $table = $doc->createElement('table');
                    $table->setAttribute('class', 'tablesorter');
                    $table->setAttribute('style', 'width:90%;margin:auto;');
                    $table = $body->appendChild($table);

                    $display_keys=array(
                        'stid'=>array('label'=>'Stock ID','style'=>''),
                        'barcode'=>array('label'=>'Barcode','style'=>''),
                        'name'=>array('label'=>'Item','style'=>''),
                        'sku'=>array('label'=>'SKU','style'=>''),
                        //'unit_cost'=>array('label'=>'Unit Cost','style'=>'text-align:right;'),
                        'unit_sale'=>array('label'=>'Unit Price','style'=>'text-align:right;'),
                        'base_quantity'=>array('label'=>'Quantity','style'=>'text-align:right;'),
                        //'total_cost'=>array('label'=>'Total Cost','style'=>'text-align:right;'),
                        'total_sale'=>array('label'=>'Total Price','style'=>'text-align:right;'),
                        'stocked_on'=>array('label'=>'Entry Time','style'=>'text-align:right;')
                        );
                    if($this->input->post('include_cost')==1)
                    {
                        $display_keys=array(
                        'stid'=>array('label'=>'Stock ID','style'=>''),
                        'barcode'=>array('label'=>'Barcode','style'=>''),
                        'name'=>array('label'=>'Item','style'=>''),
                        'sku'=>array('label'=>'SKU','style'=>''),
                        'unit_cost'=>array('label'=>'Unit Cost','style'=>'text-align:right;'),
                        'unit_sale'=>array('label'=>'Unit Price','style'=>'text-align:right;'),
                        'base_quantity'=>array('label'=>'Quantity','style'=>'text-align:right;'),
                        'total_cost'=>array('label'=>'Total Cost','style'=>'text-align:right;'),
                        'total_sale'=>array('label'=>'Total Price','style'=>'text-align:right;'),
                        'stocked_on'=>array('label'=>'Entry Time','style'=>'text-align:right;')
                        );
                    }
                    
                    $tr=$doc->createElement('tr');
                    $tr=$table->appendChild($tr);

                    foreach($display_keys as $key=>$value)
                    {
                        $th=$doc->createElement('th');
                        $th=$tr->appendChild($th);
                        $th->setAttribute('style',$value['style']);

                        $text=$doc->createTextNode($value['label']);
                        $text=$th->appendChild($text);
                    }


                    $i=0;
                    foreach($data['results'] as $result)
                    {
                        $tr=$doc->createElement('tr');
                        $tr=$table->appendChild($tr);
                        if($i%2==0)
                            $tr->setAttribute('class','even');
                        else
                            $tr->setAttribute('class','odd');

                        $keys=array_keys($result);
                        foreach($display_keys as $key=>$value)
                        {
                            $td=$doc->createElement('td');
                            $td=$tr->appendChild($td);
                            $td->setAttribute('style',$value['style']);

                            $text=$doc->createTextNode($result[$key]);
                            $text=$td->appendChild($text);
                        }
                        $i++;
                        $data['total_total_cost']+=$result['total_cost'];
                        $data['total_total_price']+=$result['total_sale'];
                    }
                    
                    if($this->input->post('include_cost')==1)
                    {
                        $div = $doc->createElement('div');
                        $div = $body->appendChild($div);
                        $div->setAttribute('style','font-size: 15px;font-family: monospace;text-align:right;margin-right:5%;margin-left:5%;border-top:1px solid #ccc;margin-top: 25px;padding-top: 5px;');
                        $text = $doc->createTextNode('Total Cost : '.$data['total_total_cost']);
                        $text = $div->appendChild($text);
                        
                        $div = $doc->createElement('div');
                        $div = $body->appendChild($div);
                        $div->setAttribute('style','font-size: 15px;font-family: monospace;text-align:right;margin-right:5%;');
                        $text = $doc->createTextNode('Total Price : '.$data['total_total_price']);
                        $text = $div->appendChild($text);
                    }
                    else
                    {
                        $div = $doc->createElement('div');
                        $div = $body->appendChild($div);
                        $div->setAttribute('style','font-size: 15px;font-family: monospace;text-align:right;margin-right:5%;margin-left:5%;border-top:1px solid #ccc;margin-top: 25px;padding-top: 5px;');
                        $text = $doc->createTextNode('Total Price : '.$data['total_total_price']);
                        $text = $div->appendChild($text);
                    }

                    

                    if($this->input->post('active_1'))$data['file'].='_Dep_'.preg_replace('/[^a-zA-Z0-9]+/', '_', $data['results'][0]['department']);
                    if($this->input->post('active_2'))$data['file'].='_Sup_'.preg_replace('/[^a-zA-Z0-9]+/', '_', $data['results'][0]['supplier']);
                    if($this->input->post('include_cost')!=1)$data['file'].='_without_cost';
                    
                    $doc->saveHTMLFile($tempDir.$data['file'].'.html');
                }
                
                header('Content-type: application/json');
                echo json_encode($data);
                die();
            }
        }
        public function fetch()
        {
            if(user_logged_in() && user_can('GENERATE_REPORT') && $this->input->post('type')=='stockentry')
            {
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'stocked_on';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 50;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, array('stocked_on','stid','sku','sid','department','unit_cost','unit_sale','base_quantity')))
                {
                    /* Search */                      
                    if(in_array($this->input->get_post('filter_by_1'),array('did')) && in_array($this->input->get_post('filter_by_2'),array('sid')))
                    {
                        $this->db->select('*');
                        $this->db->from('stocks');
                        if((int)$this->input->post('active_1')==1)$this->db->where('products.department',$this->input->post('filter_1'));
                        if((int)$this->input->post('active_2')==1)$this->db->where('stocks.sid',$this->input->post('filter_2'));
                        if($this->input->post('from'))$this->db->where('stocks.stocked_on >=',$this->input->post('from'));
                        if($this->input->post('to'))$this->db->where('stocks.stocked_on <=',$this->input->post('to'));
                        $this->db->join('products', 'stocks.pid = products.pid');
                        $data['total'] = $this->db->get()->num_rows();
                        
                        $this->db->select('*');
                        $this->db->from('stocks');
                        if((int)$this->input->post('active_1')==1)$this->db->where('products.department',$this->input->post('filter_1'));
                        if((int)$this->input->post('active_2')==1)$this->db->where('stocks.sid',$this->input->post('filter_2'));
                        if($this->input->post('from'))$this->db->where('stocks.stocked_on >=',$this->input->post('from'));
                        if($this->input->post('to'))$this->db->where('stocks.stocked_on <=',$this->input->post('to'));                        
                        $this->db->join('products', 'stocks.pid = products.pid');
                        
                        $this->db->order_by($sort_by, $order);
                        $this->db->limit($limit, $limit*$page);
                        $data['results'] = $this->db->get()->result_array();
                        $data['page']=$page;
                        $data['limit']=$limit;
                        $data['status']='ok';
                        
                        foreach($data['results'] as $key=>$value)
                        {
                            $this->db->where('sid',$data['results'][$key]['sid']);
                            $data['results'][$key]['supplier']=$this->db->get('suppliers')->row(0,'object')->name;

                            $this->db->where('pid',$data['results'][$key]['pid']);
                            $product=$this->db->get('products')->row_array();
                            $data['results'][$key]['sku']=$product['sku'];

                            $this->db->where('did',$product['department']);
                            $data['results'][$key]['department']=$this->db->get('departments')->row(0,'object')->name;
                            
                            $data['results'][$key]['total_cost']=number_format((float)$data['results'][$key]['unit_cost']*$data['results'][$key]['base_quantity'], 2, '.', '');
                            $data['results'][$key]['total_sale']=number_format((float)$data['results'][$key]['unit_sale']*$data['results'][$key]['base_quantity'], 2, '.', '');

                            $data['results'][$key]['stocked_on']=date('jS F, Y @ h:i A', strtotime($data['results'][$key]['stocked_on']));
                        }
                        
                        /* Totals */
                        $this->db->select('sum(unit_cost * base_quantity) as cost, sum(unit_sale * base_quantity) as price');
                        $this->db->from('stocks');
                        if((int)$this->input->post('active_1')==1)$this->db->where('products.department',$this->input->post('filter_1'));
                        if((int)$this->input->post('active_2')==1)$this->db->where('stocks.sid',$this->input->post('filter_2'));
                        if($this->input->post('from'))$this->db->where('stocks.stocked_on >=',$this->input->post('from'));
                        if($this->input->post('to'))$this->db->where('stocks.stocked_on <=',$this->input->post('to'));
                        $this->db->join('products', 'stocks.pid = products.pid');
                        $total_total=$this->db->get()->row(0,'object');
                        $data['total_total_cost']=$total_total->cost;
                        $data['total_total_price']=$total_total->price;
                        

                        header('Content-type: application/json');
                        echo json_encode($data);
                        die();
                    }
                    else
                    {
                        header('Content-type: application/json');
                        $data['status']='invalid_filter_by';
                        echo json_encode($data);
                        die();
                    }
                }
                else
                {
                    header('Content-type: application/json');
                    $data['status']='invalid_sort_by';
                    echo json_encode($data);
                    die();
                }
            }
            else if(user_logged_in() && user_can('GENERATE_REPORT') && $this->input->post('type')=='combined_sellinfo')
            {
                header('Content-type: application/json');
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'name';
                $order_by=$this->input->get_post('order') ? $this->input->get_post('order') : 'asc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, array('name','sku','department','supplier','total_cost','total_sale','quantity')))
                {
                    /* Search */                      
                    if(in_array($this->input->get_post('filter_by_1'),array('sku','name')) && in_array($this->input->get_post('filter_by_2'),array('did')) && in_array($this->input->get_post('filter_by_3'),array('sid')))
                    {
                        if($this->input->post('from'))$this->db->where('invoices.bill_time >=',$this->input->post('from'));
                        if($this->input->post('to'))$this->db->where('invoices.bill_time <=',$this->input->post('to'));
                        $invoices=$this->db->get('invoices')->result_array();
                        
                        $sales=array();

                        foreach($invoices as $invoice)
                        {
                            $this->db->where('invoice_id',$invoice['invoice_id']);
                            $orders=$this->db->get('orders')->result_array();

                            foreach($orders as $order)
                            {
                                $this->db->where('stid',$order['stid']);
                                $stock=$this->db->get('stocks')->row_array();
                                
                                $index=$stock['pid'];
                                
                                if(isset($sales[$index]))
                                {
                                    $this->db->where('pid',$stock['pid']);
                                    $t=$this->db->get('products')->row_array();
                                    
                                    $sales[$index]['pid']=$stock['pid'];
                                    $sales[$index]['barcode']=$t['barcode'];
                                    $sales[$index]['sku']=$t['sku'];
                                    $sales[$index]['name']=$t['name'];
                                    
                                    $this->db->where('did',$t['department']);
                                    $sales[$index]['department']=$this->db->get('departments')->row(0,'object')->name;
                                    
                                    $this->db->where('sid',$stock['sid']);
                                    $sales[$index]['supplier']=$this->db->get('suppliers')->row(0,'object')->name;
                                    
                                    $sales[$index]['sid']=$stock['sid'];
                                    $sales[$index]['quantity']+=$order['quantity'];
                                    $sales[$index]['total_cost']+=$stock['unit_cost']*$order['quantity'];
                                    $sales[$index]['total_sale']+=$order['total_sale']-$order['total_discount'];
                                }
                                else
                                {
                                    $this->db->where('pid',$stock['pid']);
                                    $t=$this->db->get('products')->row_array();
                                    
                                    $sales[$index]['pid']=$stock['pid'];
                                    $sales[$index]['barcode']=$t['barcode'];
                                    $sales[$index]['sku']=$t['sku'];
                                    $sales[$index]['name']=$t['name'];
                                    
                                    $this->db->where('did',$t['department']);
                                    $sales[$index]['department']=$this->db->get('departments')->row(0,'object')->name;
                                    
                                    $this->db->where('sid',$stock['sid']);
                                    $sales[$index]['supplier']=$this->db->get('suppliers')->row(0,'object')->name;
                                    
                                    $sales[$index]['sid']=$stock['sid'];
                                    $sales[$index]['quantity']=$order['quantity'];
                                    $sales[$index]['total_cost']=$stock['unit_cost']*$order['quantity'];
                                    $sales[$index]['total_sale']=$order['total_sale']-$order['total_discount'];
                                }
                            }
                        }
                        if(sizeof($sales)>0)
                        {
                            if((int)$this->input->post('active_1')==1)
                            {
                                if($this->input->post('filter_1'))
                                {
                                    if($this->input->post('filter_by_1')=='sku')
                                    {
                                        $this->db->like('sku',$this->input->post('filter_1'),'both');
                                        $results=$this->db->get('products')->result_array();
                                        $pid_pool=array();
                                        foreach($results as $result)
                                            array_push($pid_pool,$result['pid']);

                                        foreach($sales as $key=>$value)
                                        {
                                            if(!in_array($key, $pid_pool))
                                                unset($sales[$key]);
                                        }

                                    }
                                    else if($this->input->post('filter_by_1')=='name')
                                    {
                                        $this->db->like('name',$this->input->post('filter_1'),'both');
                                        $results=$this->db->get('products')->result_array();
                                        $pid_pool=array();
                                        foreach($results as $result)
                                            array_push($pid_pool,$result['pid']);

                                        foreach($sales as $key=>$value)
                                        {
                                            if(!in_array($key, $pid_pool))
                                                unset($sales[$key]);
                                        }
                                    }
                                }
                            }
                        }
                        if(sizeof($sales)>0)
                        {
                            if((int)$this->input->post('active_2')==1)
                            {
                                if($this->input->post('filter_2'))
                                {
                                    $this->db->where('department',$this->input->post('filter_2'));
                                    $results=$this->db->get('products')->result_array();
                                    $pid_pool=array();
                                    foreach($results as $result)
                                        array_push($pid_pool,$result['pid']);

                                    foreach($sales as $key=>$value)
                                    {
                                        if(!in_array($key, $pid_pool))
                                            unset($sales[$key]);
                                    }
                                }
                            }
                        }
                        if(sizeof($sales)>0)
                        {
                            if((int)$this->input->post('active_3')==1)
                            {
                                if($this->input->post('filter_3'))
                                {
                                    foreach($sales as $key=>$value)
                                    {
                                        if($sales[$key]['sid'] != $this->input->post('filter_3'))
                                            unset($sales[$key]);
                                    }
                                }
                            }
                        }
                        //echo '<pre>';print_r($sales);echo '</pre>';die();
                        
                        if(sizeof($sales)>0)
                        {
                            if($order_by=='asc')
                            {
                                if($sort_by=='name')usort($sales, function($a, $b) {return strcmp($a["name"], $b["name"]);});
                                else if($sort_by=='sku')usort($sales, function($a, $b) {return strcmp($a["sku"], $b["sku"]);});
                                else if($sort_by=='department')usort($sales, function($a, $b) {return strcmp($a["department"],$b["department"]);});
                                else if($sort_by=='supplier')usort($sales, function($a, $b) {return strcmp($a["supplier"],$b["supplier"]);});
                                else if($sort_by=='total_cost')usort($sales, function($a, $b) {return ($a["total_cost"]-$b["total_cost"]);});
                                else if($sort_by=='total_sale')usort($sales, function($a, $b) {return ($a["total_sale"]-$b["total_sale"]);});
                                else if($sort_by=='quantity')usort($sales, function($a, $b) {return ($a["quantity"]-$b["quantity"]);});
                            }
                            else
                            {
                                if($sort_by=='name')usort($sales, function($a, $b) {return strcmp($b["name"], $a["name"]);});
                                else if($sort_by=='sku')usort($sales, function($a, $b) {return strcmp($b["sku"], $a["sku"]);});
                                else if($sort_by=='department')usort($sales, function($a, $b) {return strcmp($b["department"],$a["department"]);});
                                else if($sort_by=='supplier')usort($sales, function($a, $b) {return strcmp($b["supplier"],$a["supplier"]);});
                                else if($sort_by=='total_cost')usort($sales, function($a, $b) {return ($b["total_cost"]-$a["total_cost"]);});
                                else if($sort_by=='total_sale')usort($sales, function($a, $b) {return ($b["total_sale"]-$a["total_sale"]);});
                                else if($sort_by=='quantity')usort($sales, function($a, $b) {return ($b["quantity"]-$a["quantity"]);});
                            }
                            $data['results'] = array_slice($sales,$page*$limit,$limit);
                        }
                        else
                            $data['results']=array();
                        
                        
                        $data['total'] = sizeof($sales);
                        $data['page']=$page;
                        $data['limit']=$limit;
                        $data['status']='ok';
                        
                        //foreach($data['results'] as $key=>$value)
                        {
                            /*
                            $this->db->where('sid',$data['results'][$key]['sid']);
                            $data['results'][$key]['supplier']=$this->db->get('suppliers')->row(0,'object')->name;

                            $this->db->where('pid',$data['results'][$key]['pid']);
                            $product=$this->db->get('products')->row_array();
                            $data['results'][$key]['sku']=$product['sku'];

                            $this->db->where('did',$product['department']);
                            $data['results'][$key]['department']=$this->db->get('departments')->row(0,'object')->name;
                            
                            $data['results'][$key]['total_cost']=$data['results'][$key]['unit_cost']*$data['results'][$key]['base_quantity'];
                            $data['results'][$key]['total_sale']=$data['results'][$key]['unit_sale']*$data['results'][$key]['base_quantity'];

                            $data['results'][$key]['stocked_on']=date('jS F, Y @ h:i A', strtotime($data['results'][$key]['stocked_on']));
                             * 
                             */
                        }

                        echo json_encode($data);
                        die();
                    }
                    
                    //else
                    {
                        $data['status']='invalid_filter_by';
                        echo json_encode($data);
                        die();
                    }
                }
                //else
                {
                    $data['status']='invalid_sort_by';
                    echo json_encode($data);
                    die();
                }
            }
            else if(user_logged_in() && user_can('GENERATE_REPORT') && $this->input->post('type')=='sellinfo')
            {
                header('Content-type: application/json');
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'time';
                $order_by=$this->input->get_post('order') ? $this->input->get_post('order') : 'desc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, array('name','sku','did','sid','total_cost','total_sale','quantity','time')))
                {
                    /* Search */                      
                    if(in_array($this->input->get_post('filter_by_1'),array('sku','barcode','name')) && in_array($this->input->get_post('filter_by_2'),array('did')) && in_array($this->input->get_post('filter_by_3'),array('sid')))
                    {
                        /*
                        if($this->input->post('from'))$this->db->where('invoices.bill_time >=',$this->input->post('from'));
                        if($this->input->post('to'))$this->db->where('invoices.bill_time <=',$this->input->post('to'));
                        $invoices=$this->db->get('invoices')->result_array();
                        
                        $sales=array();

                        foreach($invoices as $invoice)
                        {
                            $this->db->where('invoice_id',$invoice['invoice_id']);
                            $orders=$this->db->get('orders')->result_array();

                            foreach($orders as $order)
                            {
                                $this->db->where('stid',$order['stid']);
                                $stock=$this->db->get('stocks')->row_array();
                                
                                $this->db->where('pid',$stock['pid']);
                                $t=$this->db->get('products')->row_array();

                                $sale['invoice']=$invoice['generated_id'];
                                $sale['oid']=$order['order_id'];
                                $sale['pid']=$stock['pid'];
                                $sale['barcode']=$t['barcode'];
                                $sale['sku']=$t['sku'];
                                $sale['name']=$t['name'];

                                $this->db->where('did',$t['department']);
                                $sale['department']=$this->db->get('departments')->row(0,'object')->name;

                                $this->db->where('sid',$stock['sid']);
                                $sale['supplier']=$this->db->get('suppliers')->row(0,'object')->name;

                                $sale['sid']=$stock['sid'];
                                $sale['quantity']=$order['quantity'];
                                $sale['total_cost']=$stock['unit_cost']*$order['quantity'];
                                $sale['total_sale']=$order['total_sale']-$order['total_discount'];
                                $sale['bill_time']=$invoice['bill_time'];
                                $sale['time']=date('jS F, Y @ h:i A', strtotime($invoice['bill_time']));
                                
                                $sales[]=$sale;
                            }
                        }
                        if(sizeof($sales)>0)
                        {
                            if((int)$this->input->post('active_1')==1)
                            {
                                if($this->input->post('filter_1'))
                                {
                                    if($this->input->post('filter_by_1')=='sku')
                                    {
                                        $this->db->like('sku',$this->input->post('filter_1'),'both');
                                        $results=$this->db->get('products')->result_array();
                                        $pid_pool=array();
                                        foreach($results as $result)
                                            array_push($pid_pool,$result['pid']);

                                        foreach($sales as $key=>$value)
                                        {
                                            if(!in_array($value['pid'], $pid_pool))
                                                unset($sales[$key]);
                                        }

                                    }
                                    else if($this->input->post('filter_by_1')=='barcode')
                                    {
                                        $this->db->like('barcode',$this->input->post('filter_1'),'both');
                                        $results=$this->db->get('products')->result_array();
                                        $pid_pool=array();
                                        foreach($results as $result)
                                            array_push($pid_pool,$result['pid']);

                                        foreach($sales as $key=>$value)
                                        {
                                            if(!in_array($value['pid'], $pid_pool))
                                                unset($sales[$key]);
                                        }
                                    }
                                    else if($this->input->post('filter_by_1')=='name')
                                    {
                                        $this->db->like('name',$this->input->post('filter_1'),'both');
                                        $results=$this->db->get('products')->result_array();
                                        $pid_pool=array();
                                        foreach($results as $result)
                                            array_push($pid_pool,$result['pid']);

                                        foreach($sales as $key=>$value)
                                        {
                                            if(!in_array($value['pid'], $pid_pool))
                                                unset($sales[$key]);
                                        }
                                    }
                                }
                            }
                        }
                        if(sizeof($sales)>0)
                        {
                            if((int)$this->input->post('active_2')==1)
                            {
                                if($this->input->post('filter_2'))
                                {
                                    $this->db->where('department',$this->input->post('filter_2'));
                                    $results=$this->db->get('products')->result_array();
                                    $pid_pool=array();
                                    foreach($results as $result)
                                        array_push($pid_pool,$result['pid']);

                                    foreach($sales as $key=>$value)
                                    {
                                        if(!in_array($value['pid'], $pid_pool))
                                            unset($sales[$key]);
                                    }
                                }
                            }
                        }
                        if(sizeof($sales)>0)
                        {
                            if((int)$this->input->post('active_3')==1)
                            {
                                if($this->input->post('filter_3'))
                                {
                                    foreach($sales as $key=>$value)
                                    {
                                        if($value['sid'] != $this->input->post('filter_3'))
                                            unset($sales[$key]);
                                    }
                                }
                            }
                        }
                        //echo '<pre>';print_r($sales);echo '</pre>';die();
                        
                        $data['total_total_cost']=0;
                        $data['total_total_sale']=0;
                        foreach($sales as $sale)
                        {
                            $data['total_total_cost']+=$sale['total_cost'];
                            $data['total_total_sale']+=$sale['total_sale'];
                        }
                        
                        if(sizeof($sales)>0)
                        {
                            if($order_by=='asc')
                            {
                                if($sort_by=='name')usort($sales, function($a, $b) {return strcmp($a["name"], $b["name"]);});
                                else if($sort_by=='sku')usort($sales, function($a, $b) {return strcmp($a["sku"], $b["sku"]);});
                                else if($sort_by=='department')usort($sales, function($a, $b) {return strcmp($a["department"],$b["department"]);});
                                else if($sort_by=='supplier')usort($sales, function($a, $b) {return strcmp($a["supplier"],$b["supplier"]);});
                                else if($sort_by=='total_cost')usort($sales, function($a, $b) {return ($a["total_cost"]-$b["total_cost"]);});
                                else if($sort_by=='total_sale')usort($sales, function($a, $b) {return ($a["total_sale"]-$b["total_sale"]);});
                                else if($sort_by=='quantity')usort($sales, function($a, $b) {return ($a["quantity"]-$b["quantity"]);});
                                else if($sort_by=='time')usort($sales, function($a, $b) {return strcmp($a["bill_time"],$b["bill_time"]);});
                            }
                            else
                            {
                                if($sort_by=='name')usort($sales, function($a, $b) {return strcmp($b["name"], $a["name"]);});
                                else if($sort_by=='sku')usort($sales, function($a, $b) {return strcmp($b["sku"], $a["sku"]);});
                                else if($sort_by=='department')usort($sales, function($a, $b) {return strcmp($b["department"],$a["department"]);});
                                else if($sort_by=='supplier')usort($sales, function($a, $b) {return strcmp($b["supplier"],$a["supplier"]);});
                                else if($sort_by=='total_cost')usort($sales, function($a, $b) {return ($b["total_cost"]-$a["total_cost"]);});
                                else if($sort_by=='total_sale')usort($sales, function($a, $b) {return ($b["total_sale"]-$a["total_sale"]);});
                                else if($sort_by=='time')usort($sales, function($a, $b) {return strcmp($b["bill_time"],$a["bill_time"]);});
                            }
                            $data['results']=array_slice($sales,$page*$limit,$limit);
                        }
                        else
                            $data['results']=array();
                        
                        $data['total'] = sizeof($sales);
                        $data['page']=$page;
                        $data['limit']=$limit;
                        $data['status']='ok';
                         * *
                         */
                        
                        //foreach($data['results'] as $key=>$value)
                        {
                            /*
                            $this->db->where('sid',$data['results'][$key]['sid']);
                            $data['results'][$key]['supplier']=$this->db->get('suppliers')->row(0,'object')->name;

                            $this->db->where('pid',$data['results'][$key]['pid']);
                            $product=$this->db->get('products')->row_array();
                            $data['results'][$key]['sku']=$product['sku'];

                            $this->db->where('did',$product['department']);
                            $data['results'][$key]['department']=$this->db->get('departments')->row(0,'object')->name;
                            
                            $data['results'][$key]['total_cost']=$data['results'][$key]['unit_cost']*$data['results'][$key]['base_quantity'];
                            $data['results'][$key]['total_sale']=$data['results'][$key]['unit_sale']*$data['results'][$key]['base_quantity'];

                            $data['results'][$key]['stocked_on']=date('jS F, Y @ h:i A', strtotime($data['results'][$key]['stocked_on']));
                             * 
                             */
                        }
                        $this->db->select('*');
                        $this->db->from('invoices');
                        if((int)$this->input->post('active_1')==1)$this->db->like('products.'.$this->input->post('filter_by_1'),$this->input->post('filter_1'),'both');
                        if((int)$this->input->post('active_2')==1)$this->db->where('products.department',$this->input->post('filter_2'));
                        if((int)$this->input->post('active_3')==1)$this->db->where('stocks.sid',$this->input->post('filter_3'));
                        if($this->input->post('from'))$this->db->where('invoices.bill_time >=',$this->input->post('from'));
                        if($this->input->post('to'))$this->db->where('invoices.bill_time <=',$this->input->post('to'));
                        $this->db->join('orders', 'invoices.invoice_id = orders.invoice_id');
                        $this->db->join('stocks', 'stocks.stid = orders.stid');
                        $this->db->join('products', 'stocks.pid = products.pid');
                        $this->db->join('departments', 'departments.did = products.department');
                        $this->db->join('suppliers', 'suppliers.sid = stocks.sid');
                        $data['total'] = $this->db->get()->num_rows();
                        
                        $this->db->select('invoices.generated_id as invoice, orders.order_id as oid, barcode,sku,products.name as name,departments.name as department, suppliers.name as supplier, (stocks.unit_cost * orders.quantity) as total_cost,, (stocks.unit_sale * orders.quantity) as total_sale, orders.quantity as quantity, invoices.bill_time as time');
                        $this->db->from('invoices');
                        if((int)$this->input->post('active_1')==1)$this->db->like('products.'.$this->input->post('filter_by_1'),$this->input->post('filter_1'),'both');
                        if((int)$this->input->post('active_2')==1)$this->db->where('products.department',$this->input->post('filter_2'));
                        if((int)$this->input->post('active_3')==1)$this->db->where('stocks.sid',$this->input->post('filter_3'));
                        if($this->input->post('from'))$this->db->where('invoices.bill_time >=',$this->input->post('from'));
                        if($this->input->post('to'))$this->db->where('invoices.bill_time <=',$this->input->post('to'));                        
                        $this->db->join('orders', 'invoices.invoice_id = orders.invoice_id');
                        $this->db->join('stocks', 'stocks.stid = orders.stid');
                        $this->db->join('products', 'stocks.pid = products.pid');
                        $this->db->join('departments', 'departments.did = products.department');
                        $this->db->join('suppliers', 'suppliers.sid = stocks.sid');
                        
                        if($sort_by=='time')$this->db->order_by('invoices.bill_time', $order_by);
                        if($sort_by=='total_cost')$this->db->order_by('total_cost', $order_by);
                        if($sort_by=='total_sale')$this->db->order_by('total_sale', $order_by);
                        if($sort_by=='sid')$this->db->order_by('supplier', $order_by);
                        if($sort_by=='did')$this->db->order_by('department', $order_by);
                        if($sort_by=='quantity')$this->db->order_by('quantity', $order_by);
                        if($sort_by=='barcode')$this->db->order_by('barcode', $order_by);
                        if($sort_by=='sku')$this->db->order_by('sku', $order_by);
                        if($sort_by=='name')$this->db->order_by('name', $order_by);
                        
                        $this->db->limit($limit, $limit*$page);
                        $data['results'] = $this->db->get()->result_array();
                        $data['page']=$page;
                        $data['limit']=$limit;
                        $data['status']='ok';
                        
                        foreach($data['results'] as $key=>$value)
                        {
                            $data['results'][$key]['time']=date('jS F, Y @ h:i A', strtotime($data['results'][$key]['time']));
                        }
                        
                        $this->db->select('sum(stocks.unit_cost * orders.quantity) as cost, sum(orders.total_sale) as sale');
                        $this->db->from('invoices');
                        if((int)$this->input->post('active_1')==1)$this->db->like('products.'.$this->input->post('filter_by_1'),$this->input->post('filter_1'),'both');
                        if((int)$this->input->post('active_2')==1)$this->db->where('products.department',$this->input->post('filter_2'));
                        if((int)$this->input->post('active_3')==1)$this->db->where('stocks.sid',$this->input->post('filter_3'));
                        if($this->input->post('from'))$this->db->where('invoices.bill_time >=',$this->input->post('from'));
                        if($this->input->post('to'))$this->db->where('invoices.bill_time <=',$this->input->post('to'));                        
                        $this->db->join('orders', 'invoices.invoice_id = orders.invoice_id');
                        $this->db->join('stocks', 'stocks.stid = orders.stid');
                        $this->db->join('products', 'stocks.pid = products.pid');
                        $this->db->join('departments', 'departments.did = products.department');
                        $this->db->join('suppliers', 'suppliers.sid = stocks.sid');
                        $total_total=$this->db->get()->row(0,'object');
                        $data['total_total_cost']=$total_total->cost;
                        $data['total_total_sale']=$total_total->sale;
                        

                        echo json_encode($data);
                        die();
                    }
                    
                    //else
                    {
                        $data['status']='invalid_filter_by';
                        echo json_encode($data);
                        die();
                    }
                }
                //else
                {
                    $data['status']='invalid_sort_by';
                    echo json_encode($data);
                    die();
                }
            }
            else if(user_logged_in() && user_can('GENERATE_REPORT') && $this->input->post('type')=='profit')
            {
                header('Content-type: application/json');
                
                if($this->input->post('from'))$this->db->where('invoices.bill_time >=',$this->input->post('from'));
                if($this->input->post('to'))$this->db->where('invoices.bill_time <=',$this->input->post('to'));
                
                $invoices=$this->db->get('invoices')->result_array();
                $total_cost=0;
                $total_subtotal=0;
                $total_discount=0;
                $total_vat=0;
                $total_bill=0;
                $total_paid=0;
                foreach($invoices as $invoice)
                {
                    $total_subtotal+=$invoice['subtotal'];
                    $total_discount+=$invoice['discount']+$invoice['extra_discount'];
                    $total_vat+=$invoice['vat'];
                    $total_bill+=$invoice['total_bill'];
                    if($invoice['payment_method']=='cash')
                        $total_paid+=$invoice['total_bill'];
                    
                    $this->db->where('invoice_id',$invoice['invoice_id']);
                    $orders=$this->db->get('orders')->result_array();
                    foreach($orders as $order)
                    {
                        $this->db->where('stid',$order['stid']);
                        $stock=$this->db->get('stocks')->row_array();
                        $total_cost += $stock['unit_cost']*$order['quantity'];
                    }
                }
                
                $data['total_cost']=$total_cost;
                $data['total_subtotal']=$total_subtotal;
                $data['total_discount']=$total_discount;
                $data['total_vat']=$total_vat;
                $data['total_bill']=$total_bill;
                $data['total_paid']=$total_paid;
                $data['status']='ok';
                echo json_encode($data);
                die();
            }
            else if(user_logged_in() && user_can('GENERATE_REPORT') && $this->input->post('type')=='currentstock')
            {
                header('Content-type: application/json');
                
                //if($this->input->post('from'))$this->db->where('invoices.bill_time >=',$this->input->post('from'));
                //if($this->input->post('to'))$this->db->where('invoices.bill_time <=',$this->input->post('to'));
                
                $total_cost=$this->db->query('SELECT sum(unit_cost * (quantity+store_quantity)) as total_cost from stocks INNER JOIN products ON stocks.pid = products.pid WHERE products.review = 1')->row_array();
                $total_sale=$this->db->query('SELECT sum(unit_sale * (quantity+store_quantity)) as total_sale from stocks INNER JOIN products ON stocks.pid = products.pid WHERE products.review = 1')->row_array();
                
                $data['total_cost']=$total_cost['total_cost'];
                $data['total_sale']=$total_sale['total_sale'];
                $data['status']='ok';
                echo json_encode($data);
                die();
            }
            else
                echo '<h1>Bad Request</h1>';
            die();
        }        
        public function stockentry()
        {
            if(user_logged_in() && user_can('GENERATE_REPORT'))
            {                
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'stocked_on';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'desc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 40;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, array('stocked_on')))
                {
                    $data['fields']=array(
                        'stid'=>array('Stock ID','6'),
                        'sku'=>array('SKU','6'),
                        'sid'=>array('Supplier','10'),
                        'department'=>array('Department','8'),
                        'unit_cost'=>array('Unit Cost',5,'right'),
                        'unit_sale'=>array('Unit Price',5,'right'),
                        'base_quantity'=>array('Quantity',5,'center'),
                        'total_cost'=>array('Total Cost','5','right'),
                        'total_sale'=>array('Total Price','5','right'),
                        'stocked_on'=>array('Entry Time','15','center'),
                    );
                    $data['sort_fields']=array(                        
                        'stocked_on'=>'Entry Time',
                        'stid'=>'Stock ID',
                        'sku'=>'SKU',
                        'department'=>'Department',
                        'sid'=>'Supplier',
                        'unit_cost'=>'Unit Cost',
                        'unit_sale'=>'Unit Price',
                        'base_quantity'=>'Quantity'
                    );
                    
                    $data['search_fields_1']=array(                        
                        'did'=>'Department'
                    );
                    $data['search_fields_2']=array(
                        'sid'=>'Supplier'
                    );
                    $data['search_fields_3']=array(
                        'stocked_on'=>'Entry Time'
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );
                    
                    unset($temp);
                    $this->db->where('active',1);
                    $temp=$this->db->get('suppliers')->result_array();
                    foreach($temp as $key=>$value)
                        $data['suppliers'][$value['sid']]=$value['name'];

                    unset($temp);
                    $this->db->where('active',1);
                    $temp=$this->db->get('departments')->result_array();
                    foreach($temp as $key=>$value)
                        $data['departments'][$value['did']]=$value['name'];
                    
                    $data['sort_by']='stocked_on';
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;                    

                    //echo '<pre>';print_r($data);echo '</pre>';die();          

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('reports/stockentry',$data);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');                    
                }
            }
            else            
                echo '<h1>Bad Request</h1>';
        }
        public function sellinfo()
        {
            if(user_logged_in() && user_can('GENERATE_REPORT'))
            {                
                $sort_by=$this->input->get_post('sort_by') ? $this->input->get_post('sort_by') : 'time';
                $order=$this->input->get_post('order') ? $this->input->get_post('order') : 'desc';
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 50;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;

                if(in_array($sort_by, array('name','sku','did','sid','total_cost','total_sale','quantity','time')))
                {
                    $data['fields']=array(
                        'barcode'=>array('Barcode','6'),
                        'sku'=>array('SKU','5'),
                        'name'=>array('Item',10,),
                        'did'=>array('Department','6'),
                        'sid'=>array('Supplier','8'),
                        'total_cost'=>array('Total Cost',5,'right'),
                        'total_sale'=>array('Total Sale',5,'right'),
                        'quantity'=>array('Quantity',5,'center'),
                        'time'=>array('Time',14,'center')
                    );
                    $data['sort_fields']=array(
                        'time'=>'Time',
                        'name'=>'Item',
                        'sku'=>'SKU',
                        'did'=>'Department',
                        'sid'=>'Supplier',
                        'total_cost'=>'Total Cost',
                        'total_sale'=>'Total Sale',
                        'quantity'=>'Quantity'
                    );
                    
                    $data['search_fields_1']=array(
                        'sku'=>'SKU',
                        'barcode'=>'Barcode',
                        'name'=>'Item'
                    );
                    $data['search_fields_2']=array(
                        'did'=>'Department'
                    );
                    $data['search_fields_3']=array(
                        'sid'=>'Supplier'
                    );
                    $data['search_fields_4']=array(
                        'time'=>'Time'
                    );
                    $data['orders']=array(
                        'asc'=>'Ascending',
                        'desc'=>'Descending'
                    );
                    
                    unset($temp);
                    $this->db->where('active',1);
                    $temp=$this->db->get('suppliers')->result_array();
                    foreach($temp as $key=>$value)
                        $data['suppliers'][$value['sid']]=$value['name'];

                    unset($temp);
                    $this->db->where('active',1);
                    $temp=$this->db->get('departments')->result_array();
                    foreach($temp as $key=>$value)
                        $data['departments'][$value['did']]=$value['name'];
                    
                    $data['sort_by']='time';
                    $data['order']=$order;
                    $data['limit']=$limit;
                    $data['page']=$page;                    

                    //echo '<pre>';print_r($data);echo '</pre>';die();          

                    $this->load->view('header');
                    $this->load->view('menu');
                    $this->load->view('wrap_begin');
                    $this->load->view('reports/sellinfo',$data);
                    $this->load->view('wrap_end');
                    $this->load->view('footer');                    
                }
            }
            else            
                echo '<h1>Bad Request</h1>';
        }
        public function profit()
        {
            if(user_logged_in() && user_can('GENERATE_REPORT'))
            {                
                
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('reports/profit');
                $this->load->view('wrap_end');
                $this->load->view('footer');
                
            }
            else            
                echo '<h1>Bad Request</h1>';
        }
        public function currentstock()
        {
            if(user_logged_in() && user_can('GENERATE_REPORT'))
            {
                
                $data['limit']=10;
                $data['fields']=array(
                    'time'=>array('Date','30','left'),
                    'total_cost'=>array('Total Cost','20'),
                    'total_sale'=>array('Total Sale','20'),
                    'potential_profit'=>array('Potential Profit','20')
                );
                $this->load->view('header');
                $this->load->view('menu');
                $this->load->view('wrap_begin');
                $this->load->view('reports/currentstock',$data);
                $this->load->view('wrap_end');
                $this->load->view('footer');
                
            }
            else            
                echo '<h1>Bad Request</h1>';
        }
        public function currentstocks()
        {
            if(user_logged_in() && user_can('GENERATE_REPORT'))
            {                
                
                $limit=$this->input->get_post('limit') ? $this->input->get_post('limit') : 10;
                $page=$this->input->get_post('page') ? $this->input->get_post('page') : 0;
                
                $data['results']=array();
                $this->db->select('DISTINCT(date(time)) as u',FALSE);
                $dates=$this->db->get('currentstock')->result_array();
                ksort($dates);
                foreach($dates as $date)
                {
                    $this->db->where('date(time)',$date['u']);
                    $this->db->order_by('time','desc');
                    $this->db->limit(1);
                    $data['results'][] = $this->db->get('currentstock')->row_array();
                }
                $data['total']=sizeof($dates);
                $data['results']=array_slice($data['results'],$page*$limit,$limit);
                $data['limit']=$limit;
                $data['page']=$page;
                $data['status']='ok';
                
                foreach($data['results'] as $key=>$value)
                {
                    $data['results'][$key]['time']=date('jS F, Y @ h:i A', strtotime($data['results'][$key]['time']));
                }
                
                echo json_encode($data);
                die();
                
            }
            else
            {
                echo '<h1>Bad Request</h1>';
            }
        }
}

/* End of file reports.php */
/* Location: ./application/controllers/reports.php */
