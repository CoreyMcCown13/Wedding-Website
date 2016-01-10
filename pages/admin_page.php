<?php
if (isset($_COOKIE["McCownUser"]) && isset($_COOKIE["McCownPass"])) {
	include_once("admin/creds.php");
	if (($_COOKIE["McCownUser"] == hash('md5', $username)) && ($_COOKIE["McCownPass"] == hash('md5', $password . $hash)))
	{
		displayAdminPanel();
	} else { 
		$need_login = true;
	}
} else { 
	$need_login = true;
}
if($need_login)
{
	include_once("admin/login.php");
}

function displayAdminPanel()
{
	include_once("../../includes/sql.inc.php");
	?>
    <h1>Administration Panel</h1>
    <table border="0" style="text-align:center;">
        <tbody>
            <tr>
                <td><a href="#admin" onClick="viewImport()">Invite Guests</td>
                <td><a href="#admin" onClick="viewInvited()">Guests Invited</a></td>
                <td><a href="#admin" onClick="viewAttending()">Guests Attending</a></td>
                <td><a href="#admin" onClick="viewRequests()">Songs Requested</a></td>
                <td><a href="#admin" onClick="viewExport()">Export Data</a></td>
                <td><a href="#" onClick="logout()">Logout</a></td>
            </tr>
        </tbody>
    </table>
    <br />

    <div id="attending-content">
    	<?php displayAttending(); ?>
    </div>
    <div id="invited-content">
		<?php displayInvited(); ?>
    </div>
    <div id="requests-content">
		<?php displayRequests(); ?>
    </div>
    <div id="export-content">
		<?php displayExport(); ?>
    </div>
    <div id="import-content">
    	<?php displayImport(); ?>
    </div>
    <script>
	function hideAll() {
		$("#attending-content").hide();
		$("#invited-content").hide();
		$("#requests-content").hide();
		$("#export-content").hide();
		$("#import-content").hide();		
	}
	function viewAttending() {
		hideAll();
		$("#attending-content").fadeIn("fast");
	}
	function viewInvited() {
		hideAll();
		$("#invited-content").fadeIn("fast");
	}
	function viewRequests() {
		hideAll();
		$("#requests-content").fadeIn("fast");
	}
	function viewExport() {
		hideAll();
		$("#export-content").fadeIn("fast");
	}
	function viewImport() {
		hideAll();
		$("#import-content").fadeIn("fast");
	}
	function logout() {
		setCookie('McCownUser', '', -1);
		setCookie('McCownPass', '', -1);
		switchPage('home_page.html');
	}
	hideAll();
	</script>
    <br />
    <?php
}

function displayAttending() {
	?>
    <h1>Guests Attending</h1>
    <?php
	$db = new PDO("mysql:host=localhost;dbname=mccownwedding;", DB_USER, DB_PASSWORD);	
	$STH = $db->prepare('SELECT * FROM guests WHERE attending = 1');
	$STH->execute();
	# setting the fetch mode
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$total_invitees = 0;
	$total_guests = 0;
	$info_array = array();
	if ($STH->rowCount() > 0)
	{
		while($row = $STH->fetch()) {
			$total_invitees++;
			$total_guests += $row["additional_confirmed"];
			$this_array = array(
				firstname => $row["firstname"],
				lastname => $row["lastname"],
				guest_id => $row["guest_id"],
				additional_confirmed => $row["additional_confirmed"],
				requests_confirmed => $row["requests_confirmed"],
				rsvp_time => $row["rsvp_time"]
			);
			array_push($info_array, $this_array);
		}
	
		?>
        <p style="text-align:center;"><strong>Total Attending: <?php echo ($total_invitees + $total_guests); ?></strong> - <strong>Invitees: <?php echo $total_invitees; ?></strong> - <strong>Guests: <?php echo $total_guests; ?></strong></p>
        <table id="attending-table" class="tablesorter">
            <thead>
                <tr>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">RSVP ID</th>
                    <th scope="col">Additional Attending</th>
                    <th scope="col">Song Requests</th>
                    <th scope="col">RSVP Time</th>
                </tr>
            </thead>
        <tbody>
        <?php
		foreach($info_array as $info)
		{
			?>
            <tr>
  				<td><?php echo $info["firstname"]; ?></td>
                <td><?php echo $info["lastname"]; ?></td>
                <td><?php echo strtoupper($info["guest_id"]); ?></td>
                <td><?php echo $info["additional_confirmed"]; ?></td>
                <td><?php echo $info["requests_confirmed"]; ?></td>
                <td><?php echo $info["rsvp_time"]; ?></td>
            </tr>
            <?php
		}
		?>
        </tbody>
    </table>
    <script>
        $("#attending-table").tablesorter(); 
    </script>
<?php
	} else {
		echo "<p>No records to show.</p>";	
	}
}

function displayInvited() {
	?>
    <h1>Guests Invited</h1>
    <?php
    
	$db = new PDO("mysql:host=localhost;dbname=mccownwedding;", DB_USER, DB_PASSWORD);	
	$STH = $db->prepare('SELECT * FROM guests');
	$STH->execute();
	# setting the fetch mode
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$total_invitees = 0;
	$total_guests = 0;
	$info_array = array();
	if ($STH->rowCount() > 0)
	{
		while($row = $STH->fetch()) {
			$this_array = array(
				firstname => $row["firstname"],
				lastname => $row["lastname"],
				guest_id => $row["guest_id"],
				additional => $row["additional"],
				requests => $row["requests"]
			);
			array_push($info_array, $this_array);
		}
	
		?>
        <table id="invited-table" class="tablesorter">
            <thead>
                <tr>
                	<th scope="col">Remove</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                    <th scope="col">RSVP ID</th>
                    <th scope="col">Additional Allowed</th>
                    <th scope="col">Songs Allowed</th>
                </tr>
            </thead>
        <tbody>
        <?php
		foreach($info_array as $info)
		{
			?>
            <tr>
            	<td><a href="#admin" onClick="deleteInvite('<?php echo $info["firstname"] . " " . $info["lastname"]; ?>', '<?php echo $info["guest_id"]; ?>')"><strong>X</strong></a></td>
  				<td><?php echo $info["firstname"]; ?></td>
                <td><?php echo $info["lastname"]; ?></td>
                <td><?php echo strtoupper($info["guest_id"]); ?></td>
                <td><?php echo $info["additional"]; ?></td>
                <td><?php echo $info["requests"]; ?></td>
            </tr>
            <?php
		}
		?>
        </tbody>
    </table>
    <script>
        $("#invited-table").tablesorter(); 
		function deleteInvite(name, gid) {
			if(confirm("Are you sure that you want to remove " + name + " from the invitation list?"))
			{
				var posting = $.post( 
					"pages/admin/delete.php", 
					{ id : gid } 
				);	
				posting.done(function( data ) {
					alert (name + " has been removed from the guest list.");
				});
			} else {
				return false;	
			}
		}
    </script>
<?php
	} else {
		echo "<p>No records to show.</p>";	
	}
}

function displayRequests() {
	?>
    <h1>Songs Requested</h1>
    <?php
	$db = new PDO("mysql:host=localhost;dbname=mccownwedding;", DB_USER, DB_PASSWORD);	
	$STH = $db->prepare('SELECT * FROM song_requests');
	$STH->execute();
	# setting the fetch mode
	$STH->setFetchMode(PDO::FETCH_ASSOC);
	$total_requests = 0;
	$info_array = array();
	if ($STH->rowCount() > 0)
	{
		while($row = $STH->fetch()) {
			$total_requests++;
			$this_array = array(
				requestor => $row["requestor"],
				title => $row["title"],
				artist => $row["artist"]
			);
			array_push($info_array, $this_array);
		}
	
		?>
        <table id="requests-table" class="tablesorter">
            <thead>
                <tr>
                    <th scope="col">Song Name</th>
                    <th scope="col">Artist</th>
                    <th scope="col">Requested By</th>
                    
                </tr>
            </thead>
        <tbody>
        <?php
		foreach($info_array as $info)
		{
			?>
            <tr>
  				<td><?php echo htmlentities(stripslashes($info["title"])); ?></td>
                <td><?php echo htmlentities(stripslashes($info["artist"])); ?></td>
                <td><?php echo $info["requestor"]; ?></td>
            </tr>
            <?php
		}
		?>
        </tbody>
    </table>
    <script>
        $("#requests-table").tablesorter(); 
    </script>
<?php
	} else {
		echo "<p>No records to show.</p>";	
	}
}

function displayImport() {
	?>
    <h1>Invite Guests</h1>
    <p style="text-align:center;">Invite guests by uploading a .CSV file.</p>
    <center>
        <form method="post" action="pages/admin/import.php" enctype="multipart/form-data">
        <input type="file" name="csv" accept=".csv"><br /><br />
        <input type="submit" value="Import File">
        </form>
    </center>
    <?php
}

function displayExport() {
	?>
    <h1>Export Data</h1>
    <p style="text-align:center;">
    	<a href="pages/admin/export.php?option=invites">Download List of Invited Guests</a><br />
        <a href="pages/admin/export.php?option=attending">Download List of Attending Guests</a><br />
        <a href="pages/admin/export.php?option=songs">Download List of Requested Songs</a>
    </p>
    <?php
}
?>
