<?php
session_start();
error_reporting(0);
include('includes/config.php');

$msg = "";
$error = "";

if(isset($_POST['submit2'])) {
    $pid       = intval($_GET['pkgid']);
    $useremail = $_SESSION['login'];
    $fromdate  = $_POST['fromdate'];
    $todate    = $_POST['todate'];
    $comment   = $_POST['comment'];
    $status    = 0;

    $sql = "INSERT INTO tblbooking(PackageId,UserEmail,FromDate,ToDate,Comment,status)
            VALUES(:pid,:useremail,:fromdate,:todate,:comment,:status)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pid',       $pid,       PDO::PARAM_STR);
    $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
    $query->bindParam(':fromdate',  $fromdate,  PDO::PARAM_STR);
    $query->bindParam(':todate',    $todate,    PDO::PARAM_STR);
    $query->bindParam(':comment',   $comment,   PDO::PARAM_STR);
    $query->bindParam(':status',    $status,    PDO::PARAM_STR);
    $query->execute();

    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId) { $msg   = "Booked Successfully"; }
    else              { $error = "Something went wrong. Please try again"; }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TravelMate | Package Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="css/style.css" rel="stylesheet" type="text/css"/>
<link href="css/font-awesome.css" rel="stylesheet">
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<link rel="stylesheet" href="css/jquery-ui.css"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<script src="js/jquery-1.12.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/wow.min.js"></script>
<script src="js/jquery-ui.js"></script>
<script> new WOW().init(); </script>

<style>
:root {
    --sky:      #0ea5e9;
    --ocean:    #0369a1;
    --sand:     #f59e0b;
    --coral:    #f43f5e;
    --dark:     #0f172a;
    --mid:      #334155;
    --muted:    #94a3b8;
    --light:    #f1f5f9;
    --white:    #ffffff;
    --green:    #10b981;
    --radius:   14px;
    --shadow:   0 4px 24px rgba(15,23,42,0.10);
    --shadow-lg:0 8px 40px rgba(15,23,42,0.15);
}

body { font-family: 'DM Sans', sans-serif; background: var(--light); }

/* Alerts */
.errorWrap {
    padding: 14px 20px; margin: 0 0 24px;
    background: #ffe4e6; border-left: 4px solid var(--coral);
    border-radius: 10px; color: #9f1239; font-size: 14px;
}
.succWrap {
    padding: 14px 20px; margin: 0 0 24px;
    background: #d1fae5; border-left: 4px solid var(--green);
    border-radius: 10px; color: #065f46; font-size: 14px;
}

/* Page wrapper */
.pkg-detail-wrap { padding: 48px 0 60px; }

/* Image Card */
.pkg-img-card {
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    position: relative;
}
.pkg-img-card img {
    width: 100%; height: 360px;
    object-fit: cover; display: block;
}
.pkg-id-badge {
    position: absolute; top: 16px; left: 16px;
    background: rgba(15,23,42,0.70);
    color: white; font-size: 12px; font-weight: 600;
    padding: 5px 14px; border-radius: 999px;
    backdrop-filter: blur(4px); letter-spacing: 0.5px;
}

/* Info column */
.pkg-info-col { padding-left: 32px; }

.pkg-title {
    font-family: 'Playfair Display', serif;
    font-size: 28px; font-weight: 700;
    color: var(--dark); margin: 0 0 8px; line-height: 1.25;
}

.pkg-location {
    display: flex; align-items: center; gap: 6px;
    font-size: 14px; color: var(--muted); margin-bottom: 18px;
}
.pkg-location i { color: var(--sky); }

/* Stat Pills */
.stat-pills { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
.stat-pill {
    display: flex; align-items: center; gap: 8px;
    background: var(--white); border: 1.5px solid #e2e8f0;
    border-radius: 999px; padding: 8px 16px;
    font-size: 13px; font-weight: 500; color: var(--mid);
    box-shadow: 0 2px 8px rgba(15,23,42,0.06);
}
.stat-pill i { color: var(--sky); font-size: 14px; }
.pill-label {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.6px; color: var(--muted); margin-right: 2px;
}

/* Star rating display */
.star-display { display: flex; align-items: center; gap: 6px; margin-bottom: 18px; }
.star-display i { color: var(--sand); font-size: 16px; }
.star-display i.empty { color: #e2e8f0; }
.star-num { font-size: 14px; font-weight: 700; color: var(--dark); }

/* Features row */
.pkg-features-row {
    background: #f0f9ff; border-radius: 10px;
    padding: 12px 16px; margin-bottom: 18px;
    font-size: 14px; color: var(--mid);
    border-left: 3px solid var(--sky);
}
.pkg-features-row strong { color: var(--dark); }

/* Price box */
.price-box {
    background: linear-gradient(135deg, var(--ocean), var(--sky));
    border-radius: var(--radius); padding: 20px 24px; margin: 18px 0;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: 0 4px 20px rgba(3,105,161,0.30);
}
.price-label { color: rgba(255,255,255,0.80); font-size: 13px; font-weight: 500; }
.price-note  { font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 3px; }
.price-val   { font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 700; color: white; }

/* Details section */
.pkg-details-section {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow); padding: 28px 32px; margin-top: 32px;
}
.section-title {
    font-family: 'Playfair Display', serif;
    font-size: 20px; font-weight: 700; color: var(--dark);
    margin: 0 0 14px;
    display: flex; align-items: center; gap: 10px;
}
.section-title::after { content:''; flex:1; height:1px; background:#e2e8f0; }
.pkg-details-section p { color: var(--mid); font-size: 15px; line-height: 1.80; margin: 0; }

/* Booking card */
.booking-card {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow); padding: 28px 32px; margin-top: 24px;
}
.booking-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.bk-field { display: flex; flex-direction: column; gap: 6px; }
.bk-field label {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.7px; color: var(--mid);
}
.bk-input {
    padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-family: 'DM Sans', sans-serif; font-size: 14px; color: var(--dark);
    background: #f8fafc; outline: none; transition: all 0.2s;
    box-sizing: border-box; width: 100%;
}
.bk-input:focus {
    border-color: var(--sky); background: white;
    box-shadow: 0 0 0 3px rgba(14,165,233,0.12);
}

/* Book button */
.btn-book {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    padding: 14px 36px; width: 100%; margin-top: 8px;
    background: linear-gradient(135deg, var(--ocean), var(--sky));
    color: white; border: none; border-radius: 999px;
    font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 600;
    cursor: pointer; box-shadow: 0 4px 20px rgba(14,165,233,0.35);
    transition: transform 0.2s, box-shadow 0.2s;
}
.btn-book:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(14,165,233,0.40); }

/* Login prompt */
.login-prompt {
    text-align: center; padding: 16px; background: #f0f9ff;
    border-radius: 10px; font-size: 14px; color: var(--mid); margin-top: 8px;
}
.login-prompt a { color: var(--sky); font-weight: 600; text-decoration: none; }

@media(max-width: 768px) {
    .pkg-info-col { padding-left: 0; margin-top: 24px; }
    .booking-grid { grid-template-columns: 1fr; }
    .pkg-img-card img { height: 240px; }
    .price-val { font-size: 22px; }
}
</style>
</head>
<body>

<?php include('includes/header.php'); ?>

<div class="banner-3">
    <div class="container">
        <h1 class="wow zoomIn animated" data-wow-delay=".5s">TravelMate — Package Details</h1>
    </div>
</div>

<div class="pkg-detail-wrap">
    <div class="container">

        <?php if($error): ?>
            <div class="errorWrap"><strong>ERROR:</strong> <?php echo htmlentities($error); ?></div>
        <?php elseif($msg): ?>
            <div class="succWrap"><strong>SUCCESS:</strong> <?php echo htmlentities($msg); ?></div>
        <?php endif; ?>

        <?php
        $pid   = intval($_GET['pkgid']);
        $sql   = "SELECT * FROM tbltourpackages WHERE PackageId=:pid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':pid', $pid, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0):
        foreach($results as $result):
        ?>

        <form name="book" method="post">

            <!-- Top: Image + Info -->
            <div class="row">

                <!-- Image -->
                <div class="col-md-5 wow fadeInLeft" data-wow-delay=".3s">
                    <div class="pkg-img-card">
                        <img src="admin/pacakgeimages/<?php echo htmlentities($result->PackageImage); ?>"
                             alt="<?php echo htmlentities($result->PackageName); ?>">
                        <div class="pkg-id-badge">#PKG-<?php echo htmlentities($result->PackageId); ?></div>
                    </div>
                </div>

                <!-- Info -->
                <div class="col-md-7 wow fadeInRight" data-wow-delay=".3s">
                    <div class="pkg-info-col">

                        <h1 class="pkg-title"><?php echo htmlentities($result->PackageName); ?></h1>

                        <div class="pkg-location">
                            <i class="fa fa-map-marker"></i>
                            <?php echo htmlentities($result->PackageLocation); ?>
                        </div>

                        <!-- ✅ Stat Pills: Duration, Group Size, Rating, Type -->
                        <div class="stat-pills">

                            <?php if(!empty($result->PackageType)): ?>
                            <div class="stat-pill">
                                <i class="fa fa-tag"></i>
                                <span class="pill-label">Type</span>
                                <?php echo htmlentities($result->PackageType); ?>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($result->duration)): ?>
                            <div class="stat-pill">
                                <i class="fa fa-clock-o"></i>
                                <span class="pill-label">Duration</span>
                                <?php echo htmlentities($result->duration); ?>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($result->groupsize)): ?>
                            <div class="stat-pill">
                                <i class="fa fa-users"></i>
                                <span class="pill-label">Group</span>
                                <?php echo htmlentities($result->groupsize); ?>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($result->rating)): ?>
                            <div class="stat-pill">
                                <i class="fa fa-star" style="color:var(--sand)"></i>
                                <span class="pill-label">Rating</span>
                                <?php echo htmlentities($result->rating); ?>/5
                            </div>
                            <?php endif; ?>

                        </div>

                        <!-- ✅ Star Rating Visual -->
                        <?php if(!empty($result->rating)): ?>
                        <div class="star-display">
                            <?php
                            $r = floatval($result->rating);
                            for($i = 1; $i <= 5; $i++):
                                if($r >= $i)           echo '<i class="fa fa-star"></i>';
                                elseif($r >= $i - 0.5) echo '<i class="fa fa-star-half-o"></i>';
                                else                   echo '<i class="fa fa-star empty"></i>';
                            endfor;
                            ?>
                            <span class="star-num"><?php echo number_format($r, 1); ?> / 5</span>
                        </div>
                        <?php endif; ?>

                        <!-- Features -->
                        <?php if(!empty($result->PackageFetures)): ?>
                        <div class="pkg-features-row">
                            <strong><i class="fa fa-check-circle" style="color:var(--green)"></i> Features &nbsp;</strong>
                            <?php echo htmlentities($result->PackageFetures); ?>
                        </div>
                        <?php endif; ?>

                        <!-- Price Box -->
                        <div class="price-box">
                            <div>
                                <div class="price-label">Package Price</div>
                                <div class="price-note">per person, all inclusive</div>
                            </div>
                            <div class="price-val">
                                ₹<?php echo htmlentities($result->PackagePrice); ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div><!-- /row -->

            <!-- Package Details -->
            <div class="pkg-details-section wow fadeInUp" data-wow-delay=".2s">
                <div class="section-title">
                    <i class="fa fa-info-circle" style="color:var(--sky)"></i> Package Details
                </div>
                <p><?php echo nl2br(htmlentities($result->PackageDetails)); ?></p>
            </div>

            <!-- Booking Card -->
            <div class="booking-card wow fadeInUp" data-wow-delay=".3s">
                <div class="section-title">
                    <i class="fa fa-calendar-check-o" style="color:var(--sky)"></i> Book This Package
                </div>

                <div class="booking-grid">
                    <div class="bk-field">
                        <label>From Date</label>
                        <input class="bk-input" id="datepicker2" type="date" name="fromdate" required>
                    </div>
                    <div class="bk-field">
                        <label>To Date</label>
                        <input class="bk-input" id="datepicker3" type="date" name="todate" required>
                    </div>
                </div>

                <div class="bk-field" style="margin-bottom: 16px;">
                    <label>Comment / Special Request</label>
                    <input class="bk-input" type="text" name="comment"
                        placeholder="Any special request or note..." required>
                </div>

                <?php if($_SESSION['login']): ?>
                    <button type="submit" name="submit2" class="btn-book">
                        <i class="fa fa-paper-plane"></i> Confirm Booking
                    </button>
                <?php else: ?>
                    <div class="login-prompt">
                        Please <a href="#" data-toggle="modal" data-target="#myModal4">sign in</a>
                        to book this package.
                    </div>
                <?php endif; ?>

            </div>

        </form>

        <?php endforeach; endif; ?>

    </div>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/signup.php'); ?>
<?php include('includes/signin.php'); ?>
<?php include('includes/write-us.php'); ?>

<script>
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("datepicker2").setAttribute("min", today);
    document.getElementById("datepicker3").setAttribute("min", today);

    // To date always after From date
    document.getElementById("datepicker2").addEventListener("change", function() {
        document.getElementById("datepicker3").setAttribute("min", this.value);
    });
</script>

</body>
</html>