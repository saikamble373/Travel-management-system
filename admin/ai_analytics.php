<?php
/*
 * ============================================================
 *  FILE:  tms/admin/ai-analytics.php   (NEW FILE — create it)
 *
 *  HOW TO ADD TO SIDEBAR:
 *  Open admin/includes/sidebarmenu.php
 *  After the line:  <li><a href="manage-enquires.php">...
 *  Add this line:
 *      <li><a href="ai-analytics.php"><i class="fa fa-bar-chart"></i> <span>AI Analytics</span></a></li>
 * ============================================================
 */
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin']) == 0){
    header('location:index.php');
    exit();
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>TravelMate | AI Analytics</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link href="css/admin-theme.css" rel='stylesheet' type='text/css' />
<link href="css/font-awesome.css" rel="stylesheet">
<script src="js/jquery-2.1.4.min.js"></script>
<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<style>
.ai-card {
    background: #fff; border-radius: 12px; padding: 24px;
    margin-bottom: 24px; box-shadow: 0 2px 12px rgba(0,0,0,.07);
}
.ai-card h4 {
    font-size: 15px; font-weight: 700; color: #334155;
    margin: 0 0 16px; display:flex; align-items:center; gap:8px;
}
.ai-card h4 i { color: #0ea5e9; }
.insight-box {
    background: #f0f9ff; border-left: 4px solid #0ea5e9;
    border-radius: 8px; padding: 16px; font-size: 14px;
    color: #0c4a6e; line-height: 1.8; white-space: pre-line;
}
.top-pkg-row {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 0; border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
}
.top-pkg-row:last-child { border-bottom: none; }
.pkg-rank {
    width: 26px; height: 26px; border-radius: 50%;
    background: #0ea5e9; color: #fff;
    font-size: 12px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pkg-bar-wrap { flex: 1; background: #f1f5f9; border-radius: 999px; height: 8px; }
.pkg-bar      { height: 8px; border-radius: 999px; background: linear-gradient(90deg,#0369a1,#0ea5e9); }
.type-pill {
    display: inline-flex; align-items: center; gap: 8px;
    background: #f0f9ff; border: 1.5px solid #bae6fd;
    border-radius: 999px; padding: 8px 16px; font-size: 13px;
    color: #0c4a6e; margin: 4px;
}
#ai-loader {
    text-align: center; padding: 60px 0; font-size: 14px; color: #94a3b8;
}
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
            <a href="dashboard.php">Home</a>
            <i class="fa fa-angle-right"></i> AI Analytics
        </li>
    </ol>

    <div style="padding: 20px 24px;">
        <h3 style="margin-bottom:20px; color:#1e293b;">
            <i class="fa fa-bar-chart" style="color:#0ea5e9;"></i> AI Analytics &amp; Insights
        </h3>

        <div id="ai-loader">
            <i class="fa fa-spinner fa-spin fa-2x"></i><br><br>
            Loading analytics from AI service…
        </div>

        <div id="ai-content" style="display:none;">

            <!-- AI Insight -->
            <div class="ai-card">
                <h4><i class="fa fa-lightbulb-o"></i> AI Business Insight</h4>
                <div class="insight-box" id="ai-insight-text">—</div>
            </div>

            <div class="row">
                <!-- Monthly Bookings Chart -->
                <div class="col-md-7">
                    <div class="ai-card">
                        <h4><i class="fa fa-line-chart"></i> Monthly Bookings (Last 6 Months)</h4>
                        <canvas id="monthlyChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Price by Type -->
                <div class="col-md-5">
                    <div class="ai-card">
                        <h4><i class="fa fa-tags"></i> Avg. Price by Package Type</h4>
                        <div id="type-pills"></div>
                    </div>
                </div>
            </div>

            <!-- Top Packages -->
            <div class="ai-card">
                <h4><i class="fa fa-trophy"></i> Top 5 Most Booked Packages</h4>
                <div id="top-packages-list"></div>
            </div>

        </div><!-- /ai-content -->
    </div>

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
        setTimeout(function() { $("#menu span").css({"position": "relative"}); }, 400);
    }
    toggle = !toggle;
});
</script>
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
fetch('http://localhost:5000/analytics')
.then(function(r){ return r.json(); })
.then(function(data){
    document.getElementById('ai-loader').style.display = 'none';
    document.getElementById('ai-content').style.display = 'block';

    // AI Insight
    document.getElementById('ai-insight-text').textContent =
        data.ai_insight || 'No insight available.';

    // Monthly Bookings Chart
    var months = (data.monthly_bookings || []).map(function(m){ return m.month; });
    var counts  = (data.monthly_bookings || []).map(function(m){ return m.bookings; });
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Bookings',
                data: counts,
                backgroundColor: 'rgba(14,165,233,.75)',
                borderColor: '#0369a1',
                borderWidth: 1.5,
                borderRadius: 6
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Top Packages
    var top  = data.top_packages || [];
    var maxB = top.length ? top[0].total_bookings : 1;
    var html = '';
    top.forEach(function(p, i){
        var pct = Math.round((p.total_bookings / maxB) * 100);
        html += '<div class="top-pkg-row">'
             + '<div class="pkg-rank">' + (i+1) + '</div>'
             + '<div style="flex:1; min-width:0;">'
             + '<div style="font-weight:600; color:#1e293b; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">'
             + p.PackageName + '</div>'
             + '<div class="pkg-bar-wrap" style="margin-top:5px;">'
             + '<div class="pkg-bar" style="width:' + pct + '%"></div></div>'
             + '</div>'
             + '<div style="font-weight:700; color:#0ea5e9; font-size:13px; flex-shrink:0; margin-left:8px;">'
             + p.total_bookings + ' bookings</div>'
             + '</div>';
    });
    document.getElementById('top-packages-list').innerHTML =
        html || '<p style="color:#94a3b8; font-size:13px;">No bookings data yet.</p>';

    // Price by Type
    var types = data.by_type || [];
    var pills = types.map(function(t){
        return '<span class="type-pill">'
             + '<i class="fa fa-tag" style="color:#0ea5e9; font-size:12px;"></i>'
             + '<strong>' + t.PackageType + '</strong>'
             + '&nbsp;&#8377;' + Number(t.avg_price).toLocaleString('en-IN')
             + ' avg &nbsp;<span style="color:#94a3b8;">(' + t.count + ' pkg)</span>'
             + '</span>';
    }).join('');
    document.getElementById('type-pills').innerHTML =
        pills || '<p style="color:#94a3b8; font-size:13px;">No data.</p>';
})
.catch(function(){
    document.getElementById('ai-loader').innerHTML =
        '<i class="fa fa-exclamation-triangle" style="color:#f59e0b; font-size:24px;"></i>'
        + '<br><br><strong>Could not reach AI service.</strong>'
        + '<br><span style="font-size:13px;">Make sure <code>ai_service.py</code> is running on port 5000.</span>';
});
</script>
</body>
</html>