<?php
session_start();
if(isset($_POST['signin']))
{
$email=$_POST['email'];
$password=md5($_POST['password']);
$sql ="SELECT EmailId,Password FROM tblusers WHERE EmailId=:email and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
$_SESSION['login']=$_POST['email'];
echo "<script type='text/javascript'> document.location = 'package-list.php'; </script>";
} else{
	
	echo "<script>alert('Invalid Details');</script>";

}

}

?>

<div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content modal-info">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>						
			</div>
			<div class="modal-body modal-spa">
				<div class="tm-modal">
					<div class="tm-modal-left">
						<i class="fa fa-lock mlicon" style="color:white"></i>
						<div class="mlbrand">Travel<span>Mate</span></div>
						<p>Welcome back! Securely log in to manage your bookings, wishlist, and issues.</p>
					</div>
					<div class="tm-modal-right">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute; top:20px; right:20px; z-index:10; font-size: 28px; opacity: 0.5;"><span aria-hidden="true">×</span></button>
						<h3>Sign In</h3>
						<span class="msub">Access your premium travel dashboard</span>
						
						<form method="post">
							<div class="tm-mf">
								<label>Email Address</label>
								<div class="mf-wrap">
									<i class="fa fa-envelope"></i>
									<input type="email" name="email" id="email" placeholder="Enter your Email" required="">
								</div>
							</div>
							<div class="tm-mf">
								<label>Password</label>
								<div class="mf-wrap">
									<i class="fa fa-key"></i>
									<input type="password" name="password" id="password" placeholder="Password" required="">
								</div>
							</div>
							
							<div class="tm-modal-link" style="text-align: right; margin: -5px 0 15px;">
								<a href="forgot-password.php">Forgot password?</a>
							</div>
							
							<input type="submit" name="signin" value="SIGN IN" class="tm-modal-submit">
						</form>
						
						<p class="modal-terms">By logging in you agree to our <a href="page.php?type=terms">Terms</a> and <a href="page.php?type=privacy">Privacy Policy</a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>