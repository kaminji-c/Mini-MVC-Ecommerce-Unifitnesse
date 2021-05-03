<?php
//Database connection, Used PDO
include 'connect.php';
//var_dump($_SESSION);
$total = 0;
$currcat = $_GET['ca'];
/* print_r($_GET); */
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
  header("Location:category.php");
}
//Empty All
if ($action == 'emptyall') {
  $_SESSION['products'] = array();
  header("Location:category.php");
}

//Empty one by one
if ($action == 'empty') {
  $sku = $_GET['sku'];
  $products = $_SESSION['products'];
  unset($products[$sku]);
  $_SESSION['products'] = $products;
  header("Location:category.php");
}
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
  <header>
    <?php include('nav.php'); ?>
    <!-- Products Carousel  -->
    <?php
    $getbanner = $dbh->prepare(
      "SELECT categories.id, categories.category, categories.categorydesc, categories.banner, categories.header, products.fitid FROM categories, products WHERE products.fitid = categories.id AND fitid = ? GROUP BY products.fitid"
    );
    $getbanner->bindParam(1, $currcat, PDO::PARAM_INT);
    $getbanner->execute();
    $banner = $getbanner->fetch();
    $cover = $banner['banner'];
    $coverdesc = $banner['categorydesc'];
    $header = $banner['header'];
    ?>

    <div class="banner-container">
      <img src="<?php echo $cover; ?>" class="img-fluid" alt="Responsive image" style="width: 100%; height: auto;">
      <aside class="promo-container after">
        <h3><?php echo $header; ?></h3>
        <p><?php echo $coverdesc; ?></p>
      </aside>
    </div>
  </header>
  <section class="container-fluid">
    <?php
    $getbycat = $dbh->prepare("SELECT products.product_id, products.sku, products.name, products.price, products.image, categories.category, categories.banner FROM products, categories WHERE products.fitid = categories.id AND fitid = ?");
    $getbycat->bindParam(1, $currcat, PDO::PARAM_INT);
    $getbycat->execute();
    $products = $getbycat->fetchAll();
    $cover = $products['banner'];

    ?>
    <h3><?php echo $products[0]['category']; ?></h3>
    <div class="row row-cols-sm-2 row-cols-md-4">


      <?php
      foreach ($products as $product) :
        $currprice = $product['price'];
        $oldprice = $currprice + 5;
        $pid = $product['product_id'];
        $percentoff = ($oldprice - $currprice) * 100;

      ?>

        <?php include('showproductsale.php') ?>
      <?php endforeach; ?>
    </div>
  </section>
  <footer>
    <?php include('footer.php'); ?>
  </footer>
</body>

</html>