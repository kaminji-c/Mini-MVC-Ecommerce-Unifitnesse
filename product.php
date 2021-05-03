<?php
//Database connection, replace with your connection string.. Used PDO
include 'connect.php';
$total = 0;
/* var_dump($_GET); */

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Unifitnesse</title>
  <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
  <!-- Include Cloud Zoom CSS. -->
  <link rel="stylesheet" type="text/css" href="cloudzoom.css" />

  <!-- Include Cloud Zoom script. -->
  <script type="text/javascript" src="cloudzoom.js"></script>
  <script type="text/javascript">
    CloudZoom.quickStart();
  </script>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="styles.css">
  <!-- Font Awesome -->
  <script src="https://kit.fontawesome.com/fcf1955543.js" crossorigin="anonymous"></script>
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;1,700&family=Open+Sans:ital,wght@0,400;1,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://use.typekit.net/nko4kwz.css">
</head>

<body>
  <div class="container">
    <header>
      <?php include('nav.php'); ?>
    </header>
    <!-- Single Product Display -->

    <!-- Image Carousel -->
    <!--   Get Product Details -->
    <?php
    $pid = $_GET['pid'];
    if (empty($pid)) {
      header("Location: product.php");
    }
    $getproductinfo = $dbh->prepare("SELECT product_id, name, image, price, sku, proddesc, descrip FROM products where product_id = ?");
    $getproductinfo->bindParam(1, $pid, PDO::PARAM_INT);
    $getproductinfo->execute();
    $product = $getproductinfo->fetch();
    $currprice = $product['price'];
    $oldprice = $currprice + 5;
    $pid = $product['product_id'];

    ?>
    <!-- Get All Product Images -->
    <?php
    $getprodimg = $dbh->prepare("SELECT products.product_id, images.image_id, images.image, images.alt FROM products, images WHERE products.product_id = images.product_id AND products.product_id = ?");
    $getprodimg->bindParam(1, $pid, PDO::PARAM_INT);
    $getprodimg->execute();
    $prodimg = $getprodimg->fetch();
    $image = $prodimg['image'];
    $alt = $prodimg['alt']

    ?>
    <section class="product">
      <div class="product-row">
        <div class="col-sm">
          <div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img class="cloudzoom img-thumbnail" src="<?php echo $product['image']; ?>" style="width: 100%" data-cloudzoom="zoomImage: '<?php echo $product['image']; ?>'" />
              </div>
              <div class="carousel-item">
                <?php echo '<img class="img-thumbnail d-block w-100" src="' . $image . '" alt="' . $alt . '/>'; ?>
              </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleFade" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleFade" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
        </div> <!-- col left end -->
        <div class="col-sm left-divider">
          <?php echo '<h3 class="product">' . $product['name'] . '</h3>'; ?>
          <div class="product-caption">
            <span class="prod-price" style="text-align:center;color:#000;"><b>$<?php print $product['price'] ?></b></span>
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
              <br>
              <h4 class="product">Details</h4>
              <br>
              <?php
              $proddescrip = $product['descrip'];
              $prodinfobr = str_replace(array("\r\n", "\n"), array("<br>", "<br />"), $proddescrip);
              ?>
              <div class="proddesc"><?php echo $product['proddesc']; ?></div>
              <br>
              <div class="sm-descip"><?php echo $prodinfobr; ?></div>
              <br>
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
                <input type="hidden" name="quantity" value="<?php print $product['qty']; ?>" />
                <input type="hidden" name="item_number" value="<?php print $pid; ?>" />
                <input type="hidden" name="notify_url" value="http://cdelcarmendev.com/testcart" />
                <button type="submit" class="btn btn-right btn-submit prod-btn">Buy Now</button>
              </form>
            </div>
          </div>
        </div> <!-- End Product Description Col -->
      </div> <!-- End Row -->
    </section><!-- End product section -->
    <footer>
      <?php include('footer.php'); ?>
    </footer>
  </div><!-- Container ends -->
</body>

</html>