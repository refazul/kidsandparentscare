<?php

//echo '<pre>';
//print_r($invoice);
//echo '</pre>';

?>
<html>
    <head>
        <style type="text/css">
            tr:last-child td
            {
                border-bottom: 1px solid #ccc;
            }
        </style>
    </head>
    <body>
        <?php
            $this->db->where('uid',$invoice['billed_by']);
            $billed_by=$this->db->get('users')->row(0,'object')->full_name;
        ?>
        <div style='width:300px;margin:auto;'>
            
        <h2 style="text-align: center;">Kids & Parents Care</h2>
            
        <div style="text-align: center;margin-bottom: 15px;">---------------RETAIL INVOICE--------------</div>
        
        <div style="text-align:right;margin-bottom:10px;"><?php echo $invoice['bill_time'];?></div>
        
        <div>Cashier: <?php echo $billed_by;?></div>
        <div style="border-bottom:1px solid #ccc;padding-bottom:10px;margin-bottom:10px;">Invoice Number: <?php echo $invoice['generated_id'];?></div>
            
            
        <table style="float:right;width:100%;">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Item Description</th>
                    <th>MRP</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>                
                <?php $i=1;$subtotal=0;$discount=0;foreach($invoice['orders'] as $order):?>
                <tr>
                    <td><?php echo $i;?></td>
                    <td>
                        <?php
                        
                        $this->db->where('stid',$order['stid']);
                        $pid=$this->db->get('stocks')->row(0,'object')->pid;
                        
                        $this->db->where('pid',$pid);
                        $name=$this->db->get('products')->row(0,'object')->name;
                        
                        echo $name;
                        
                        $subtotal += $order['unit_sale']*$order['quantity'];
                        $discount += $order['total_discount'];
                        
                        ?>
                    </td>
                    <td style="text-align:right;"><?php echo $order['unit_sale'];?></td>
                    <td style="text-align:right;"><?php echo $order['quantity'];?></td>
                    <td style="text-align:right;"><?php echo $order['unit_sale']*$order['quantity'];?></td>
                </tr>
                <?php $i++;endforeach;?>
            </tbody>
        </table>
        <div style="float:right;clear:right;">Subtotal : <?php echo $subtotal;?></div>
        <div style="float:right;clear:right;">VAT(+) : <?php echo $invoice['vat'];?></div>
        <div style="float:right;clear:right;">Discount(-) : <?php echo $discount;?></div>
        <div style="float:right;clear:right;border-top:1px solid #ccc;">Net Payable : <?php echo $subtotal + $invoice['vat'] - $discount;?></div>
        </div>
    </body>
</html>