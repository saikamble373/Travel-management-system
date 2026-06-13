<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TravelMate | Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href="css/admin-theme.css" rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="css/morris.css" type="text/css"/>
<!-- Graph CSS -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- jQuery -->
<script src="js/jquery-2.1.4.min.js"></script>
<!-- //jQuery -->
<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<!-- lined-icons -->
<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
<!-- //lined-icons -->
</head> 
<body>
   <div class="page-container">
   <!--/content-inner-->
<div class="left-content">
	   <div class="mother-grid-inner">
<!--header start here-->
<?php include('includes/header.php');?>
<!--header end here-->
		<ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a> <i class="fa fa-angle-right"></i></li>
            </ol>
<!--four-grids here-->
		<div class="four-grids">
			<a href="manage-users.php" target="_blank">
					<div class="col-md-4 four-grid">
						<div class="four-agileits">
							<div class="icon">
								<i class="glyphicon glyphicon-user" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>User</h3>

								<?php $sql = "SELECT id from tblusers";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=$query->rowCount();
					?>			<h4> <?php echo htmlentities($cnt);?> </h4>
				
								
							</div>
							
						</div>
					</div>
				</a>


<a href="manageissues.php" target="_blank">
						<div class="col-md-4 four-grid">
						<div class="four-w3ls">
							<div class="icon">
								<i class="glyphicon glyphicon-folder-open" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>Issues Riaised</h3>
												<?php $sql5 = "SELECT id from tblissues";
$query5= $dbh -> prepare($sql5);
$query5->execute();
$results5=$query5->fetchAll(PDO::FETCH_OBJ);
$cnt5=$query5->rowCount();
					?>
								<h4><?php echo htmlentities($cnt5);?></h4>
								
							</div>
							
						</div>
					</div></a>


<a href="manage-packages.php" target="_blank">
					<div class="col-md-4 four-grid">
						<div class="four-wthree">
							<div class="icon">
								<i class="glyphicon glyphicon-briefcase" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>Toatal packages</h3>
																	<?php $sql3 = "SELECT PackageId from tbltourpackages";
$query3= $dbh -> prepare($sql3);
$query3->execute();
$results3=$query3->fetchAll(PDO::FETCH_OBJ);
$cnt3=$query3->rowCount();
					?>
								<h4><?php echo htmlentities($cnt3);?></h4>
								
							</div>
							
						</div>
					</div>
						<div class="clearfix"></div>
				</div>
</a>
	

	<a href="manage-enquires.php" target="_blank">
	<div class="four-grids">
			<div class="col-md-4 four-grid">
						<div class="four-agileinfo">
							<div class="icon">
								<i class="glyphicon glyphicon-folder-open" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>Enquiries</h3>
												<?php $sql2 = "SELECT id from tblenquiry";
$query2= $dbh -> prepare($sql2);
$query2->execute();
$results2=$query2->fetchAll(PDO::FETCH_OBJ);
$cnt2=$query2->rowCount();
					?>
								<h4><?php echo htmlentities($cnt2);?></h4>
								
							</div>
							
						</div>
					</div>
				</a>


	<a href="manage-enquires.php" target="_blank">
			<div class="col-md-4 four-grid">
						<div class="four-agileits">
							<div class="icon">
								<i class="glyphicon glyphicon-folder-open" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>New Enquiries</h3>

								<?php $sql ="SELECT id from tblenquiry where (Status is null || Status='')";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$newenq=$query->rowCount();
					?>			<h4> <?php echo htmlentities($newenq);?> </h4>	
							</div>
						</div>
					</div></a>

	<a href="manage-enquires.php" target="_blank">
						<div class="col-md-4 four-grid">
						<div class="four-w3ls">
							<div class="icon">
								<i class="glyphicon glyphicon-folder-open" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>Read Enquiries</h3>
												<?php $sql5 ="SELECT id from tblenquiry where (Status='1')";
$query5= $dbh -> prepare($sql5);
$query5->execute();
$results5=$query5->fetchAll(PDO::FETCH_OBJ);
$redenq=$query5->rowCount();
					?>
								<h4><?php echo htmlentities($redenq);?></h4>
								
							</div>
							
						</div>
					</div>
				</a>
<div class="clearfix"></div>
				</div>
	<div class="four-grids">

	<a href="manage-bookings.php" target="_blank">
								<div class="col-md-3 four-grid">
						<div class="four-agileinfo">
							<div class="icon">
								<i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>Bookings</h3>
										<?php $sql1 = "SELECT BookingId from tblbooking";
$query1 = $dbh -> prepare($sql1);
$query1->execute();
$results1=$query1->fetchAll(PDO::FETCH_OBJ);
$cnt1=$query1->rowCount();
					?>
								<h4><?php echo htmlentities($cnt1);?></h4>

							</div>
							
						</div>
					</div></a>
		<a href="manage-bookings.php" target="_blank">
		<div class="col-md-3 four-grid">
						<div class="four-wthree" style="color:#ffc107 !important">
							<div class="icon">
								<i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>New Bookings</h3>

								<?php $sql ="SELECT BookingId from tblbooking where (status is null || status='')";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$newbookings=$query->rowCount();
					?>			<h4> <?php echo htmlentities($newbookings);?> </h4>	
							</div>
						</div>
					</div>
</a>

	<a href="manage-bookings.php" target="_blank">

		<div class="col-md-3 four-grid">
						<div class="four-agileits">
							<div class="icon">
								<i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>Cancelled Bookings</h3>

								<?php $sql ="SELECT BookingId from tblbooking where (status='2')";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cancelbooking=$query->rowCount();
					?>			<h4> <?php echo htmlentities($cancelbooking);?> </h4>	
							</div>
						</div>
					</div>
</a>
	<a href="manage-bookings.php" target="_blank">
						<div class="col-md-3 four-grid">
						<div class="four-w3ls">
							<div class="icon">
								<i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i>
							</div>
							<div class="four-text">
								<h3>Confirmed Bookings</h3>

								<?php $sql ="SELECT BookingId from tblbooking where (status='1')";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cancelbooking=$query->rowCount();
					?>			<h4> <?php echo htmlentities($cancelbooking);?> </h4>	
							</div>
						</div>
					</div>
				</a>
					<div class="clearfix"></div>
				</div>
<!--//four-grids here-->


<div class="inner-block">

</div>
<!--inner block end here-->
<!--copy rights start here-->
<?php include('includes/footer.php');?>
</div>
</div>

			<!--/sidebar-menu-->
				<?php include('includes/sidebarmenu.php');?>
							  <div class="clearfix"></div>		
							</div>
							<script>
							var toggle = true;
										
							$(".sidebar-icon").click(function() {                
							  if (toggle)
							  {
								$(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
								$("#menu span").css({"position":"absolute"});
							  }
							  else
							  {
								$(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
								setTimeout(function() {
								  $("#menu span").css({"position":"relative"});
								}, 400);
							  }
											
											toggle = !toggle;
										});
							</script>
<!--js -->
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<!-- Bootstrap Core JavaScript -->
   <script src="js/bootstrap.min.js"></script>
   <!-- /Bootstrap Core JavaScript -->	   
<!-- morris JavaScript -->	
<script src="js/raphael-min.js"></script>
<script src="js/morris.js"></script>
<?php
	// Fetch dynamic booking data for the chart
	$chartSql = "
		SELECT 
			DATE_FORMAT(RegDate, '%Y-%m') as period, 
			COUNT(BookingId) as total
		FROM tblbooking 
		GROUP BY DATE_FORMAT(RegDate, '%Y-%m') 
		ORDER BY period ASC LIMIT 12
	";
	// Note: using try-catch so it won't crash if RegDate is named differently in the user's variant
	try {
		$chartQuery = $dbh->prepare($chartSql);
		$chartQuery->execute();
		$chartResults = $chartQuery->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		$chartResults = [];
	}
	
	// Create chart data array
	if(empty($chartResults)) {
		// Provide fallback data so chart doesn't break if table is empty
		$chartResults = [
			['period' => '2026-01', 'total' => 2],
			['period' => '2026-02', 'total' => 5],
			['period' => '2026-03', 'total' => 8],
			['period' => '2026-04', 'total' => 12]
		];
	}
?>
<script>
	$(document).ready(function() {
		//BOX BUTTON SHOW AND CLOSE
	   jQuery('.small-graph-box').hover(function() {
		  jQuery(this).find('.box-button').fadeIn('fast');
	   }, function() {
		  jQuery(this).find('.box-button').fadeOut('fast');
	   });
	   jQuery('.small-graph-box .box-close').click(function() {
		  jQuery(this).closest('.small-graph-box').fadeOut(200);
		  return false;
	   });
	   
	    //CHARTS
		var bookingData = <?php echo json_encode($chartResults); ?>;
		
		graphArea2 = Morris.Area({
			element: 'hero-area',
			padding: 10,
        behaveLikeLine: true,
        gridEnabled: false,
        gridLineColor: '#dddddd',
        axes: true,
        resize: true,
        smooth:true,
        pointSize: 4,
        lineWidth: 2,
        fillOpacity:0.85,
			data: bookingData,
			lineColors:['#0ea5e9'],
			xkey: 'period',
            redraw: true,
            ykeys: ['total'],
            labels: ['Total Bookings'],
			pointSize: 3,
			hideHover: 'auto',
			resize: true
		});
		
	   
	});
	</script>
</body>
</html>
<?php } ?>