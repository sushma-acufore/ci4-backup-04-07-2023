<?php 
namespace App\Controllers\Search;
use App\Models\Search\CartAppend;
use CodeIgniter\Controller;

class CartAppendController extends BaseController
{
	protected $ordermodel;
    public function __construct()
    {
        \Config\Services::session();
        $this->cartappend = new CartAppend();
    }

    public function cart_append()
    {
      $cid = $_POST['cid'];
      $e1 = '';
      $num_rows1 = $this->cartappend->check_product_cart_index($cid);
      if (!empty($num_rows1)) 
      {
        $e1 .='<div class="row">
                <div class="col-lg-12" style="padding: 5px 25px;">
                <div class="row table-area-row table-responsive">
                    <table class="table cart-table-data" id="table-data">
                        <thead>
                            <tr class="table-dark tr-head">
                            <th style="width:15%">PARTS NUMBER</th>
                            <th style="width:25%">DESCRIPTION</th>
                            <th style="width:5%">STOCK</th>
                            <th style="width:5%">QUANTITY</th>
                            <th style="width:10%">LIST PRICE</th>
                            <th style="width:12%">DEALER&nbsp;DISCOUNT</th>
                            <th style="width:16%">STOCK&nbsp;DEALER&nbsp;PRICE</th>
                            <th style="width:15%">EXTENDED&nbsp;PRICE</th>
                            <th style="width:5%">REMOVE</th>
                            </tr>
                        </thead>
                        <tbody>';
                        foreach($num_rows1 as $row1)
                        {
                          $rid = $row1['id'];

                          $check_desc_items = $this->cartappend->product_cart_discount_items($rid);
                          $num_disc_items = $check_desc_items->getNumRows();
                          $fetch_arr = $check_desc_items->getResult();

                            $product = $row1['product'];
                            $lprice = $row1['lprice'];
                            $description = $row1['description'];
                            
                            $fulldescription=$row1['description'];
                            $fulldescription=str_replace('"', '', $fulldescription);

                            $sp_price = $row1['sp_price'];
                            if(strlen($description)>15)
                            {
                                $description = substr($description, 0, 15)."...";
                            }
                            $qty = $row1['qty'];
                            $price = $row1['price'];
                            $discount_name = $row1['discount_name'];
                            $discount = $row1['discount'];
                            $totalprice = $row1['totalprice'];
                            $currency = $row1['currency'];
                            $discount8weeks_status = $row1['discount8weeks_status'];
                            $is_stock_dealer = $row1['is_stock_dealer'];
                            $stock_dealer_discount = $row1['stock_dealer_discount'];
                            
                            $sp_price = number_format($sp_price,2);
                            $lprice = number_format($lprice,2);
                            $price = number_format($price,2);
                            $totalprice = number_format($totalprice,2);
                            $total_8weeksdiscount_amt = $row1['total_8weeksdiscount_amt'];
                            $total_8weeksdiscount_amt = number_format($total_8weeksdiscount_amt,2);


                            $ind_discount_amt = (float)$row1['sp_price']*$discount;
                            $ind_discount_amt = (float)$ind_discount_amt/100;
                            $total_ind_discount_amt = $ind_discount_amt*$qty;
                            $total_ind_discount_amt = number_format($total_ind_discount_amt,2);

                          $result_avl = $this->cartappend->check_eagle_available_stock($product);
                          $avl_stock =0;
                          $serial_no_count = 0;
                          if(!empty($result_avl))
                          {
                            $avl_stock=$result_avl['S_AVAIL'];
                          }
                          $existing_qty=$row1['qty'];

                            $e1 .='<tr class="table-default">
                          <td>'.$product.'</td>
                          <td style="width:15%;"  data-toggle="tooltip" title="'.$fulldescription.'">'.$description.'</td>
                          <td>'.$avl_stock.'</td>
                          <td>
                              <div class="quantity-container">
                              <input onchange=\'change_input_quantity_cart("'.$rid.'","'.$product.'","'.$avl_stock.'","'.$qty.'")\' id="qty_value_cart'.$product.'" value="'.$qty.'"  style="background-color : #ecebeb; border-color: #ecebeb;border: 1px solid #ecebeb;width:30px;">
                              <!--<h2 id="search-table-counting" style="font-size: 14px;font-family: \'Inter\',sans-serif;">'.$qty.'</h2>-->
                                  <div class="row"
                                      style="padding: 0px 13px;margin: 0px -11px;background: none;width:-2px;">
                                      <button onclick=\'change_quantity_in_cart(2,"'.$rid.'","'.$avl_stock.'","'.$existing_qty.'","'.$qty.'")\' style="padding: 0px 0px;height:15px;"><img
                                              src="public/assets/img/Arrow-drop-up.png"></button>
                                      <button onclick=\'change_quantity_in_cart(1,"'.$rid.'","'.$avl_stock.'","'.$existing_qty.'","'.$qty.'")\' style="padding: 0px 0px;height:10px;"><img
                                              src="public/assets/img/Arrow-drop-down.png"></button>
                                  </div>
                              </div>
                          </td>
                          <td class="align-right">$'.$lprice.'</td>
                          <td class="align-right">$'.$sp_price.'</td>';
                          // stock discount
                if($is_stock_dealer==1)
                {
                   
                    //$e1 .= '<td class="align-right"><button type="button" class="cart-table-btn-dealer-price" >$'.$totalprice.'</button></td>';
                    $e1 .= '<td class="align-right">Discount&nbsp;in&nbsp;below&nbsp;line</td>';
                    $e1 .='<td class="align-right">$'.$totalprice.'</td>';
                }else
                { $e1 .= '<td class="align-right">
                                <button type="button" class="cart-table-btn-dealer-price" 
                                onclick=\'stock_dealer_alert("'.$product.'","'.$row1['sp_price'].'","'.$row1['stock_dealer_discount'].'","index")\' >Click&nbsp;Here</button>
                            </td>';
                    $e1 .='<td class="align-right">$'.$price.'</td>';
                }
                          $e1 .='<td><a onclick=\'delete_cart_item("'.$rid.'")\'><img class="search-cart-trash" src="public/assets/img/Trash.png" /></a></td>
                        </tr>';   
                        if($num_disc_items>0)
                        {
                          foreach($fetch_arr as $fetch_disc_item_row){
                            $discount_price=$fetch_disc_item_row->discount_price;
                            $total_discount_amt=$discount_price*$existing_qty;
                            $e1 .='<tr class="table-default">
                                    <td>'.$fetch_disc_item_row->discount_name.'</td>
                                    <td>'.$fetch_disc_item_row->discount_name.'</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>';
                                    $e1 .= '<td class="align-right">-&nbsp;$'.$total_discount_amt.'</td>';
                                    $e1 .='<td></td>
                                </tr>'; 
                          }
                              }
                      }
                      $e1 .='</tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>';
      }else
      {
          $e1 .='<div class="row">
                  <div class="col-lg-12" style="padding: 5px 25px;">
                      <div class="row table-area-row table-responsive">
                          <table class="table cart-table-data" id="table-data">
                              <thead>
                                  <tr class="table-dark tr-head">
                                  <th style="width:15%">PARTS NUMBER</th>
                                  <th style="width:20%">DESCRIPTION</th>
                                  <th style="width:10%">STOCK</th>
                                  <th style="width:5%">QUANTITY</th>
                                  <th style="width:10%">LIST PRICE</th>
                                  <th style="width:12%">DEALER&nbsp;DISCOUNT</th>
                                  <th style="width:16%">*STOCK&nbsp;DEALER&nbsp;PRICE</th>
                                  <th style="width:15%">EXTENDED&nbsp;PRICE</th>
                                  <th style="width:5%">REMOVE</th>
                                  </tr>
                              </thead>
                              <tbody>
                                <tr class="table-default">
                                <td colspan="9" style="text-align:center">Cart is Empty</td>
                                </tr>  
                            </tbody>
                          </table>
                      </div>
                  </div>
              </div>';
      }
    echo $e1;
    }



}