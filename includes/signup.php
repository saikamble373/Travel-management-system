<?php
error_reporting(0);
if(isset($_POST['submit']))
{
$fname=$_POST['fname'];
$mnumber=$_POST['mobilenumber'];
$email=$_POST['email'];
$password=md5($_POST['password']);
$sql="INSERT INTO  tblusers(FullName,MobileNumber,EmailId,Password) VALUES(:fname,:mnumber,:email,:password)";
$query = $dbh->prepare($sql);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':mnumber',$mnumber,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':password',$password,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$_SESSION['msg']="You are Scuccessfully registered. Now you can login ";
header('location:thankyou.php');
}
else 
{
$_SESSION['msg']="Something went wrong. Please try again.";
header('location:thankyou.php');
}
}
?>
<!--Javascript for check email availabilty-->
<script>
function checkAvailability() {

$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'emailid='+$("#email").val(),
type: "POST",
success:function(data){
$("#user-availability-status").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
			</div>
			<div class="modal-body modal-spa">
				<div class="tm-modal">
					<div class="tm-modal-left">
						<i class="fa fa-user-plus mlicon" style="color:white"></i>
						<div class="mlbrand">Travel<span>Mate</span></div>
						<p>Create an account to book amazing tours and keep track of your wishlists.</p>
					</div>
					<div class="tm-modal-right">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute; top:20px; right:20px; z-index:10; font-size: 28px; opacity: 0.5;"><span aria-hidden="true">×</span></button>
						<h3>Sign Up</h3>
						<span class="msub">Join our community of travelers</span>
						
						<form name="signup" method="post">
							<div class="tm-mf">
								<label>Full Name</label>
								<div class="mf-wrap">
									<i class="fa fa-user"></i>
									<input type="text" name="fname" placeholder="Full Name" autocomplete="off" required="">
								</div>
							</div>
							<div class="tm-mf">
								<label>Mobile Number</label>
								<div class="mf-wrap">
									<i class="fa fa-phone"></i>
									<input type="text" name="mobilenumber" maxlength="10" placeholder="Mobile Number" autocomplete="off" required="">
								</div>
							</div>
							<div class="tm-mf">
								<label>Email Address</label>
								<div class="mf-wrap">
									<i class="fa fa-envelope"></i>
									<input type="email" name="email" id="email" onBlur="checkAvailability()" placeholder="Email Address" autocomplete="off" required="">
								</div>
								<span id="user-availability-status" style="font-size:12px;"></span> 
							</div>
							<div class="tm-mf">
								<label>Password</label>
								<div class="mf-wrap">
									<i class="fa fa-key"></i>
									<input type="password" name="password" placeholder="Password" required="">
								</div>
							</div>
							
							<input type="submit" name="submit" id="submit" value="CREATE ACCOUNT" class="tm-modal-submit">
						</form>
						
						<p class="modal-terms">By signing up you agree to our <a href="page.php?type=terms">Terms</a> and <a href="page.php?type=privacy">Privacy Policy</a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>