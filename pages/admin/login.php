<?php
	if((isset($_POST["username"]) && isset($_POST["password"])) &&
	!(($_POST["username"] == "") || ($_POST["password"] == "")))
	{
		include_once("creds.php");
		if($_POST["username"] == $username && hash("md5", $_POST["password"]) == $password)
		{
			?>
            <script>
				setCookie("McCownUser", "<?php echo hash("md5", $username); ?>", 2);
				setCookie("McCownPass", "<?php echo hash("md5", ($password . $hash)); ?>", 2);
				$("#result").load('pages/admin_page.php');
			</script>
            <?php
		} else {
			echo "<h1>Administration Login</h1>
			<h2><center>Invalid Credentials</center></h2>";
			include("login_form.html");
		}
	} else {
?>
	<div id="admin_content">
    	<h1>Administration Login</h1>
        <?php include("login_form.html"); ?>
    </div>
<?php } ?>
<br /><br />
