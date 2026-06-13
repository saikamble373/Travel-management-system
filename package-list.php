<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Handle Search and Filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$price_max = isset($_GET['price_max']) ? intval($_GET['price_max']) : '';

$sql = "SELECT * from tbltourpackages WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (PackageName LIKE :search OR PackageLocation LIKE :search)";
}
if (!empty($type)) {
    $sql .= " AND PackageType = :type";
}
if (!empty($price_max)) {
    $sql .= " AND PackagePrice <= :price_max";
}

$query = $dbh->prepare($sql);
if (!empty($search)) {
    $searchParam = "%$search%";
    $query->bindParam(':search', $searchParam, PDO::PARAM_STR);
}
if (!empty($type)) {
    $query->bindParam(':type', $type, PDO::PARAM_STR);
}
if (!empty($price_max)) {
    $query->bindParam(':price_max', $price_max, PDO::PARAM_INT);
}

$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

// Get distinct types for filter
$typeSql = "SELECT DISTINCT PackageType FROM tbltourpackages";
$typeQuery = $dbh->prepare($typeSql);
$typeQuery->execute();
$types = $typeQuery->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TravelMate | Package List</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href="css/theme.css" rel="stylesheet" type="text/css" /> <!-- PREMIUM THEME -->
<link href="css/font-awesome.css" rel="stylesheet">
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/wow.min.js"></script>
<script> new WOW().init(); </script>
<style>
.row.filter-row {
	display:flex;
	align-items:center;
	gap:10px;
}
@media (max-width: 768px) {
	.row.filter-row {
		flex-direction: column;
	}
	.row.filter-row .col-md-4, .row.filter-row .col-md-3, .row.filter-row .col-md-2 {
		width: 100%;
		margin-bottom: 15px;
	}
}
</style>
</head>
<body>
<?php include('includes/header.php');?>

<div class="tm-page-hero wow fadeInDown" data-wow-delay=".3s">
    <div class="container">
        <h1>Explore Our Packages</h1>
        <p>Find your dream destination safely and seamlessly.</p>
    </div>
</div>

<div class="tm-section" style="background:var(--surface)">
    <div class="container">

        <!-- Search and Filter Bar -->
        <div class="tm-search-bar wow fadeInUp" data-wow-delay=".3s">
            <form method="get" action="package-list.php">
                <div class="row filter-row">
                    <div class="col-md-4" style="flex:1;">
                        <input type="text" name="search" class="form-control" placeholder="Search destination or package..." value="<?php echo htmlentities($search); ?>">
                    </div>
                    <div class="col-md-3" style="flex:1;">
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            <?php foreach($types as $t) { if(!empty($t->PackageType)){ ?>
                                <option value="<?php echo htmlentities($t->PackageType); ?>" <?php if($type == $t->PackageType) echo 'selected'; ?>><?php echo htmlentities($t->PackageType); ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="flex:1;">
                        <input type="number" name="price_max" class="form-control" placeholder="Max Price (Rs)" value="<?php echo isset($_GET['price_max']) && $_GET['price_max'] != '' ? htmlentities($price_max) : ''; ?>">
                    </div>
                    <div class="col-md-2" style="flex:initial">
                        <button type="submit" class="tm-search-btn"><i class="fa fa-search"></i> Search</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if($query->rowCount() > 0) { ?>
        <div class="packages-grid">
            <?php foreach($results as $result) { ?>
                <div class="pkg-card wow fadeInUp" data-wow-delay=".4s">
                    <div class="pkg-card-image">
                        <img src="admin/pacakgeimages/<?php echo htmlentities($result->PackageImage);?>" alt="<?php echo htmlentities($result->PackageName);?>">
                        <div class="pkg-card-image-overlay"></div>
                        <?php if(!empty($result->PackageType)) { ?>
                            <span class="pkg-type-badge"><?php echo htmlentities($result->PackageType);?></span>
                        <?php } ?>
                        <!-- wishlist logic if logged in -->
                        <?php if(isset($_SESSION['login']) && !empty($_SESSION['login'])) { ?>
                            <a href="add-wishlist.php?pid=<?php echo htmlentities($result->PackageId); ?>" class="pkg-wishlist-btn" title="Add to Wishlist">
                                <i class="fa fa-heart"></i>
                            </a>
                        <?php } ?>
                    </div>
                    <div class="pkg-card-body">
                        <div class="pkg-location"><i class="fa fa-map-marker"></i> <?php echo htmlentities($result->PackageLocation);?></div>
                        <h3 class="pkg-title"><?php echo htmlentities($result->PackageName);?></h3>
                        <p class="pkg-features"><?php echo htmlentities($result->PackageFetures);?></p>
                    </div>
                    <div class="pkg-card-footer">
                        <div class="pkg-price">
                            <span class="pkg-price-label">Starting from</span>
                            <span class="pkg-price-value"><small>Rs</small> <?php echo htmlentities($result->PackagePrice);?></span>
                        </div>
                        <a href="package-details.php?pkgid=<?php echo htmlentities($result->PackageId);?>" class="tm-btn-details">Details <i class="fa fa-arrow-right"></i></a>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php } else { ?>
            <div class="tm-empty wow fadeInUp" data-wow-delay=".3s">
                <i class="fa fa-search ei"></i>
                <h3>No Packages Found</h3>
                <p>Try adjusting your search criteria and filters.</p>
            </div>
        <?php } ?>

    </div>
</div>

<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>
<?php include('includes/signin.php');?>
<?php include('includes/write-us.php');?>
</body>
</html>