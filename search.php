<?php
/* Connect to the database */
include('connect.php');

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
?>
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
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;1,700&family=Open+Sans:ital,wght@0,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/nko4kwz.css">
    <!--  Ajax Rating System -->
    <script src="https://code.jquery.com/jquery-3.5.0.min.js" integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ=" crossorigin="anonymous"></script> <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;1,700&family=Open+Sans:ital,wght@0,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/nko4kwz.css">
    <title>Uniftinesse</title>

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
</head>

<body>
    <div class="container">
        <header>
            <?php include('nav.php'); ?>
        </header>
        <section class="prod-container">
            <?php $qry = $_GET['qry'];
            $getsearch = $dbh->prepare("SELECT products.product_id, products.name, products.image, products.price, products.descrip 
                FROM products, categories 
                WHERE (products.name LIKE '%$qry%'OR products.descrip LIKE '%$qry%'OR categories.category LIKE '%$qry%' OR categories.categorydesc LIKE '%$qry%') AND categories.id = products.fitid");
            $getsearch->execute();
            $products = $getsearch->fetchAll();
            $num = count($products);

            /*  var_dump(count($products)); */ ?>

            <?php echo '<h3>Your search for <span class="search-keyword">' . '"' . $qry . '"' . ' </span> resulted in ' . $num . ' results </h3>';
            ?>
            <div class="row row-cols-sm-2 row-cols-md-4">
                <!--  Sort by functionality  -->
                <div class="dropdown">
                    <button class="btn btn-info dropdown-toggle float-right" type="button" data-toggle="dropdown">Sort by</button>
                    <span class="caret"></span>
                    <ul class="dropdown-menu">
                        <li><a href="search.php?sort=price">Price</a></li>
                        <li><a href="search.php?sort=alpha">A-Z</a></li>
                    </ul>
                </div>
                <?php
                /*  $qry = $_GET['qry'];
                $getsearch = $dbh->prepare("SELECT products.product_id, products.name, products.image, products.price, products.descrip 
                FROM products, categories 
                WHERE (products.name LIKE '%$qry%'OR products.descrip LIKE '%$qry%'OR categories.category LIKE '%$qry%' OR categories.categorydesc LIKE '%$qry%') AND categories.id = products.fitid");
                $getsearch->execute();
                $products = $getsearch->fetchAll(); */
                foreach ($products as $product) :
                    $currprice = $product['price'];
                    $oldprice = $currprice + 5;
                    $pid = $product['product_id'];
                    /*  $name = $product['name'];
                    $lcname = strtolower($name);  lowercase product name 
                    $newstring = str_replace(' ', '-', $lcname);  replace spaces with dashes 
                    echo   '<a href="product.php?pid=' . $pid . '-' . $newstring . ' "</a>'; */
                    /*   echo   '<a href="product.php?pid=' . $pid . ' "</a>'; */
                ?>

                    <?php include('showproduct.php') ?>
                <?php endforeach; ?>

            </div>
        </section>
        <footer>
            <?php include('footer.php'); ?>
        </footer>
    </div><!-- Container end -->
</body>