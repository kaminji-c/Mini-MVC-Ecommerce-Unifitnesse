

//Empty All
if($action=='emptyall') {
	$_SESSION['products'] =array();
	header("Location:index.php");	
}

//Empty one by one
if($action=='empty') {
	$sku = $_GET['sku'];
	$products = $_SESSION['products'];
	unset($products[$sku]);
  $_SESSION['products']= $products;
	header("Location:index.php");	
}
?>