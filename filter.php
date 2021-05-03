<?php //search filter functionality
//connection to db
include('connect.php');
$qry = $_GET['qry'];
$query = "SELECT * FROM products WHERE name LIKE '%$qry%' ";
/* echo $query; */
$stmt = $dbh->prepare($query);
$stmt->execute();
$products = stmt->fetchAll();

foreach($product as $key=>$product): ?>
<!-- Product Display -->
                 <div class="col">
                     <div class="crop-fit">
                         <img src="<?php print $product['image'] ?>" alt="fitness clothing" style="width: 100%;height: 100%;">
                     </div>
                     <div class="caption">
                         <p style="text-align:center;"><?php print $product['name'] ?></p>
                         <p style="text-align:center;color:#000;"><b>$<?php print $product['price'] ?></b></p>
                         <!-- Add to Cart -->
                         <form method="post" action="index.php?action=addcart">
                             <p style="text-align:center;color:#000;">
                                 <select class="chkbtn" name="qty">
                                     <option value="1">1</option>
                                     <option value="2">2</option>
                                     <option value="3">3</option>
                                 </select>
                                 <button type="submit" class="btn btn-dark btn-outline-dark chkbtn">Add To Cart</button>
                                 <input type="hidden" name="sku" value="<?php print $product['sku'] ?>">
                             </p>
                         </form>
                         <!-- Buy Now -->
                         <form action="https://www.sandbox.paypal.com/us/cgi-bin/webscr" method="post" target="paypal">
                             <input type="hidden" name="cmd" value="_ext-enter" />
                             <input type="hidden" name="redirect_cmd" value="_xclick" />
                             <input type="hidden" name="cancel_return" value="http://cdelcarmendev.com/testcart" />
                             <input type="hidden" name="return" value="http://cdelcarmendev.com/testcart" />
                             <input type="hidden" name="business" value="camerondelcarmen@gmail.com" />
                             <input type="hidden" name="item_name" value="<?php print $product['name'] ?>" />
                             <input type="hidden" name="amount" value="<?php print $product['price'] ?>" />
                             <input type="hidden" name="item_number" value="<?php print $product['qty'] ?>" />
                             <input type="hidden" name="notify_url" value="http://cdelcarmendev.com/testcart" />
                             <button type="submit" class="btn btn-dark btn-outline-dark chkbtn" value>Buy Now</button>
                     </div>
                     </form>
                 </div>