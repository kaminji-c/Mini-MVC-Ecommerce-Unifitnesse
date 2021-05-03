<?php
//This script populates the navigation bar with categories from the db
// Script displays products on sale
//Connect to database, session start, error handling
include('connect.php');

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


//Get all Products 
$query = "SELECT * FROM products $order";
$stmt = $dbh->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll();

?>

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
</script>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Uniftinesse provides the comfort of fitness clothing that fit the best you regardless if you are bulking or trying to slim down.">
    <meta name="keywords" content="fitness clothing, unique fit, gym attire, slim fit, relaxed fit, all day wear, unisex wear, fitness home equipment, supplement">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/fcf1955543.js" crossorigin="anonymous"></script>
    <!--  Ajax Rating System -->
    <script src="https://code.jquery.com/jquery-3.5.0.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script> <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;1,700&family=Open+Sans:ital,wght@0,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/nko4kwz.css">
    <title>Uniftinesse</title>
</head>

<body>
    <nav>
        <ul>
            <li><a href="index.php"><img class="logo" src="images/unifitnesselogo.png" alt="Unifitnesse brand logo" style="width: 100%; height:auto;"></a></li>
            <?php
            //select fitid, category from categories
            $getfit = $dbh->prepare("SELECT fitid, category FROM categories");
            $getfit->execute();
            while ($getfitrow = $getfit->fetch()) {
                $fitid = $getfitrow['fitid'];
                $fitname = $getfitrow['category'];
                echo '<li><a href="categories.php?ca=' . $fitid . '">' . $fitname . '</a></li>';
            }
            ?>
            <li><a href="findfit.html">Find Your Fit</a></li>
            <li><a href="about.html">About</a></li>
            <div class="topnav-right">
                <li>
                    <label for="usr"></label>
                    <input type="text" id="usr" placeholder="Search Product">
                    <button type="submit" class="btn btn-dark btn-sm">Go!</button>
                </li>

                <li><a href="checkout.php"><i class="fas fa-shopping-bag fa-xl"></i></a></li>
                <li><a href="account.html"><i class="fas fa-user-circle fa-xl"></i></a></li>
            </div>
        </ul>
    </nav>
    <section class="prod-container">
        <h3>All Products</h3>
        <div class="row row-cols-sm-2 row-cols-md-4">
            <?php foreach ($products as  $product) : ?>
                <!-- Product Display -->
                <div class="col show">
                    <div class="item-sale-sticker"><span>SALE</span></div>
                    <div class="crop-fit">
                        <img src="<?php print $product['image'] ?>" alt="fitness clothing" style="width: 100%;height: 100%;">
                    </div>
                    <div class="caption">
                        <span style="text-align:center;"><?php print $product['name'] ?></span>
                        <span style="text-decoration: line-through;"><?php echo $oldprice; ?></span>
                        <span style="text-align:center;color:#000;"><b>$<?php echo $currprice; ?></b></span>
                    </div>
                    <!--  Rating, Buy now, Add now Button Wrapper -->
                    <section class="product-buy-section">
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
                        </form> <!-- End Add to Cart -->
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
                            <button type="submit" class="btn btn-dark btn-outline-dark chkbtn">Buy Now</button>
                        </form> <!-- End Buy Now -->
                    </section>
                </div><!--  End Col -->

            <?php endforeach; ?>
    </section> <!-- End Section -->
    </div> <!-- End Row -->
</body>

</html>