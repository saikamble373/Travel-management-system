<?php
// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Force globally load premium aesthetic styles onto every legacy page -->
<link href="css/theme.css" rel="stylesheet" type="text/css" />

<div class="tm-navbar">
    <div class="container">
        <!-- Logo -->
        <a href="index.php" class="tm-logo">
            <div class="tm-logo-icon">T</div>Travel<span>Mate</span>
        </a>

        <!-- Desktop Nav -->
        <ul class="tm-nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="page.php?type=aboutus">About</a></li>
            <li><a href="package-list.php">Tour Packages</a></li>
            <li><a href="page.php?type=contact">Contact Us</a></li>
            <?php if(!isset($_SESSION['login']) || empty($_SESSION['login'])) { ?>
                <li><a href="enquiry.php">Enquiry</a></li>
            <?php } ?>
        </ul>

        <!-- Auth & Actions -->
        <div class="tm-auth">
            <?php if(isset($_SESSION['login']) && !empty($_SESSION['login'])) { ?>
                <div class="tm-user-dropdown">
                    <div class="tm-user-avatar">
                        <?php echo strtoupper(substr($_SESSION['login'], 0, 1)); ?>
                    </div>
                    <div class="tm-dropdown-menu">
                        <div class="tm-dropdown-header">
                            <span class="dn">My Account</span>
                            <span class="dl"><?php echo htmlentities($_SESSION['login']); ?></span>
                        </div>
                        <div class="divider"></div>
                        <a href="profile.php"><div class="di" style="background:#f1f5f9;"><i class="fa fa-user"></i></div> Profile</a>
                        <a href="change-password.php"><div class="di" style="background:#f1f5f9;"><i class="fa fa-key"></i></div> Password</a>
                        <a href="tour-history.php"><div class="di" style="background:#f1f5f9;"><i class="fa fa-list"></i></div> Tour History</a>
                        <a href="issuetickets.php"><div class="di" style="background:#f1f5f9;"><i class="fa fa-ticket"></i></div> Tickets</a>
                        <a href="wishlist.php"><div class="di" style="background:#f1f5f9;"><i class="fa fa-heart"></i></div> Wishlist</a>
                        <div class="divider"></div>
                        <a href="logout.php" class="logout-link"><div class="di" style="background:#fef2f2;"><i class="fa fa-sign-out"></i></div> Logout</a>
                    </div>
                </div>
            <?php } else { ?>
                <a href="admin/index.php" class="tm-btn-ghost" style="border:none; padding:8px" title="Admin Login"><i class="fa fa-lock"></i> Admin</a>
                <a href="#" class="tm-btn-ghost" data-toggle="modal" data-target="#myModal4">Sign In</a>
                <a href="#" class="tm-btn-accent" data-toggle="modal" data-target="#myModal">Sign Up</a>
            <?php } ?>
            <button class="tm-hamburger"><i class="fa fa-bars"></i></button>
        </div>
    </div>
</div>

<!-- Mobile Menu -->
<div class="tm-mobile-menu">
    <a href="index.php"><i class="fa fa-home"></i> Home</a>
    <a href="page.php?type=aboutus"><i class="fa fa-info-circle"></i> About</a>
    <a href="package-list.php"><i class="fa fa-suitcase"></i> Tour Packages</a>
    <a href="page.php?type=contact"><i class="fa fa-phone"></i> Contact Us</a>
    <?php if(!isset($_SESSION['login']) || empty($_SESSION['login'])) { ?>
        <a href="enquiry.php"><i class="fa fa-envelope"></i> Enquiry</a>
        <div class="tm-mobile-auth">
            <a href="#" class="tm-btn-ghost" data-toggle="modal" data-target="#myModal4" style="background:rgba(255,255,255,0.1)">Sign In</a>
            <a href="#" class="tm-btn-accent" data-toggle="modal" data-target="#myModal">Sign Up</a>
        </div>
    <?php } else { ?>
        <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
        <a href="change-password.php"><i class="fa fa-key"></i> Password</a>
        <a href="tour-history.php"><i class="fa fa-list"></i> Tour History</a>
        <a href="issuetickets.php"><i class="fa fa-ticket"></i> Tickets</a>
        <a href="wishlist.php"><i class="fa fa-heart"></i> Wishlist</a>
        <a href="logout.php" style="color:#ef4444!important"><i class="fa fa-sign-out"></i> Logout</a>
    <?php } ?>
</div>

<script>
$('.tm-hamburger').click(function(){
    $('.tm-mobile-menu').slideToggle();
});
$(window).scroll(function(){
    if($(this).scrollTop() > 50) { $('.tm-navbar').addClass('scrolled'); }
    else { $('.tm-navbar').removeClass('scrolled'); }
});
</script>