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

if(isset($_POST['submitReview'])) {
    $pid       = intval($_GET['pkgid']);
    $useremail = $_SESSION['login'];
    $rating    = $_POST['rating'];
    $r_comment = $_POST['review_comment'];

    $sql = "INSERT INTO tblreviews(PackageId,UserEmail,StarRating,ReviewText)
            VALUES(:pid,:useremail,:rating,:comment)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pid',       $pid,       PDO::PARAM_STR);
    $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
    $query->bindParam(':rating',    $rating,    PDO::PARAM_STR);
    $query->bindParam(':comment',   $r_comment, PDO::PARAM_STR);
    $query->execute();

    $lastInsertIdReview = $dbh->lastInsertId();
    if($lastInsertIdReview) { $msg   = "Review submitted successfully!"; }
    else              { $error = "Failed to submit review."; }
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

.pkg-detail-wrap { padding: 48px 0 60px; }

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

.star-display { display: flex; align-items: center; gap: 6px; margin-bottom: 18px; }
.star-display i { color: var(--sand); font-size: 16px; }
.star-display i.empty { color: #e2e8f0; }
.star-num { font-size: 14px; font-weight: 700; color: var(--dark); }

.pkg-features-row {
    background: #f0f9ff; border-radius: 10px;
    padding: 12px 16px; margin-bottom: 18px;
    font-size: 14px; color: var(--mid);
    border-left: 3px solid var(--sky);
}
.pkg-features-row strong { color: var(--dark); }

.price-box {
    background: linear-gradient(135deg, var(--ocean), var(--sky));
    border-radius: var(--radius); padding: 20px 24px; margin: 18px 0;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: 0 4px 20px rgba(3,105,161,0.30);
}
.price-label { color: rgba(255,255,255,0.80); font-size: 13px; font-weight: 500; }
.price-note  { font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 3px; }
.price-val   { font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 700; color: white; }

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

.login-prompt {
    text-align: center; padding: 16px; background: #f0f9ff;
    border-radius: 10px; font-size: 14px; color: var(--mid); margin-top: 8px;
}
.login-prompt a { color: var(--sky); font-weight: 600; text-decoration: none; }

.reviews-section {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow); padding: 28px 32px; margin-top: 24px;
}
.review-item {
    border-bottom: 1px solid #e2e8f0; padding: 16px 0;
}
.review-item:last-child { border-bottom: none; }
.review-header { display: flex; justify-content: space-between; margin-bottom: 6px; }
.review-user { font-weight: 700; color: var(--dark); font-size: 14px; }
.review-date { font-size: 12px; color: var(--muted); }
.review-stars { color: var(--sand); font-size: 13px; margin-bottom: 8px; }
.review-comment { font-size: 14px; color: var(--mid); line-height: 1.6; }

.review-form { margin-top: 24px; padding-top: 24px; border-top: 1.5px dashed #e2e8f0; }
.star-rating-input { font-size: 24px; color: #e2e8f0; cursor: pointer; direction: rtl; display: inline-flex; }
.star-rating-input input { display: none; }
.star-rating-input label { display: inline-block; cursor: pointer; transition: color 0.2s; padding: 0 2px; }
.star-rating-input label:hover,
.star-rating-input label:hover ~ label,
.star-rating-input input:checked ~ label { color: var(--sand); }

/* ── AI Chatbot Styles ── */
#ai-chat-fab {
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg, var(--ocean), var(--sky));
    color: white; border: none; font-size: 22px;
    box-shadow: 0 4px 20px rgba(14,165,233,0.45);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: transform 0.2s;
}
#ai-chat-fab:hover { transform: scale(1.1); }

#ai-chat-box {
    position: fixed; bottom: 96px; right: 28px; z-index: 9998;
    width: 340px; background: white; border-radius: 18px;
    box-shadow: 0 8px 40px rgba(15,23,42,0.18);
    display: none; flex-direction: column; overflow: hidden;
    font-family: 'DM Sans', sans-serif;
}
#ai-chat-box.open { display: flex; }

#ai-chat-header {
    background: linear-gradient(135deg, var(--ocean), var(--sky));
    color: white; padding: 14px 18px;
    display: flex; align-items: center; justify-content: space-between;
    font-weight: 600; font-size: 15px;
}
#ai-chat-header span { font-size: 13px; opacity: 0.85; }
#ai-chat-close { cursor: pointer; font-size: 18px; background: none; border: none; color: white; }

#ai-chat-messages {
    flex: 1; overflow-y: auto; padding: 14px 16px;
    display: flex; flex-direction: column; gap: 10px;
    max-height: 300px; min-height: 200px;
    background: #f8fafc;
}

.ai-msg, .user-msg {
    max-width: 82%; padding: 10px 14px; border-radius: 14px;
    font-size: 13.5px; line-height: 1.5;
}
.ai-msg {
    background: white; color: var(--dark);
    border: 1.5px solid #e2e8f0; align-self: flex-start;
    border-bottom-left-radius: 4px;
}
.user-msg {
    background: linear-gradient(135deg, var(--ocean), var(--sky));
    color: white; align-self: flex-end;
    border-bottom-right-radius: 4px;
}

#ai-chat-input-row {
    display: flex; gap: 8px; padding: 12px 14px;
    border-top: 1px solid #e2e8f0; background: white;
}
#ai-chat-input {
    flex: 1; border: 1.5px solid #e2e8f0; border-radius: 999px;
    padding: 9px 14px; font-size: 13px; font-family: 'DM Sans', sans-serif;
    outline: none; background: #f8fafc;
}
#ai-chat-input:focus { border-color: var(--sky); background: white; }
#ai-chat-send {
    background: var(--sky); color: white; border: none;
    border-radius: 999px; padding: 9px 16px; font-size: 13px;
    cursor: pointer; font-family: 'DM Sans', sans-serif; font-weight: 600;
}
#ai-chat-send:hover { background: var(--ocean); }

@media(max-width: 768px) {
    .pkg-info-col { padding-left: 0; margin-top: 24px; }
    .booking-grid { grid-template-columns: 1fr; }
    .pkg-img-card img { height: 240px; }
    .price-val { font-size: 22px; }
    #ai-chat-box { width: calc(100vw - 40px); right: 20px; }
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

        $ratingSql = "SELECT AVG(StarRating) as avg_rating FROM tblreviews WHERE PackageId=:pid";
        $ratingQuery = $dbh->prepare($ratingSql);
        $ratingQuery->bindParam(':pid', $pid, PDO::PARAM_STR);
        $ratingQuery->execute();
        $avgRatingData = $ratingQuery->fetch(PDO::FETCH_OBJ);
        $dynamicAvgRating = round($avgRatingData->avg_rating, 1);
        if(!$dynamicAvgRating && !empty($results[0]->rating)) {
            $dynamicAvgRating = floatval($results[0]->rating);
        }

        if($query->rowCount() > 0):
        foreach($results as $result):
        ?>

        <form name="book" method="post">

            <div class="row">

                <div class="col-md-5 wow fadeInLeft" data-wow-delay=".3s">
                    <div class="pkg-img-card">
                        <img src="admin/pacakgeimages/<?php echo htmlentities($result->PackageImage); ?>"
                             alt="<?php echo htmlentities($result->PackageName); ?>">
                        <div class="pkg-id-badge">#PKG-<?php echo htmlentities($result->PackageId); ?></div>
                    </div>
                </div>

                <div class="col-md-7 wow fadeInRight" data-wow-delay=".3s">
                    <div class="pkg-info-col">

                        <h1 class="pkg-title"><?php echo htmlentities($result->PackageName); ?></h1>

                        <div class="pkg-location">
                            <i class="fa fa-map-marker"></i>
                            <?php echo htmlentities($result->PackageLocation); ?>
                        </div>

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

                            <?php if($dynamicAvgRating > 0): ?>
                            <div class="stat-pill">
                                <i class="fa fa-star" style="color:var(--sand)"></i>
                                <span class="pill-label">Rating</span>
                                <?php echo number_format($dynamicAvgRating, 1); ?>/5
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if($dynamicAvgRating > 0): ?>
                        <div class="star-display">
                            <?php
                            $r = $dynamicAvgRating;
                            for($i = 1; $i <= 5; $i++):
                                if($r >= $i)           echo '<i class="fa fa-star"></i>';
                                elseif($r >= $i - 0.5) echo '<i class="fa fa-star-half-o"></i>';
                                else                   echo '<i class="fa fa-star empty"></i>';
                            endfor;
                            ?>
                            <span class="star-num"><?php echo number_format($r, 1); ?> / 5</span>
                        </div>
                        <?php endif; ?>

                        <?php if(!empty($result->PackageFetures)): ?>
                        <div class="pkg-features-row">
                            <strong><i class="fa fa-check-circle" style="color:var(--green)"></i> Features &nbsp;</strong>
                            <?php echo htmlentities($result->PackageFetures); ?>
                        </div>
                        <?php endif; ?>

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
            </div>

            <div class="pkg-details-section wow fadeInUp" data-wow-delay=".2s">
                <div class="section-title">
                    <i class="fa fa-info-circle" style="color:var(--sky)"></i> Package Details
                </div>
                <p><?php echo nl2br(htmlentities($result->PackageDetails)); ?></p>
            </div>

            <div class="reviews-section wow fadeInUp" data-wow-delay=".3s">
                <div class="section-title">
                    <i class="fa fa-comments" style="color:var(--sky)"></i> Guest Reviews
                </div>

                <?php
                $revSql = "SELECT * FROM tblreviews WHERE PackageId=:pid ORDER BY PostingDate DESC";
                $revQuery = $dbh->prepare($revSql);
                $revQuery->bindParam(':pid', $pid, PDO::PARAM_STR);
                $revQuery->execute();
                $reviews = $revQuery->fetchAll(PDO::FETCH_OBJ);

                if($revQuery->rowCount() > 0) {
                    foreach($reviews as $rev) {
                        ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="review-user"><?php echo htmlentities($rev->UserEmail); ?></div>
                                <div class="review-date"><?php echo htmlentities(date("M j, Y", strtotime($rev->PostingDate))); ?></div>
                            </div>
                            <div class="review-stars">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="fa fa-star<?php echo ($rev->StarRating >= $i) ? '' : ' empty'; ?>" <?php echo ($rev->StarRating < $i) ? 'style="color:#e2e8f0;"' : ''; ?>></i>
                                <?php endfor; ?>
                            </div>
                            <div class="review-comment"><?php echo nl2br(htmlentities($rev->ReviewText)); ?></div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No reviews yet. Be the first to review this package!</p>";
                }
                ?>

                <?php if($_SESSION['login']): ?>
                <div class="review-form">
                    <h4>Write a Review</h4>
                    <div class="bk-field" style="margin-top:10px;">
                        <label>Your Rating</label>
                        <div class="star-rating-input">
                            <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 stars"><i class="fa fa-star"></i></label>
                            <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars"><i class="fa fa-star"></i></label>
                            <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars"><i class="fa fa-star"></i></label>
                            <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars"><i class="fa fa-star"></i></label>
                            <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star"><i class="fa fa-star"></i></label>
                        </div>
                    </div>
                    <div class="bk-field" style="margin-top:10px;">
                        <label>Your Review</label>
                        <textarea name="review_comment" class="bk-input" rows="3" required placeholder="Tell us about your experience..."></textarea>
                    </div>
                    <button type="submit" name="submitReview" class="btn-book" style="width: auto; padding: 10px 24px; margin-top:10px;">Submit Review</button>
                </div>
                <?php endif; ?>
            </div>

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

        <!-- ── AI Smart Recommendations ── -->
        <div class="pkg-details-section wow fadeInUp" data-wow-delay=".4s"
             style="margin-top:24px;" id="ai-recommendations-section">
            <div class="section-title">
                <i class="fa fa-magic" style="color:var(--sky)"></i> You Might Also Like
            </div>
            <div id="ai-rec-loading" style="color:var(--muted); font-size:14px; padding:10px 0;">
                Finding similar packages for you…
            </div>
            <div id="ai-rec-results" style="display:flex; flex-wrap:wrap; gap:16px; margin-top:10px;"></div>
        </div>

        <script>
        (function(){
            var pkgId   = <?php echo intval($result->PackageId); ?>;
            var pkgType = <?php echo json_encode($result->PackageType); ?>;
            var pkgLoc  = <?php echo json_encode($result->PackageLocation); ?>;

            fetch('http://localhost:5000/recommend', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({package_id: pkgId, package_type: pkgType, location: pkgLoc})
            })
            .then(function(r){ return r.json(); })
            .then(function(data){
                document.getElementById('ai-rec-loading').style.display = 'none';
                var recs = data.recommendations || [];
                if(recs.length === 0){
                    document.getElementById('ai-recommendations-section').style.display = 'none';
                    return;
                }
                var html = '';
                recs.forEach(function(p){
                    html += '<a href="package-details.php?pkgid=' + p.PackageId + '" '
                          + 'style="text-decoration:none; flex:1; min-width:200px; max-width:240px;">'
                          + '<div style="background:#fff; border-radius:12px; overflow:hidden; '
                          + 'box-shadow:0 2px 14px rgba(15,23,42,.09); transition:transform .2s;" '
                          + 'onmouseover="this.style.transform=\'translateY(-4px)\'" '
                          + 'onmouseout="this.style.transform=\'none\'">'
                          + '<img src="admin/pacakgeimages/' + p.PackageImage + '" '
                          + 'style="width:100%; height:130px; object-fit:cover;">'
                          + '<div style="padding:12px;">'
                          + '<div style="font-weight:700; font-size:14px; color:#0f172a; margin-bottom:4px;">'
                          + p.PackageName + '</div>'
                          + '<div style="font-size:12px; color:#94a3b8; margin-bottom:6px;">'
                          + '<i class="fa fa-map-marker"></i> ' + p.PackageLocation + '</div>'
                          + '<div style="font-size:13px; font-weight:700; color:#0ea5e9;">&#8377;' + p.PackagePrice + '</div>'
                          + '</div></div></a>';
                });
                document.getElementById('ai-rec-results').innerHTML = html;
            })
            .catch(function(){
                document.getElementById('ai-rec-loading').style.display = 'none';
            });
        })();
        </script>
        <!-- ── end recommendations ── -->

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

    document.getElementById("datepicker2").addEventListener("change", function() {
        document.getElementById("datepicker3").setAttribute("min", this.value);
    });
</script>

<!-- ── AI Chatbot Widget ── -->
<button id="ai-chat-fab" title="Chat with AI Assistant">
    <i class="fa fa-comments"></i>
</button>

<div id="ai-chat-box">
    <div id="ai-chat-header">
        <div>
            <div><i class="fa fa-robot"></i> TravelMate AI</div>
            <span>Ask anything about our packages</span>
        </div>
        <button id="ai-chat-close" title="Close">&#10005;</button>
    </div>
    <div id="ai-chat-messages">
        <div class="ai-msg">👋 Hi! I'm your TravelMate AI assistant. Ask me anything about our tour packages, pricing, or destinations!</div>
    </div>
    <div id="ai-chat-input-row">
        <input id="ai-chat-input" type="text" placeholder="Type your question…" autocomplete="off">
        <button id="ai-chat-send">Send</button>
    </div>
</div>

<script>
(function(){
    var fab   = document.getElementById('ai-chat-fab');
    var box   = document.getElementById('ai-chat-box');
    var close = document.getElementById('ai-chat-close');
    var input = document.getElementById('ai-chat-input');
    var send  = document.getElementById('ai-chat-send');
    var msgs  = document.getElementById('ai-chat-messages');

    fab.addEventListener('click', function(){
        box.classList.toggle('open');
        if(box.classList.contains('open')) input.focus();
    });
    close.addEventListener('click', function(){ box.classList.remove('open'); });

    function appendMsg(text, cls){
        var d = document.createElement('div');
        d.className = cls;
        d.textContent = text;
        msgs.appendChild(d);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function sendMessage(){
        var text = input.value.trim();
        if(!text) return;
        appendMsg(text, 'user-msg');
        input.value = '';
        send.disabled = true;

        var thinking = document.createElement('div');
        thinking.className = 'ai-msg';
        thinking.textContent = '…';
        msgs.appendChild(thinking);
        msgs.scrollTop = msgs.scrollHeight;

        fetch('http://localhost:5000/chat', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({message: text})
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            thinking.textContent = data.reply || 'Sorry, I could not understand that.';
        })
        .catch(function(){
            thinking.textContent = 'AI service is offline. Please make sure ai_service.py is running.';
        })
        .finally(function(){
            send.disabled = false;
            msgs.scrollTop = msgs.scrollHeight;
        });
    }

    send.addEventListener('click', sendMessage);
    input.addEventListener('keydown', function(e){ if(e.key === 'Enter') sendMessage(); });
})();
</script>
<!-- ── end chatbot ── -->

</body>
</html>