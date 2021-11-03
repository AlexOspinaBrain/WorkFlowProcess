<?php
	// Some settings
	$msg = "";
	$username_admin = "admin";
	$password_admin = "admin";

    $username_user = "user";
	$password_user = "user"; 
	
	if (isset($_POST['submit_button'])) {
		// If password match, then set login
		if ($_POST['username'] == $username_admin && $_POST['password'] == $password_admin) {
			// Set session
			session_start();
			$_SESSION['isLoggedIn'] = true;
			$_SESSION['login'] = true;
			$_SESSION['level'] = "admin";

			// Redirect
			header("location:beranda.php");
			die;
		}
		elseif ($_POST['username'] == $username_user && $_POST['password'] == $password_user){
			// Set session
			session_start();
			$_SESSION['isLoggedIn'] = true;
			$_SESSION['login'] = true;
			$_SESSION['level'] = "user";

			
			// Redirect
			header("location:beranda.php");
			die;
		}
		else {
			$msg = "Username / Password salah...";
		}
	}
?>

<?php
if ($_SESSION['login'] == false) {
?>
<html>
<head>
<title>Tinymce Upload Gambar</title>
<style>
body { font-family: Arial, Verdana; font-size: 13px; }
fieldset { display: block; width: 250px; }
legend { font-weight: bold; }
label { display: block; }
div { margin-bottom: 10px; }
div.last { margin: 0; }
div.container { position: absolute; top: 20%; left: 50%; margin: -100px 0 0 -100px; }
h1 { font-size: 14px; }
.button { border: 1px solid gray; font-family: Arial, Verdana; font-size: 11px; }
.error { color: red; margin: 0; margin-top: 10px; }
</style>
</head>
<body>

<div class="container">
	<fieldset>
	<legend>Level User</legend>
	<font style="font-size:10px;">Admin : Username = admin & Password = admin<br>
	User : Username = user & Password = user</font>
	</fieldset>
	<br>
	<form action="index.php" method="post">
		
		<fieldset>
			<legend>Login Users</legend>

			<div>
				<label>Username:</label>
				<input type="text" name="username" class="text" value="<?php echo isset($_POST['username']) ? htmlentities($_POST['username']) : ""; ?>" />
			</div>

			<div>
				<label>Password:</label>
				<input type="password" name="password" class="text" value="<?php echo isset($_POST['password']) ? htmlentities($_POST['password']) : ""; ?>" />
			</div>

			<div class="last">
				<input type="submit" name="submit_button" value="Login" class="button" />
			</div>

<?php if ($msg) { ?>
			<div class="error">
				<?php echo $msg; ?>
			</div>
<?php } ?>
		</fieldset>
	</form>
	
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=123635271055329";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="fb-like-box" data-href="http://www.facebook.com/pages/Dokumen-Ary/190077674426569" data-width="275" data-height="300" data-show-faces="true" data-stream="false" data-header="true"></div>
</div>

</body>
</html>

<?php
}
else{
	header('location:beranda.php');
}
?>