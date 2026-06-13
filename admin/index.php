<?php
session_start();
include('includes/config.php');
if(isset($_POST['login']))
{
    $uname=$_POST['username'];
    $password=md5($_POST['password']);
    $sql ="SELECT UserName,Password FROM admin WHERE UserName=:uname and Password=:password";
    $query= $dbh -> prepare($sql);
    $query-> bindParam(':uname', $uname, PDO::PARAM_STR);
    $query-> bindParam(':password', $password, PDO::PARAM_STR);
    $query-> execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    if($query->rowCount() > 0)
    {
        $_SESSION['alogin']=$_POST['username'];
        echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
    } else{
        echo "<script>alert('Invalid Details');</script>";
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>TravelMate | Admin Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0a3460;
            --accent: #f97316;
            --t: 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, rgba(10,52,96,0.88) 0%, rgba(14,165,233,0.65) 100%),
                        url('../images/banner.jpg') no-repeat center center;
            background-size: cover;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        body::after {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse at 75% 50%, rgba(249,115,22,0.15) 0%, transparent 60%);
            pointer-events: none; z-index: 1;
        }
        
        .admin-login-wrapper {
            position: relative; z-index: 5;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            width: 100%; max-width: 440px;
            box-shadow: 0 24px 80px rgba(10,52,96,0.4);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.3);
            margin: 20px;
        }
        
        .login-header {
            background: linear-gradient(160deg, #0a3460 0%, #0f4c81 55%, #0369a1 100%);
            padding: 40px 30px;
            text-align: center; color: white;
        }
        .login-header h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0; font-size: 28px; font-weight: 800;
        }
        .login-header h1 span { color: var(--accent); }
        .login-header p {
            margin: 8px 0 0; font-size: 14px; opacity: 0.85;
            font-weight: 500;
        }
        
        .login-body { padding: 40px 36px; }
        
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block; font-size: 12px; font-weight: 700; color: #475569;
            margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 15px;
        }
        .form-control {
            width: 100%; height: 50px;
            border: 1.5px solid #e2e8f0; border-radius: 12px;
            padding: 0 44px; font-size: 14px; color: #1e293b;
            font-family: 'Inter', sans-serif; background: #f8fafc;
            transition: all var(--t); outline: none;
        }
        .form-control:focus {
            background: white; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10,52,96,0.1);
        }
        
        .forgot-link {
            text-align: right; margin-bottom: 24px;
        }
        .forgot-link a {
            color: var(--primary); font-size: 13px; font-weight: 600;
            text-decoration: none; transition: color var(--t);
        }
        .forgot-link a:hover { color: var(--accent); }
        
        .btn-login {
            width: 100%; height: 50px; border: none; border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), #1e6bb8);
            color: white; font-size: 15px; font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif; cursor: pointer;
            transition: all var(--t); box-shadow: 0 4px 14px rgba(10,52,96,0.25);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(10,52,96,0.35);
        }
        
        .back-link {
            text-align: center; margin-top: 24px;
        }
        .back-link a {
            color: #64748b; font-size: 14px; text-decoration: none;
            font-weight: 500; display: inline-flex; align-items: center; gap: 6px;
            transition: color var(--t);
        }
        .back-link a:hover { color: var(--primary); }
    </style>
</head> 
<body>
    <div class="admin-login-wrapper">
        <div class="login-header">
            <h1>Travel<span>Mate</span></h1>
            <p>Admin Control Panel</p>
        </div>
        <div class="login-body">
            <form method="post">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-wrap">
                        <i class="fa fa-user"></i>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required="">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <i class="fa fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required="">
                    </div>
                </div>
                
                <div class="forgot-link">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn-login" name="login">Sign In Securely</button>
                
                <div class="back-link">
                    <a href="../index.php"><i class="fa fa-arrow-left"></i> Back to Frontend</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>