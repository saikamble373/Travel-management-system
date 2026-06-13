<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

$msg   = "";
$error = "";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed. Please go back and try again.");
    }

    $pname     = trim($_POST['packagename']);
    $ptype     = trim($_POST['packagetype']);
    $plocation = trim($_POST['packagelocation']);
    $pprice    = trim($_POST['packageprice']);
    $pfeatures = trim($_POST['packagefeatures']);
    $pdetails  = trim($_POST['packagedetails']);
    $duration  = trim($_POST['duration']);
    $groupsize = trim($_POST['groupsize']);

    $rating = floatval($_POST['rating']);
    if ($rating < 0 || $rating > 5) {
        $error = "Rating must be a number between 0 and 5.";
    }

    if (empty($error)) {
        if (!isset($_FILES['packageimage']) || $_FILES['packageimage']['error'] !== UPLOAD_ERR_OK) {
            $error = "Please upload a package image.";
        } else {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['packageimage']['tmp_name']);

            if (!in_array($fileType, $allowed)) {
                $error = "Invalid image type. Only JPG, PNG, GIF, WEBP allowed.";
            } elseif ($_FILES['packageimage']['size'] > 2 * 1024 * 1024) {
                $error = "Image size must be under 2MB.";
            } else {
                $ext = pathinfo($_FILES['packageimage']['name'], PATHINFO_EXTENSION);
                $pimage = 'pkg_' . time() . '.' . $ext;
                if (!move_uploaded_file($_FILES['packageimage']['tmp_name'], 'pacakgeimages/' . $pimage)) {
                    $error = "Failed to upload image. Check folder permissions.";
                }
            }
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO tbltourpackages
                    (PackageName, PackageType, PackageLocation, PackagePrice, PackageFetures, PackageDetails, PackageImage, duration, groupsize, rating)
                VALUES
                    (:pname, :ptype, :plocation, :pprice, :pfeatures, :pdetails, :pimage, :duration, :groupsize, :rating)";

        $query = $dbh->prepare($sql);
        $query->bindParam(':pname',     $pname,     PDO::PARAM_STR);
        $query->bindParam(':ptype',     $ptype,     PDO::PARAM_STR);
        $query->bindParam(':plocation', $plocation, PDO::PARAM_STR);
        $query->bindParam(':pprice',    $pprice,    PDO::PARAM_STR);
        $query->bindParam(':pfeatures', $pfeatures, PDO::PARAM_STR);
        $query->bindParam(':pdetails',  $pdetails,  PDO::PARAM_STR);
        $query->bindParam(':pimage',    $pimage,    PDO::PARAM_STR);
        $query->bindParam(':duration',  $duration,  PDO::PARAM_STR);
        $query->bindParam(':groupsize', $groupsize, PDO::PARAM_STR);
        $query->bindParam(':rating',    $rating,    PDO::PARAM_STR);
        $query->execute();

        $lastInsertId = $dbh->lastInsertId();
        if ($lastInsertId) {
            $msg = "Package Created Successfully";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TravelMate | Admin Package Creation</title>
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href="css/admin-theme.css" rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="css/morris.css" type="text/css"/>
<link href="css/font-awesome.css" rel="stylesheet">
<script src="js/jquery-2.1.4.min.js"></script>
<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
<style>
    .errorWrap {
        padding: 10px;
        margin: 0 0 20px 0;
        background: #fff;
        border-left: 4px solid #dd3d36;
        -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    }
    .succWrap {
        padding: 10px;
        margin: 0 0 20px 0;
        background: #fff;
        border-left: 4px solid #5cb85c;
        -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    }
    #ai-gen-btn { transition: opacity 0.2s; }
    #ai-gen-btn:disabled { opacity: .6; cursor: not-allowed; }
</style>
</head>
<body>
<div class="page-container">
<div class="left-content">
    <div class="mother-grid-inner">
        <?php include('includes/header.php'); ?>
        <div class="clearfix"></div>
    </div>

    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php">Home</a><i class="fa fa-angle-right"></i>Create Package
        </li>
    </ol>

    <div class="grid-form">
        <div class="grid-form1">
            <h3>Create Package</h3>

            <?php if ($error): ?>
                <div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?></div>
            <?php elseif ($msg): ?>
                <div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?></div>
            <?php endif; ?>

            <div class="tab-content">
                <div class="tab-pane active" id="horizontal-form">
                    <form class="form-horizontal" name="package" method="post" enctype="multipart/form-data">

                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <!-- Package Name -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Package Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="packagename"
                                    placeholder="Create Package" required>
                            </div>
                        </div>

                        <!-- Package Type -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Package Type</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="packagetype"
                                    placeholder="e.g. Family Package / Couple Package" required>
                            </div>
                        </div>

                        <!-- Package Location -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Package Location</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="packagelocation"
                                    placeholder="Package Location" required>
                            </div>
                        </div>

                        <!-- Package Price -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Package Price in USD</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="packageprice"
                                    placeholder="Package Price in USD" required>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Duration</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="duration"
                                    placeholder="e.g. 7 Days / 6 Nights">
                            </div>
                        </div>

                        <!-- Group Size -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Group Size</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="groupsize"
                                    placeholder="e.g. 10–20 People">
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Rating (0–5)</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control1" name="rating"
                                    min="0" max="5" step="0.1" placeholder="e.g. 4.8">
                            </div>
                        </div>

                        <!-- Package Features -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Package Features</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control1" name="packagefeatures"
                                    placeholder="e.g. Free Pickup-Drop facility" required>
                            </div>
                        </div>

                        <!-- Package Details — with AI Generate button -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Package Details</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" rows="5" cols="50" name="packagedetails"
                                    id="packagedetails"
                                    placeholder="Package Details" required></textarea>

                                <button type="button" id="ai-gen-btn"
                                    style="margin-top:8px; background:#0ea5e9; color:#fff; border:none;
                                           border-radius:6px; padding:8px 18px; font-size:13px;
                                           cursor:pointer; display:flex; align-items:center; gap:7px;">
                                    <i class="fa fa-magic"></i>
                                    <span id="ai-gen-label">AI Generate Description</span>
                                </button>
                                <small style="color:#888; display:block; margin-top:4px;">
                                    Fill in Name, Location, Type &amp; Price above, then click to auto-generate.
                                </small>
                            </div>
                        </div>

                        <!-- Package Image -->
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Package Image</label>
                            <div class="col-sm-8">
                                <input type="file" name="packageimage" id="packageimage"
                                    accept="image/*" required>
                                <small style="color:#888;">Max 2MB. JPG, PNG, GIF, WEBP only.</small>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-sm-8 col-sm-offset-2">
                                <button type="submit" name="submit" class="btn-primary btn">Create</button>
                                <button type="reset" class="btn-inverse btn">Reset</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="panel-footer"></div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        var navoffeset = $(".header-main").offset().top;
        $(window).scroll(function() {
            var scrollpos = $(window).scrollTop();
            if (scrollpos >= navoffeset) {
                $(".header-main").addClass("fixed");
            } else {
                $(".header-main").removeClass("fixed");
            }
        });
    });
    </script>

    <div class="inner-block"></div>
    <?php include('includes/footer.php'); ?>
</div>
</div>

<?php include('includes/sidebarmenu.php'); ?>
<div class="clearfix"></div>

<script>
var toggle = true;
$(".sidebar-icon").click(function() {
    if (toggle) {
        $(".page-container").addClass("sidebar-collapsed").removeClass("sidebar-collapsed-back");
        $("#menu span").css({"position": "absolute"});
    } else {
        $(".page-container").removeClass("sidebar-collapsed").addClass("sidebar-collapsed-back");
        setTimeout(function() {
            $("#menu span").css({"position": "relative"});
        }, 400);
    }
    toggle = !toggle;
});
</script>

<!-- ── AI Description Generator Script ── -->
<script>
document.getElementById('ai-gen-btn').addEventListener('click', function(){
    var name     = (document.querySelector('[name=packagename]')     || {value:''}).value.trim();
    var location = (document.querySelector('[name=packagelocation]') || {value:''}).value.trim();
    var ptype    = (document.querySelector('[name=packagetype]')     || {value:''}).value.trim();
    var price    = (document.querySelector('[name=packageprice]')    || {value:''}).value.trim();
    var duration = (document.querySelector('[name=duration]')        || {value:''}).value.trim();
    var features = (document.querySelector('[name=packagefeatures]') || {value:''}).value.trim();

    if(!name || !location){
        alert('Please fill in at least the Package Name and Location first.');
        return;
    }

    var btn   = document.getElementById('ai-gen-btn');
    var label = document.getElementById('ai-gen-label');
    btn.disabled = true;
    label.textContent = 'Generating…';

    fetch('http://localhost:5000/generate-desc', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({name:name, location:location, type:ptype,
                              price:price, duration:duration, features:features})
    })
    .then(function(r){ return r.json(); })
    .then(function(data){
        if(data.description){
            document.getElementById('packagedetails').value = data.description;
        } else {
            alert('AI could not generate a description. ' + (data.error||''));
        }
    })
    .catch(function(){
        alert('Could not reach AI service. Make sure ai_service.py is running.');
    })
    .finally(function(){
        btn.disabled = false;
        label.textContent = 'AI Generate Description';
    });
});
</script>
<!-- ── end AI script ── -->

<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>