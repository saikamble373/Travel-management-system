<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Dynamic Data
$userQuery = $dbh->query("SELECT id FROM tblusers");
$happyTravelers = $userQuery ? $userQuery->rowCount() : 0;

$packQuery = $dbh->query("SELECT PackageId FROM tbltourpackages");
$destinations = $packQuery ? $packQuery->rowCount() : 0;

$ticketsQuery = $dbh->query("SELECT id FROM tblissues WHERE AdminRemark IS NOT NULL");
$resolvedTickets = $ticketsQuery ? $ticketsQuery->rowCount() : 0;
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TravelMate | Premium Travel Management</title>
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
</head>
<body>
<?php include('includes/header.php');?>

<!-- HERO SECTION -->
<div class="tm-hero wow fadeIn" data-wow-duration="1s">
    <div class="container">
        <div class="tm-hero-content">
            <div class="tm-hero-badge wow fadeInUp" data-wow-delay="0.2s">
                <i class="fa fa-plane"></i> the ultimate travel experience
            </div>
            <h1 class="wow fadeInUp" data-wow-delay="0.4s">Discover The World<br>With <em>TravelMate</em></h1>
            <p class="wow fadeInUp" data-wow-delay="0.6s">Your journey begins here. Explore hand-picked destinations, premium itineraries, and seamless booking tailored just for you.</p>
            <div class="tm-hero-actions wow fadeInUp" data-wow-delay="0.8s">
                <a href="package-list.php" class="tm-btn-hero-primary"><i class="fa fa-search"></i> Explore Packages</a>
                <a href="#features" class="tm-btn-hero-ghost"><i class="fa fa-info-circle"></i> Learn More</a>
            </div>
            
            <div class="tm-hero-stats wow fadeIn" data-wow-delay="1.2s">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $happyTravelers; ?>+</span>
                    <span class="stat-label">Happy Travelers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $destinations; ?>+</span>
                    <span class="stat-label">Destinations</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $resolvedTickets; ?>+</span>
                    <span class="stat-label">Resolved Issues</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FEATURES SECTION -->
<div id="features" class="tm-section" style="background:var(--surface)">
    <div class="container">
        <div class="tm-section-header wow fadeInUp">
            <span class="tm-section-label">Why Choose Us</span>
            <h2>Experience The Difference</h2>
            <p>We provide exclusive deals, secure bookings, and tailored experiences.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card wow fadeInUp" data-wow-delay="0.2s">
                <div class="feature-icon blue"><i class="fa fa-shield"></i></div>
                <h3>Secure Booking</h3>
                <p>Your data and payments are heavily encrypted for total peace of mind.</p>
            </div>
            <div class="feature-card wow fadeInUp" data-wow-delay="0.4s">
                <div class="feature-icon orange"><i class="fa fa-tags"></i></div>
                <h3>Best Price Guarantee</h3>
                <p>We partner directly with vendors to ensure you get the most affordable rates.</p>
            </div>
            <div class="feature-card wow fadeInUp" data-wow-delay="0.6s">
                <div class="feature-icon green"><i class="fa fa-headphones"></i></div>
                <h3>24/7 Support</h3>
                <p>Our dedicated travel experts are always on hand to assist you, anywhere.</p>
            </div>
        </div>
    </div>
</div>

<!-- FEATURED PACKAGES SECTION -->
<div class="tm-section" style="background:white">
    <div class="container">
        <div class="tm-section-header wow fadeInUp">
            <span class="tm-section-label">Top Destinations</span>
            <h2>Featured Packages</h2>
            <p>Explore our most highly rated tour packages built for lifetime memories.</p>
        </div>

        <div class="packages-grid">
            <?php 
            $sql = "SELECT * from tbltourpackages order by rand() limit 3";
            $query = $dbh->prepare($sql);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            if($query->rowCount() > 0)
            {
                foreach($results as $result)
                {	
            ?>
            <div class="pkg-card wow fadeInUp" data-wow-delay="0.3s">
                <div class="pkg-card-image">
                    <img src="admin/pacakgeimages/<?php echo htmlentities($result->PackageImage);?>" alt="<?php echo htmlentities($result->PackageName);?>">
                    <div class="pkg-card-image-overlay"></div>
                    <?php if(!empty($result->PackageType)) { ?>
                        <span class="pkg-type-badge"><?php echo htmlentities($result->PackageType);?></span>
                    <?php } ?>
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
            <?php 
                } 
            } 
            ?>
        </div>
        
        <div class="tm-view-all wow fadeInUp" data-wow-delay="0.5s">
            <a href="package-list.php" class="tm-btn-outline">View All Packages</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>			
<?php include('includes/signin.php');?>			
<?php include('includes/write-us.php');?>			
</body>
</html>