<?php
$prodname = $product['name'];
$trimprodname = trim($prodname);
$cleanurl = str_replace(' ', '-', $trimprodname);
//Get quantity, pass it to paypal sandbox
$_GET['qty'];
$qtytotal = $_GET['qty'];
echo "$qty";

?>


<!-- Product Display -->
<div class="col mb-auto">
    <div class="crop-fit">
        <img src="<?php print $product['image'] ?>" alt="fitness clothing" style="width: 100%;height: 100%;">
    </div>
    <div class="caption">
        <a class="prodlink" href="<?php echo $cleanurl; ?>">
            <span style="text-align:center;"><?php print $product['name'] ?></span><br>
            <span style="text-align:center;color:#000;"><b>$<?php print $product['price'] ?></b></span>
        </a>
        <!--  Rating, Buy now, Add now Button Wrapper -->
        <div class="item-purchase-section">
            <!-- Rating System -->
            <div class="item-rating">
                <?php
                $pid = $product['product_id'];
                echo '<span id="response' . $pid . '">';

                $avg = $dbh->prepare("select avg(rating) as average from rating where productid = '$pid'");

                $avg->execute();

                $avgrow = $avg->fetchObject();

                $score = $avgrow->average;


                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= ceil($score)) {
                        echo '<i class="far fa-star" onclick="sendRequest(\'' . $pid . '\',\'' . $i . '\');"
                                    style="font-weight:900"></i>';
                    } else {
                        echo '<i class="far fa-star" onclick="sendRequest(\'' . $pid . '\',\'' . $i . '\');"
                                    style="font-weight:400"></i>';
                    }
                }
                echo '</span>';

                ?>
            </div> <!-- End Rating Section -->
            <div class="button-group">
                <!-- Add to Cart -->
                <form method="post" action="index.php?action=addcart">
                    <p style="text-align:center;color:#000;">
                        <select class="btn btn-left" name="qty">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                        <button type="submit" class="btn btn-left">Add To Cart</button>
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
                    <input type="hidden" name="item_name" value="<?php print $product['name']; ?>" />
                    <input type="hidden" name="amount" value="<?php print $product['price']; ?>" />
                    <input type="hidden" name="quantity" value="<?php print $qtytotal; ?>" />
                    <input type="hidden" name="item_number" value="<?php print $pid; ?>" />
                    <input type="hidden" name="notify_url" value="http://cdelcarmendev.com/testcart" />
                    <button type="submit" class="btn btn-right btn-submit">Buy Now</button>

                </form>
            </div>
        </div>
    </div>
</div>