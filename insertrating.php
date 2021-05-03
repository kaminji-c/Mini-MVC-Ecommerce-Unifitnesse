	<?
	$dbh = new PDO(//add your own database info);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$prodid = $_GET['product'];
	$rating = $_GET['rating'];
	$ip = $_SERVER['REMOTE_ADDR'];
	//check ip address
	$checksql = $dbh->prepare("select count(id) from rating where ipaddress = ? and productid = ?");
	$checksql->execute(array($ip,$prodid));
	$numcheck = $checksql->fetch();
	$numrows = $numcheck[0];
     
	if ($numrows < 1){
	$inssql = $dbh->prepare("insert into rating(rating,ipaddress,productid) values (?,?,?)");
	$inssql->execute(array($rating,$ip,$prodid));
	//

	$sql = $dbh->prepare("SELECT avg(rating) as average from rating where productid = ?");
	$sql->bindValue(1,$prodid);
	$sql->execute();
	$row = $sql->fetch();
	$avg = $row['average']; 
	$i=1;
	while ($i<=5){
	if ($i <= ceil($avg))	
	{
	echo  '<i class="far fa-star" onclick="sendRequest(\'' . $pid . '\',\'' . $i . '\');"
            style="font-weight:900"></i>';
	}
	else      
	{            
	echo  '<i class="far fa-star" onclick="sendRequest(\'' . $pid . '\',\'' . $i . '\');"
            style="font-weight:400"></i>';	                      
	}
	$i++;
	}
	}
	else {
		echo "no";
	}

	?>