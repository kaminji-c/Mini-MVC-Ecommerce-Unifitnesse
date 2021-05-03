<?php session_start(); ?>

<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="index.php">
        <img class="logo" src="branding/unilogo-lg.png" alt="Unifitnesse brand logo" style="width: 100%; height: auto;"></a>
    </a>
    <button class="navbar-toggler toggle-btn" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse main-nav" id="navbarTogglerDemo03">
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
            <?php
            //select fitid, category from categories
            $getcat = $dbh->prepare("SELECT id, category FROM categories");
            $getcat->execute();
            while ($getcatrow = $getcat->fetch()) {
                $catid = $getcatrow['id'];
                $catname = $getcatrow['category'];
                $trimcatname = str_replace(' ', '', $catname);
                echo '<li><a class="nav-link" href="' . $trimcatname . '">' . $catname . '</a></li>';
                /*  var_dump($catname); */
            }
            $numcart = count($_SESSION['products']);
            /* $numcart = $numcart + $product['qty']; */
            ?>
        </ul>
    </div>
    <ul class="main-nav-func">
        <li class="main-search">
            <form class="form-inline my-2 my-lg-0" action="search.php">
                <input class="form-control mr-sm-2" type="text" placeholder="Search a Product..." name="qry" aria-label="Search">
                <button class="btn my-2 my-sm-0" type="submit">Go!</button>
            </form>
        </li>
        <!--   <?php  //variables we want for total cart items
                $cartqry = "SELECT cartitems.id, cartitems.productid, cartitems.sessionid, cartitems.qty, products.product_id, products.price
       FROM cartitems, products
       WHERE cartitems.productid = '$productid' and cartitems.sessionid = '$sessid' ";
                $cartstmt = $dbh->prepare($cartqry);
                $cartstmt->execute();
                $totalcart = $cartstmt->fetch();

                $prod_price = $totalcart['price'] * $qty;
                echo "price of item is $prod_price<br><br>";

                $prod_qty = $totalcart['qty'];
                echo "number of items in cart is $prod_qty<br>";

                $amount = ($prod_price * $prod_qty);
                echo "the total amount in cart should be$amount<br>";    /* $totalcart = 
                echo "<b>$totalcart<b> is the amount of cart items" ?>
    
                <?php
                $numcart = count($_SESSION['products']);
                echo "<p>The numcart is $numcart</p>";
                $total_qty = $_GET['qty'];
                echo "<p>The qty is $total_qty</p>";
                $cart_total = ($numcart * $total_qty);
                echo "<p>The cart qty is $cart_total</p>"; */
                ?>-->


        <li><a class="maincart" href="checkout.php"><i class="fas fa-shopping-bag fa-xl"></i> Cart (<?php echo $numcart; ?>)</a></li>
    </ul>
</nav>