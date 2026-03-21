<?php
session_start();

// ✅ FIX 1: Show errors during development (disable on production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

// ✅ FIX 2: Validate $pid early
$pid = intval($_GET['pid']);
if ($pid <= 0) {
    die("Invalid package ID.");
}

// ✅ FIX 3: Initialize $msg to avoid undefined variable notice
$msg = "";
$error = "";

if (isset($_POST['submit'])) {

    // ✅ FIX 6: CSRF check
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

    // ✅ FIX 5: Validate rating (must be numeric between 0 and 5)
    $rating = floatval($_POST['rating']);
    if ($rating < 0 || $rating > 5) {
        $error = "Rating must be a number between 0 and 5.";
    }

    if (empty($error)) {

        // ✅ FIX 4: Handle optional image upload
        $imageUpdate = "";
        if (isset($_FILES['packageimage']) && $_FILES['packageimage']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($_FILES['packageimage']['tmp_name']);

            if (!in_array($fileType, $allowed)) {
                $error = "Invalid image type. Only JPG, PNG, GIF, WEBP allowed.";
            } elseif ($_FILES['packageimage']['size'] > 2 * 1024 * 1024) {
                $error = "Image size must be under 2MB.";
            } else {
                $ext = pathinfo($_FILES['packageimage']['name'], PATHINFO_EXTENSION);
                $newImageName = 'pkg_' . time() . '.' . $ext;
                $uploadPath = 'pacakgeimages/' . $newImageName;

                if (!move_uploaded_file($_FILES['packageimage']['tmp_name'], $uploadPath)) {
                    $error = "Failed to upload image. Check folder permissions.";
                } else {
                    $imageUpdate = ", PackageImage=:pimage";
                }
            }
        }

        if (empty($error)) {
            $sql = "UPDATE TblTourPackages SET
                        PackageName=:pname,
                        PackageType=:ptype,
                        PackageLocation=:plocation,
                        PackagePrice=:pprice,
                        PackageFetures=:pfeatures,
                        PackageDetails=:pdetails,
                        duration=:duration,
                        groupsize=:groupsize,
                        rating=:rating
                        $imageUpdate
                    WHERE PackageId=:pid";

            $query = $dbh->prepare($sql);
            $query->bindParam(':pname',     $pname,     PDO::PARAM_STR);
            $query->bindParam(':ptype',     $ptype,     PDO::PARAM_STR);
            $query->bindParam(':plocation', $plocation, PDO::PARAM_STR);
            $query->bindParam(':pprice',    $pprice,    PDO::PARAM_STR);
            $query->bindParam(':pfeatures', $pfeatures, PDO::PARAM_STR);
            $query->bindParam(':pdetails',  $pdetails,  PDO::PARAM_STR);
            $query->bindParam(':duration',  $duration,  PDO::PARAM_STR);
            $query->bindParam(':groupsize', $groupsize, PDO::PARAM_STR);
            $query->bindParam(':rating',    $rating,    PDO::PARAM_STR);
            $query->bindParam(':pid',       $pid,       PDO::PARAM_INT);

            if (!empty($imageUpdate)) {
                $query->bindParam(':pimage', $newImageName, PDO::PARAM_STR);
            }

            $query->execute();
            $msg = "Package Updated Successfully";
        }
    }
}

// ✅ FIX 6: Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch current package data
$sql = "SELECT * FROM TblTourPackages WHERE PackageId=:pid";
$query = $dbh->prepare($sql);
$query->bindParam(':pid', $pid, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>TravelMate | Admin - Edit Package</title>
    <link href="css/bootstrap.min.css" rel="stylesheet"/>
    <link href="css/style.css" rel="stylesheet"/>
    <link href="css/font-awesome.css" rel="stylesheet">
    <script src="js/jquery-2.1.4.min.js"></script>
</head>
<body>
<div class="page-container">
    <div class="left-content">
        <div class="mother-grid-inner">
            <?php include('includes/header.php'); ?>

            <ol class="breadcrumb">
                <li>Update Tour Package</li>
            </ol>

            <div class="grid-form">
                <div class="grid-form1">
                    <h3>Update Package</h3>

                    <?php if ($msg): ?>
                        <div class="succWrap"><?php echo htmlentities($msg); ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="errWrap" style="color:red; margin-bottom:10px;"><?php echo htmlentities($error); ?></div>
                    <?php endif; ?>

                    <?php if ($query->rowCount() == 0): ?>
                        <p>No package found with this ID.</p>
                    <?php else: ?>
                        <?php foreach ($results as $result): ?>

                        <form class="form-horizontal" method="post" enctype="multipart/form-data">

                            <!-- ✅ FIX 6: CSRF token -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <!-- Package Name -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Package Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control1" name="packagename"
                                        value="<?php echo htmlentities($result->PackageName); ?>" required>
                                </div>
                            </div>

                            <!-- Package Type -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Package Type</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control1" name="packagetype"
                                        value="<?php echo htmlentities($result->PackageType); ?>" required>
                                </div>
                            </div>

                            <!-- Package Location -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Package Location</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control1" name="packagelocation"
                                        value="<?php echo htmlentities($result->PackageLocation); ?>" required>
                                </div>
                            </div>

                            <!-- Package Price -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Package Price</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control1" name="packageprice"
                                        value="<?php echo htmlentities($result->PackagePrice); ?>" required>
                                </div>
                            </div>

                            <!-- Duration -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Duration</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control1" name="duration"
                                        value="<?php echo htmlentities($result->duration); ?>"
                                        placeholder="e.g. 7 Days / 6 Nights">
                                </div>
                            </div>

                            <!-- Group Size -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Group Size</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control1" name="groupsize"
                                        value="<?php echo htmlentities($result->groupsize); ?>"
                                        placeholder="e.g. 10–20 People">
                                </div>
                            </div>

                            <!-- ✅ FIX 5: Rating - number input with range -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Rating (0–5)</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control1" name="rating"
                                        value="<?php echo htmlentities($result->rating); ?>"
                                        min="0" max="5" step="0.1" placeholder="e.g. 4.8">
                                </div>
                            </div>

                            <!-- Package Features -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Package Features</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control1" name="packagefeatures"
                                        value="<?php echo htmlentities($result->PackageFetures); ?>">
                                </div>
                            </div>

                            <!-- Package Details -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Package Details</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" name="packagedetails"><?php echo htmlentities($result->PackageDetails); ?></textarea>
                                </div>
                            </div>

                            <!-- ✅ FIX 4: Image - show current + allow upload -->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Current Image</label>
                                <div class="col-sm-8">
                                    <img src="pacakgeimages/<?php echo htmlentities($result->PackageImage); ?>"
                                        width="200" style="margin-bottom:8px; display:block;"><br>
                                    <label>Change Image (optional):</label>
                                    <input type="file" name="packageimage" accept="image/*" class="form-control1">
                                    <small style="color:#888;">Max 2MB. JPG, PNG, GIF, WEBP only.</small>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-2">
                                    <button type="submit" name="submit" class="btn-primary btn">Update Package</button>
                                </div>
                            </div>

                        </form>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>

            <?php include('includes/footer.php'); ?>
        </div>
    </div>
    <?php include('includes/sidebarmenu.php'); ?>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>