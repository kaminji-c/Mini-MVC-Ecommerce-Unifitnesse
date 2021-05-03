<?php
//Database connection, Used PDO
include 'connect.php';
//var_dump($_SESSION);
$total = 0;
//get action string
$action = isset($_GET['action']) ? $_GET['action'] : "";

//Add to cart
if ($action == 'addcart' && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $qty = $_POST['qty'];

    //Finding the product by code
    $query = "SELECT * FROM products WHERE sku=:sku";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam('sku', $_POST['sku']);
    $stmt->execute();
    $product = $stmt->fetch();

    $productid = $product['product_id'];


    //check the cartitem table to see if sku is in there
    $chkquery = "SELECT * FROM cartitems WHERE productid = '$productid' and sessionid = '$sessid'";
    $chkstmt = $dbh->prepare($chkquery);
    $chkstmt->execute();
    $productincart = $chkstmt->fetch();
    $id = $productincart['id'];
    if (!empty($id)) {   //if it is, we will write an update statement
        $upstmt = $dbh->prepare("update cartitems set qty = '$qty' where productid = '$productid' and sessionid = '$sessid'");
        $upstmt->execute();
    } else { //else write an insert statement
        $instmt = $dbh->prepare("insert into cartitems (productid,sessionid,timeofentry,qty) values ('$productid','$sessid',now(),'$qty')");
        $instmt->execute();
    }

    //Incrementing the product qty in cart
    $currentQty = $_POST['qty'];
    $_SESSION['products'][$_POST['sku']] = array('qty' => $currentQty, 'name' => $product['name'], 'image' => $product['image'], 'price' => $product['price']);
    $product = '';
    header("Location:index.php");
}
//Empty All
if ($action == 'emptyall') {
    $_SESSION['products'] = array();
    header("Location:index.php");
}

//Empty one by one
if ($action == 'empty') {
    $sku = $_GET['sku'];
    $products = $_SESSION['products'];
    unset($products[$sku]);
    $_SESSION['products'] = $products;
    header("Location:index.php");
}

$order = '';
$sort = $_GET['sort'];
if ($sort == 'price') {
    $order = 'order by price asc';
}
if ($sort == 'alpha') {
    $order = 'order by name asc';
}

//Get all Products 
$query = "SELECT * FROM products $order";
$stmt = $dbh->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll();

//Get all Products for Relaxed Fit
$rsquery = "SELECT * FROM products WHERE sku LIKE '%RS%' LIMIT 3";
$stmt = $dbh->prepare($rsquery);
$stmt->execute();
$relaxedproducts = $stmt->fetchAll();

//Get all Products for Slim Fit
$sfquery = "SELECT * FROM products WHERE sku LIKE '%SF%' LIMIT 3";
$stmt = $dbh->prepare($sfquery);
$stmt->execute();
$slimproducts = $stmt->fetchAll();

//Get all Products for All Day Wear
$dwquery = "SELECT * FROM products WHERE sku LIKE '%DW%' LIMIT 3";
$stmt = $dbh->prepare($dwquery);
$stmt->execute();
$alldayproducts = $stmt->fetchAll();

//Get all Products for Unisex
$usquery = "SELECT * FROM products WHERE sku LIKE '%US%' LIMIT 3";
$stmt = $dbh->prepare($usquery);
$stmt->execute();
$uniproducts = $stmt->fetchAll();

/* Figure out Clean URLS
  $pid = GET['pid'];
if ($pid == 1){
    echo 'This is where we would show Relaxed Fit Shorts<br>';
    echo '<img src = "https://cdn.shopify.com/s/files/1/0696/1303/products/052920_WR_ECOM_SUMMER-0189_200x.jpg?v=1603335917">';
} */
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Uniftinesse provides the comfort of fitness clothing that fit the best you regardless if you are bulking or trying to slim down.">
    <meta name="keywords" content="fitness clothing, unique fit, gym attire, slim fit, relaxed fit, all day wear, unisex wear, fitness home equipment, supplement">
    <title>Uniftinesse</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;1,700&family=Open+Sans:ital,wght@0,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/nko4kwz.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/fcf1955543.js" crossorigin="anonymous"></script>
    <!--  Ajax Rating System -->
    <link rel="stylesheet" href="https://use.typekit.net/nko4kwz.css">
    <script src="https://code.jquery.com/jquery-3.5.0.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script>
    <script type="text/javascript">
        const sendRequest = (pid, rat) => {
            $.get("insertrating.php?rating=" + rat + "&product=" + pid, function(data, status) {
                console.log('pid is ' + pid + ' and rating is ' + rat);
                console.log('data is ' + data);
                var response = data.trim();
                if (response == 'no') {
                    $("#response" + pid).html("you have already rated this item!");
                } else {
                    $("#response" + pid).html(response);
                }
            });
        }

        $(document).ready(function() {
            $("#usr").keyup(function() {
                const qry = $("#usr").val();
                $.get("filter.php?qry=" + qry, function(data, status) {
                    $("#prods").html(data);
                });
            });
        });
    </script>

</head>

<body>
    <div class="container-fluid">
        <header>
            <p class="limited-sale">Limited Time! Select Products and Gym Accessories Additional 20% Off!</p>
            <?php include('nav.php'); ?>
            <!-- Products Carousel  -->
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block w-100" src="images/fallbanner.jpg" alt="First slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Discover Your Unique Fit</h5>
                            <p>Take an assessment now for an additional 20% off your first purchase!</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="images/fitnessroutine.webp" alt="Second slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Discover Your Unique Fit</h5>
                            <p>Take an assessment now for an additional 20% off your first purchase!</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block w-100" src="images/home-gym.jpg" alt="Third slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>Discover Your Unique Fit</h5>
                            <p>Take an assessment now for an additional 20% off your first purchase!</p>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div><!-- Product Carousel End -->
            <!-- Custom Cart -->
            <section class="cart">
                <?php include('customcart.php'); ?>
            </section>
            <!--  Custom Cart Ends -->
        </header>
        <!-- Explore Section  -->
        <section class="container-fluid">
            <h2 class="product-head" style="margin-bottom: 0;">Explore by Category</h2>
            <div class="row">
                <div class="col category-sort">
                    <a href="category.php?ca=1"><img src="images/clothing.png" alt="clothing" /><span>Active Wear</span></a>
                </div>
                <div class="col category-sort">
                    <a href="category.php?ca=2"><img src="images/barbell.png" alt="barbell"><span>Fitness Equipment</span></a>
                </div>
                <div class="col category-sort">
                    <a href="category.php?ca=3"><img src="images/protein.png" alt="protein"><span>Supplements</span></a>
                </div>
            </div>
        </section>
        <!-- Explore Section End -->
        <!-- Shop by Cut Section -->
        <section class="container-fluid">
            <h2 class="product-head">Sort by Cut</h2>
            <div class="row">
                <div class="col-6">
                    <img class="img-fluid" src="images/mencut.jpg" alt="men clothing" style="width: 100%;height: auto;" />
                    <div class="center">
                        <a class="btn" href="shopmen.html">Shop Men</a>
                    </div>
                </div>
                <div class="col-6">
                    <!--  <div class="crop-cut"> -->
                    <img class="img-fluid" src="images/womencut.jpg" alt="women clothing" style="width: 100%;height: 100%;">
                    <!-- </div> -->
                    <div class="center">
                        <a class="btn" href="shopwomen.html">Shop Women</a>
                    </div>
                </div>
            </div>
        </section> <!-- Shop by Cut Section End-->
        <br>
        <!-- Shop by Relaxed Fit Section  -->
        <section class="container-fluid">
            <h2 class="product-head">Shop by <span>FIT</span></h2>
            <h3 class="z-align">Relaxed Fit</h3>
            <div class="row row-cols-sm-2 row-cols-md-4">
                <div class="col mb-auto">
                    <div class="crop-fit-category">
                        <img src="images/relaxedfit.webp" alt="relaxed fit clothing" style="width: 100%;height: 100%;" />
                        <div class="center">
                            <a class="btn" href="relaxedfit.html">Shop Relaxed Fit</a>
                        </div>
                    </div>
                </div>
                <?php foreach ($relaxedproducts as $product) : ?>
                    <?php include('showproduct.php') ?>
                <?php endforeach; ?>
            </div>
        </section> <!-- Shop by Relaxed Fit Section End -->
        <!-- Shop by Slim Fit Section  -->
        <section class="container-fluid ">
            <h3>Slim Fit</h3>
            <div class="row row-cols-sm-2 row-cols-md-4">
                <div class="col mb-auto">
                    <div class="crop-fit-category">
                        <img src="images/slimfit.webp" alt="slim fit clothing" style="width: 100%;height: 100%;" />
                    </div>
                    <div class="center">
                        <a class="btn" href="slimfit.html">Shop Slim Fit</a>
                    </div>
                </div>
                <?php foreach ($slimproducts as $product) : ?>
                    <?php include('showproduct.php') ?>
                <?php endforeach; ?>
            </div>
        </section> <!-- Shop by Slim Fit Section End  -->
        <br>
        <!-- Shop by All Day Wear Section  -->
        <section class="container-fluid">
            <h2 class="product-head">Shop by Comfort</h2>
            <h3 class="z-align">All Day Wear</h3>
            <div class="row row-cols-sm-2 row-cols-md-4">
                <div class="col mb-auto">
                    <div class="crop-fit-category">
                        <img src="images/alldaywear.jpg" alt="all day wear" style="width: 100%;height: 100%;" />
                    </div>
                    <div class="center">
                        <a class="btn" href="alldaywear.html">Shop All Day Wear</a>
                    </div>
                </div>
                <?php foreach ($alldayproducts as $product) :
                ?>
                    <?php include('showproduct.php') ?>
                <?php endforeach; ?>
            </div>
        </section> <!-- Shop by All Day Wear Section End -->
        <!-- Shop by Unisex Wear Section  -->
        <section class="container-fluid">
            <h3>Unisex Wear</h3>
            <div class="row row-cols-sm-2 row-cols-md-4">
                <div class="col mb-auto">
                    <div class="crop-fit-category">
                        <img src="images/unisexwear.jpg" alt="unisex fit clothing" style="width: 100%;height: 100%;" />
                    </div>
                    <div class="center">
                        <a class="btn" href="unisexwear.html">Shop Unisex Wear</a>
                    </div>
                </div>
                <?php foreach ($uniproducts as  $product) :
                ?>
                    <?php include('showproduct.php') ?>
                <?php endforeach; ?>
            </div>
        </section> <!-- Shop by Unisex Wear Section End -->
        <br>
        <section class="container-fluid">
            <h2 class="product-head">Shop Latest</h2>
            <h3 class="z-align">New Arrivals</h3>
            <div class="row row-cols-sm-2 row-cols-md-4">
                <?php
                $getlatest = $dbh->prepare("SELECT product_id, sku, name, price, image FROM products WHERE fitid = 1 ORDER BY product_id DESC LIMIT 4");
                $getlatest->execute();
                $products = $getlatest->fetchAll();
                foreach ($products as $product) :
                    $currprice = $product['price'];
                    $oldprice = $currprice + 5;
                ?>
                    <?php include('showproduct.php') ?>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="container-fluid">
            <h3>Popular Items</h3>
            <div class="row row-cols-sm-2 row-cols-md-4">
                <?php
                $getlatest = $dbh->prepare("SELECT products.product_id, products.sku, products.name, products.price, products.image, 
          AVG(rating.rating) AS AvgScore
          FROM products
          LEFT JOIN rating
          ON rating.productid = products.product_id
          GROUP BY products.product_id, products.name, products.sku, products.price
          ORDER BY rating DESC limit 4");
                $getlatest->execute();
                $products = $getlatest->fetchAll();
                foreach ($products as $product) :
                    $currprice = $product['price'];
                    $oldprice = $currprice + 5;
                ?>
                    <?php include('showproductsale.php') ?>
                <?php endforeach; ?>
            </div>
        </section>
        <footer>
            <?php include('footer.php'); ?>
        </footer>
    </div><!-- End container -->



</body>

</html>