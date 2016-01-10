<?php
if (isset($_COOKIE["McCownUser"]) && isset($_COOKIE["McCownPass"])) {
	include_once("creds.php");
	if (($_COOKIE["McCownUser"] == hash('md5', $username)) && ($_COOKIE["McCownPass"] == hash('md5', $password . $hash)))
	{
		?>
        <script>
		var scrolled = false;
		function updateScroll(){
			if(!scrolled){
				var element = document.getElementById("console");
				element.scrollTop = element.scrollHeight;
			}
		}
		
		$("#console").on('scroll', function(){
			scrolled=true;
		});
		</script>
        <h1>Bulk Invitation Import</h1>
        <p><em>This should show that I love you.</em><br>
		<a href="http://wedding.coreymccown.com/">Back to homepage.</a></p>
        <div id="console" style="bottom:0; overflow:scroll; position:absolute; overflow:scroll; width:99%; max-height:80%;">
        <?php importProceed(); ?>
        </div>
        <?php
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
function generateRandomString($length) {
    $characters = '123456789abcdefghjklmnpqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function importProceed() {
	// check there are no errors
	if($_FILES['csv']['error'] == 0) {
		$theFile = $_FILES['csv'];
		echo "File name: " . $theFile['name'] . "<br />";
		echo "Temp name: " . $theFile['tmp_name'] . "<br />";
		echo "File type: " . $theFile['type'] . "<br />";
		echo "File size: " . $theFile['size'] . " bytes<br />";
		$csv = array();
		$name = $theFile['name'];
		$ext = strtolower(end(explode('.', $theFile['name'])));
		$type = $theFile['type'];
		$tmpName = $theFile['tmp_name'];
		
		// check the file is a csv
		if($ext === 'csv'){
			//CSV file is open, time to parse...
			echo '<font color="#095F00"><strong>File is opened.</strong></font><br /><br />';
			$lines = array();
			$fh = fopen($theFile['tmp_name'], 'r+');
			while( ($row = fgetcsv($fh, 8192)) !== FALSE ) {
				$lines[] = $row;
			}
			include("../../../includes/sql.inc.php");
			$db = new PDO("mysql:host=localhost;dbname=mccownwedding;", DB_USER, DB_PASSWORD);	
			$STH = $db->prepare('SELECT guest_id, firstname, lastname FROM guests');
			$STH->execute();
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$assigned_IDs = array();
			$invited_names = array();
			if ($STH->rowCount() > 0)
				while($row = $STH->fetch()) 
				{
					array_push($assigned_IDs, $row["guest_id"]);
					array_push($invited_names, strtolower($row["firstname"] . " " . $row["lastname"]));
				}
							
			$STH = $db->prepare('INSERT INTO guests (guest_id, firstname, lastname, additional, requests) VALUES (:guest_id, :firstname, :lastname, :additional, :requests)');
			for($i = 1; $i < count($lines); $i++)
			{
				$thisfirstname = $lines[$i][0];
				$thislastname = $lines[$i][1];
				$thisname = $thisfirstname . " " . $thislastname;
				$thisadditional = $lines[$i][2];
				$thisrequests = $lines[$i][3];
				echo "Name: " . $thisname . "<br />";
				echo "Additional: " . $thisadditional . "<br />";
				echo "Requests: " . $thisrequests . "<br />";
				if(!in_array(strtolower($thisname), $invited_names))
				{
					$newid = generateRandomString(5);
					while(in_array($newid, $assigned_IDs))
						$newid = generateRandomString(5);
					
					echo '<font color="#095F00">ID Created: ' . $newid . "</font><br />";
					array_push($assigned_IDs, $newid);
					$STH->bindParam(':guest_id', $newid, PDO::PARAM_STR);
					$STH->bindParam(':firstname', $thisfirstname, PDO::PARAM_STR);
					$STH->bindParam(':lastname', $thislastname, PDO::PARAM_STR);
					$STH->bindParam(':additional', $thisadditional, PDO::PARAM_INT);
					$STH->bindParam(':requests', $thisrequests, PDO::PARAM_INT);
					$STH->execute();
					echo '<font color="#095F00">Added <strong>' . $thisname . '</strong> added to database.</font><br />'; 
				} else {
					echo '<font color="#720001"><strong>' . $thisname . '</strong> already in database.</font><br />';
				}
				echo "<br /><script>updateScroll();</script>";
			}
		}
	}
}
?>
