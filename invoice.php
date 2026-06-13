<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0) {	
    header('location:index.php');
} else {
    $bkid = intval($_GET['bkid']);
    $email = $_SESSION['login'];
    
    $sql = "SELECT tblbooking.BookingId, tblbooking.FromDate, tblbooking.ToDate, tblbooking.RegDate, tblbooking.Comment, tbltourpackages.PackageName, tbltourpackages.PackagePrice, tbltourpackages.PackageLocation, tbltourpackages.PackageType, tblusers.FullName, tblusers.MobileNumber, tblusers.EmailId FROM tblbooking JOIN tbltourpackages ON tbltourpackages.PackageId=tblbooking.PackageId JOIN tblusers ON tblusers.EmailId=tblbooking.UserEmail WHERE tblbooking.BookingId=:bkid AND tblbooking.UserEmail=:email AND tblbooking.status=1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bkid', $bkid, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if(!$result) {
        // Find if it exists but is not confirmed
        $chk = $dbh->prepare("SELECT status FROM tblbooking WHERE BookingId=:bkid AND UserEmail=:email");
        $chk->execute([':bkid'=>$bkid, ':email'=>$email]);
        $row = $chk->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            if ($row['status'] == '0') {
                echo "<div style='padding:50px; text-align:center; font-family:sans-serif;'><h2>Booking Pending Validation</h2><p>Your invoice will be generated automatically once an Admin confirms your booking.</p><a href='tour-history.php'>Return to History</a></div>";
            } elseif ($row['status'] == '2') {
                echo "<div style='padding:50px; text-align:center; font-family:sans-serif; color:red;'><h2>Booking Cancelled</h2><p>This booking has been cancelled. No invoice is available.</p><a href='tour-history.php'>Return to History</a></div>";
            } else {
                echo "<div style='padding:50px; text-align:center; font-family:sans-serif;'><h2>System Error</h2><p>Invoice not found for this confirmed booking.</p><a href='tour-history.php'>Return to History</a></div>";
            }
        } else {
            echo "<div style='padding:50px; text-align:center; font-family:sans-serif;'><h2>Access Denied</h2><p>Invoice not found or you do not have permission to view it.</p><a href='tour-history.php'>Return to History</a></div>";
        }
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #BK<?php echo htmlentities($result->BookingId); ?></title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <style>
        body { background: #f1f5f9; padding: 40px; font-family: 'Open Sans', sans-serif; }
        .invoice-box {
            max-width: 800px; margin: auto; padding: 40px;
            border: 1px solid #eee; box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            font-size: 16px; line-height: 24px; font-family: 'Helvetica Neue', 'Helvetica', sans-serif;
            color: #333; background: #fff;
        }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 35px; line-height: 45px; color: #0ea5e9; font-weight: bold; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #0ea5e9; color: white; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; font-size: 20px; color: #0ea5e9; }
        .btn-download { display: block; width: 200px; margin: 20px auto; text-align: center; background: #0369a1; color: white; padding: 12px; border-radius: 5px; text-decoration: none; font-weight: bold; cursor: pointer; }
    </style>
    <!-- Include html2pdf library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>

    <button id="downloadPdf" class="btn-download">Download PDF Invoice</button>

    <div class="invoice-box" id="invoice">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                TravelMate
                            </td>
                            
                            <td>
                                Invoice #: BK<?php echo htmlentities($result->BookingId); ?><br>
                                Created: <?php echo date("F j, Y", strtotime($result->RegDate)); ?><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>TravelMate Inc.</strong><br>
                                123 Touring Avenue<br>
                                Cityscape, TX 75001
                            </td>
                            
                            <td>
                                <strong>Billed To:</strong><br>
                                <?php echo htmlentities($result->FullName); ?><br>
                                <?php echo htmlentities($result->EmailId); ?><br>
                                <?php echo htmlentities($result->MobileNumber); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>Booking Details</td>
                <td></td>
            </tr>
            
            <tr class="item">
                <td>Package Name</td>
                <td><?php echo htmlentities($result->PackageName); ?></td>
            </tr>

            <tr class="item">
                <td>Package Type / Location</td>
                <td><?php echo htmlentities($result->PackageType); ?> / <?php echo htmlentities($result->PackageLocation); ?></td>
            </tr>
            
            <tr class="item">
                <td>Travel Dates</td>
                <td><?php echo htmlentities($result->FromDate); ?> to <?php echo htmlentities($result->ToDate); ?></td>
            </tr>
            
            <tr class="item last">
                <td>Special Notes</td>
                <td><?php echo htmlentities($result->Comment); ?></td>
            </tr>
            
            <tr class="heading">
                <td>Payment Summary</td>
                <td>Amount</td>
            </tr>
            
            <tr class="item">
                <td>Package Base Price</td>
                <td>Rs <?php echo htmlentities($result->PackagePrice); ?></td>
            </tr>
            
            <tr class="total">
                <td></td>
                <td>Total: Rs <?php echo htmlentities($result->PackagePrice); ?></td>
            </tr>
        </table>
        
        <div style="font-size: 12px; color: #777; margin-top: 40px; text-align: center;">
            Thank you for booking with TravelMate! Enjoy your trip.<br>
            If you have any questions, please contact support at support@travelmate.com.
        </div>
    </div>

    <script>
        document.getElementById('downloadPdf').addEventListener('click', function () {
            var element = document.getElementById('invoice');
            var opt = {
                margin:       0.5,
                filename:     'Invoice_BK<?php echo htmlentities($result->BookingId); ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            
            // New Promise-based usage:
            html2pdf().set(opt).from(element).save();
        });
    </script>
</body>
</html>
<?php } ?>
