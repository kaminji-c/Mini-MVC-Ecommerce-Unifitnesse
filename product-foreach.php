<section class="prod-container">
      <h2 class="product-head">Shop by <span>FIT</span></h2>
      <h3 class="z-align">Relaxed Fit</h3>
        <div class="row row-cols-sm-2 row-cols-md-4 shop-row-end">
          <div class="col">
            <div class="crop-fit">
            <img src="images/relaxedfit.webp" alt="relaxed fit clothing" style="width: 100%;height: 100%;"/>
            <div class="center">
                <a class="linkbtn" href="relaxedfit.html">Shop Relaxed Fit</a>
              </div>
              </div>
            </div>
            <?php foreach($products as $product):?> 
                        <div class="col"> 
                        <div class="crop-fit">  
                        <img src="<?php print $product['image']?>" alt="fitness clothing" style="width: 100%;height: 100%;">
                        </div>
                        <div class="caption">
                         <p style="text-align:center;"><?php print $product['prodname']?></p>
                         <p style="text-align:center;color:#04B745;"><b>$<?php print $product['price']?></b></p>
                         <form method="post" action="index.php?action=addcart">
                         <p style="text-align:center;color:#04B745;">
                        <button type="submit" class="btn btn-warning">Add To Cart</button>
                     <input type="hidden" name="sku" value="<?php print $product['sku']?>">
              </p>
            </form>
          </div>
            </div>
         
      <?php endforeach;?>
