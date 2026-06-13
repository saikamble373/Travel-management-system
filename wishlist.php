<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
	{	
header('location:index.php');
}
else{
if(isset($_REQUEST['delid']))
	{
		$pid=intval($_GET['delid']);
        $email=$_SESSION['login'];
	    $sql ="DELETE FROM tblwishlist WHERE UserEmail=:email and PackageId=:pid";
        $query= $dbh -> prepare($sql);
        $query-> bindParam(':email', $email, PDO::PARAM_STR);
        $query-> bindParam(':pid', $pid, PDO::PARAM_STR);
        $query-> execute();
        $msg="Package removed from wishlist";
    }

?>
<!DOCTYPE HTML>
<html>
<head>
<title>TravelMate | My Wishlist</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href="css/theme.css" rel='stylesheet' type='text/css' />
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,700,600' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<link href="css/font-awesome.css" rel="stylesheet">
<!-- Custom Theme files -->
<script src="js/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<!--animate-->
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
	<script>
		 new WOW().init();
	</script>

  <style>
		.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>
</head>
<body>
<?php include('includes/header.php');?>

<div class="tm-page-hero">
	<h1 class="wow zoomIn animated" data-wow-delay=".5s">My Wishlist</h1>
	<p>Your saved dream destinations</p>
</div>
<!--- privacy ---->
<div class="privacy">
	<div class="container">
		<h3 class="wow fadeInDown animated animated" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: fadeInDown;" >My Wishlist</h3>
		<form name="chngpwd" method="post" onSubmit="return valid();">
		 <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
	<p>
	<table border="1" width="100%" class="table">
<tr align="center">
<th>#</th>
<th>Image</th>
<th>Package Name</th>	
<th>Type</th>
<th>Price</th>
<th>Added Date</th>
<th>Action</th>
</tr>
<?php 

$uemail=$_SESSION['login'];
$sql = "SELECT tblwishlist.PackageId as pkgid,tbltourpackages.PackageName as packagename,tbltourpackages.PackageType as pkgtype,tbltourpackages.PackagePrice as pkgprice,tbltourpackages.PackageImage as pkgimage,tblwishlist.PostingDate as postingdate from tblwishlist join tbltourpackages on tbltourpackages.PackageId=tblwishlist.PackageId where tblwishlist.UserEmail=:uemail";
$query = $dbh->prepare($sql);
$query -> bindParam(':uemail', $uemail, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{	?>
<tr align="center">
<td><?php echo htmlentities($cnt);?></td>
<td><img src="admin/pacakgeimages/<?php echo htmlentities($result->pkgimage);?>" width="100" /></td>
<td><a href="package-details.php?pkgid=<?php echo htmlentities($result->pkgid);?>"><?php echo htmlentities($result->packagename);?></a></td>
<td><?php echo htmlentities($result->pkgtype);?></td>
<td>Rs <?php echo htmlentities($result->pkgprice);?></td>
<td><?php echo htmlentities($result->postingdate);?></td>

<td>
    <a href="package-details.php?pkgid=<?php echo htmlentities($result->pkgid);?>" class="btn btn-primary" style="margin-right: 5px;">View</a>
    <a href="wishlist.php?delid=<?php echo htmlentities($result->pkgid);?>" onclick="return confirm('Do you really want to remove this package from wishlist?')" class="btn btn-danger">Remove</a>
</td>
</tr>
<?php $cnt=$cnt+1; }} else { ?>
	<tr><td colspan="7" align="center">Your wishlist is empty.</td></tr>
<?php } ?>
	</table>
		
			</p>
			</form>

		
	</div>
</div>
<!--- /privacy ---->
<!--- footer-top ---->
<!--- /footer-top ---->
<?php include('includes/footer.php');?>
<!-- signup -->
<?php include('includes/signup.php');?>			
<!-- //signu -->
<!-- signin -->
<?php include('includes/signin.php');?>			
<!-- //signin -->
<!-- write us -->
<?php include('includes/write-us.php');?>
</body>
</html>
<?php } ?>

