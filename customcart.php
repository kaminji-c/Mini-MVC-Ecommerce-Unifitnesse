<!-- In this document: 
- Variables foreach loop defined
- Container for Custom Cart
-->

<div class="cart-container>
<?php if (!empty($_SESSION['products'])) : ?>
        <nav class=" navbar navbar-inverse">
    <div class="container-fluid pull-left" style="width:1040px;">
        <div class="navbar-header"> <a class="navbar-brand" href="#" style="text-align: center;">Shopping Cart</a> </div>
    </div>
    <div class="pull-right" style="margin-top:7px;margin-right:7px;">
        <a href="index.php?action=emptyall" class="btn btn-info">Empty cart</a>
        <a href="checkout.php" class="btn btn-info">Check Out</a></div>
</div>
</nav>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Actions</th>
        </tr>
    </thead>
    <?php foreach ($_SESSION['products'] as $key => $product) : ?>
        <tr>
            <td><img src="<?php print $product['image'] ?>" width="50"></td>
            <td><?php print $product['name'] ?></td>
            <td>$<?php print $product['price'] ?></td>
            <td><?php print $product['qty'] ?></td>
            <td><a href="index.php?action=empty&sku=<?php print $key ?>" class="btn btn-info">Delete</a></td>
        </tr>
        <?php $total = $total + ($product['price'] * $product['qty']); ?>
    <?php endforeach; ?>
    <tr>
        <td colspan="5" align="right">
            <h4>Total:$<?php print $total ?></h4>
        </td>
    </tr>
</table>
<?php endif; ?>
</nav>
</div>