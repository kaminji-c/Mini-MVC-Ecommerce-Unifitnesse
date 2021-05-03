<?php
// Database connection 
include('connect.php');

$total = 0;

//write a query of all the data that we need: (name, itemnumber, itemprice) || WHERE cart is valid &= sessions table
$getcart = $dbh->prepare("SELECT products.name, products.product_id, products.price, products.descrip, products.image, cartitems.qty
FROM products, cartitems
WHERE cartitems.productid = products.product_id and cartitems.sessionid = '$sessid'");

$getcart->execute();
$numcart = count($sessid['products'] * $product['qty']);
/* echo "The number of items in the cart are  $numcart"; */

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



//variable stored to use in html
$itemname = $getcartrow['name'];
$itemnumber = $getcartrow['product_id'];
$price = $getcartrow['price'];
$qty = $getcartrow['qty'];
$desc = $getcartrow['descrip'];
$image = $getcartrow['image'];
// var_dump($getcartrow); 
//var_dump($_SESSION); 
/* session_start();
 $numcart = count($_SESSION['products']); */

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Unifitnesse | Checkout</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;1,700&family=Open+Sans:ital,wght@0,400;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/nko4kwz.css">

    <!-- Favicons: Replace Later -->
    <link rel="apple-touch-icon" href="/docs/4.4/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="/docs/4.4/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="/docs/4.4/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="manifest" href="/docs/4.4/assets/img/favicons/manifest.json">
    <link rel="mask-icon" href="/docs/4.4/assets/img/favicons/safari-pinned-tab.svg" color="#563d7c">
    <link rel="icon" href="/docs/4.4/assets/img/favicons/favicon.ico">
    <meta name="msapplication-config" content="/docs/4.4/assets/img/favicons/browserconfig.xml">
    <meta name="theme-color" content="#563d7c">

    <style>
        .container {
            max-width: 960px;
        }

        .lh-condensed {
            line-height: 1.25;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
    <!-- Custom styles for this template  -->
    <link href="form-validation.css" rel="stylesheet">
</head>

<div class="container" id="example1">
    <div class="row">
        <div class="col-md-4 order-md-2 mb-4">
            <form action="https://www.sandbox.paypal.com/us/cgi-bin/webscr" method="post" target="paypal" class="form-validation">
                <input type="hidden" name="cmd" value="_ext-enter" />
                <input type="hidden" name="redirect_cmd" value="_xclick" />
                <input type="hidden" name="cancel_return" value="http://cdelcarmendev.com/testcart" />
                <input type="hidden" name="return" value="http://cdelcarmendev.com/testcart" />
                <input type="hidden" name="business" value="camerondelcarmen@gmail.com">
                <input type="hidden" name="upload" value="1">
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="item_name_1" value="<?php echo $itemname; ?>">
                <input type="hidden" name="item_number_1" value="<?php echo $itemnumber; ?>">
                <input type="hidden" name="amount_1" value="<?php echo $price; ?>">
                <input type="hidden" name="quantity_1" value="<?php echo $qty; ?>">
                <input type="hidden" name="shipping_1" value="">
                <input type="hidden" id="subtotal" name="subtotal" value="<?php echo $price; ?>">

                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Your cart</span>
                    <span class="badge badge-secondary badge-pill"><?php echo $numcart; ?></span>
                </h4>
                <ul class="list-group mb-3">
                    <?php
                    $total = 0;
                    $i = 1;
                    //this is where we will populate what is seen and what is not seen!
                    while ($getcartrow = $getcart->fetch()) {
                        $itemname = $getcartrow['name'];
                        $itemnumber = $getcartrow['product_id'];
                        $price = $getcartrow['price'];
                        $qty = $getcartrow['qty'];
                        if ($qty == 0) $qty = 1;
                        $desc = $getcartrow['descrip'];
                        $image = $getcartrow['image'];
                        $total += $price * $qty;
                        if ($numcart > 1) {
                            $out = '_' . $i;
                        } else {
                            $out = '';
                        }
                    ?>
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div>
                                <h6 class="my-0"><?php echo  $itemname; ?></h6>
                                <small class="text-muted"><?php echo  $desc; ?></small>
                            </div>
                            <span class="text-muted"><?php echo  $qty . ' @ $' . $price; ?></span>
                        </li>
                    <?php
                        echo '<input type = "hidden" name = "item_name' . $out . '" value = "' . $itemname . '" />
                              <input type = "hidden" name = "quantity' . $out . '" value = "' . $qty . '" /> 
                              <input type = "hidden" name = "amount' . $out . '" value = "' . $price . '" /> ';
                        $i++;
                    }
                    ?>

                    <!--<li class="list-group-item d-flex justify-content-between bg-light">
          <div class="text-success">
            <h6 class="my-0">Promo code</h6>
            <small>EXAMPLECODE</small>
          </div>
          <span class="text-success">-$5</span>
        </li>-->
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Total (USD)</span>
                        <strong>$<?php echo $total; ?></strong>
                    </li>
                </ul>
                <!--
      <form class="card p-2">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Promo code">
          <div class="input-group-append">
            <button type="submit" class="btn btn-secondary">Redeem</button>
          </div>
        </div>
      </form>
    -->
        </div>
        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Billing address</h4>
            <form class="needs-validation" action="https://www.sandbox.paypal.com/us/cgi-bin/webscr" method="post" name="_xclick">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstName">First name</label>
                        <input type="text" name="first_name" class="form-control" id="firstName" placeholder="" value="">
                        <div class="invalid-feedback">
                            Valid first name is required.
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="lastName">Last name</label>
                        <input type="text" name="last_name" class="form-control" id="lastName" placeholder="" value="">
                        <div class="invalid-feedback">
                            Valid last name is required.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email">Email <span class="text-muted">(Optional)</span></label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="you@example.com">
                    <div class="invalid-feedback">
                        Please enter a valid email address for shipping updates.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" name="address1" id="address" placeholder="1234 Main St">
                    <div class="invalid-feedback">
                        Please enter your shipping address.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address2">Address 2 <span class="text-muted">(Optional)</span></label>
                    <input type="text" class="form-control" name="address2" id="address2" placeholder="Apartment or suite">
                </div>

                <div class="row">
                    <div class="col-md-5 mb-3" id="City" style="display:none">
                        <label for="city">City</label>
                        <input type="text" class="form-control" name="city" placeholder="City">
                        <div class="invalid-feedback">
                            Please select a valid city.
                        </div>
                    </div>
                    <div class="col-md-4 mb-3" id="State" style="display:none">
                        <label for="state">State</label>
                        <input type="text" class="form-control" name="state" placeholder="State">
                        <div class="invalid-feedback">
                            Please provide a valid state.
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="zip">Zip</label>
                        <input type="text" class="form-control" name="zip" id="zip" placeholder="">
                        <div class="invalid-feedback">
                            Zip code required.
                        </div>
                    </div>
                </div>
                <hr class="mb-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="same-address">
                    <label class="custom-control-label" for="same-address">Shipping address is the same as my billing address</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="save-info">
                    <label class="custom-control-label" for="save-info">Save this information for next time</label>
                </div>
                <hr class="mb-4">
                <button class="btn btn-primary btn-lg btn-block type=" submit">Continue to checkout</button>


            </form>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script>
        $(function() {
            // IMPORTANT: Fill in your client key
            var clientKey = "js-4VcOI8BtJWWBYJHL1A1X27ceekIqAMetPPzAUFjAXdqEQHnOvdSSkLdiEEKe2Why";
            //0K5ZzXYqmPdW00NCZ2kbc051QJsQr1loLI1ZgIwqv3ClxwSp2mbPh4cqIW4Jfsyr (app key for login)
            var cache = {};
            var container = $("#example1");
            var errorDiv = container.find("div.text-error");

            /** Handle successful response */
            function handleResp(data) {
                // Check for errorDiv
                console.log(data);

                container.find("input[name='city']").val(data.city);
                container.find("input[name='state']").val(data.state);
                $("#City").css('display', 'inline');
                $("#State").css('display', 'inline')


            }

            // Set up event handlers
            container.find("input[name='zip']").on("keyup change", function() {
                // Get zip code
                var zipcode = $(this).val().substring(0, 5);
                if (zipcode.length == 5 && /^[0-9]+$/.test(zipcode)) {
                    // Clear error
                    errorDiv.empty();

                    // Check cache
                    if (zipcode in cache) {
                        handleResp(cache[zipcode]);
                    } else {
                        // Build url
                        var url = "https://www.zipcodeapi.com/rest/" + clientKey + "/info.json/" + zipcode + "/radians";

                        // Make AJAX request
                        $.ajax({
                            "url": url,
                            "dataType": "json"
                        }).done(function(data) {
                            handleResp(data);

                            // Store in cache
                            cache[zipcode] = data;
                        }).fail(function(data) {
                            if (data.responseText && (json = $.parseJSON(data.responseText))) {
                                // Store in cache
                                cache[zipcode] = json;

                                // Check for error
                                if (json.error_msg)
                                    errorDiv.text(json.error_msg);
                            } else
                                errorDiv.text('Request failed.');
                        });
                    }
                }
            }).trigger("change");
        });
    </script>
    </script>
    </body>

</html>