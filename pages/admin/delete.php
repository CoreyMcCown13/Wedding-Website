<?php
if (isset($_COOKIE["McCownUser"]) && isset($_COOKIE["McCownPass"])) {
	include_once("creds.php");
	if (($_COOKIE["McCownUser"] == hash('md5', $username)) && ($_COOKIE["McCownPass"] == hash('md5', $password . $hash)))
	{
		deleteProceed(); 
	} else { 
		$need_login = true;
	}
} else { 
	$need_login = true;
}
if($need_login)
{
	die("Forbidden");
}


function deleteProceed() {
	if(isset($_POST["id"]) && $_POST["id"] != "")
	{
		$id = strtolower($_POST["id"]);
		include("../../../includes/sql.inc.php");
		$db = new PDO("mysql:host=localhost;dbname=mccownwedding;", DB_USER, DB_PASSWORD);	
		$STH = $db->prepare('DELETE FROM guests WHERE guest_id = :id LIMIT 1');
		$STH->bindParam(':id', $id, PDO::PARAM_STR);
		$STH->execute();
		echo "Guest " . $id . " deleted.";
	} else 
		die("Need ID.");
}
?>
