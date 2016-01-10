<?php
	//Used to convert an integer to a word
	//Source: http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
	function convert_number_to_words($number) {
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
		);
		
		if (!is_numeric($number)) {
			return false;
		}
		
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}
	
		if ($number < 0) {
			return $negative . convert_number_to_words(abs($number));
		}
		
		$string = $fraction = null;
		
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}
		
		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . convert_number_to_words($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= convert_number_to_words($remainder);
				}
				break;
		}
		
		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}
		
		return $string;
	}
	
    if($_POST["option"] && $_POST["id"])
    {	
		include("../../includes/sql.inc.php");
		$db = new PDO("mysql:host=localhost;dbname=mccownwedding;", DB_USER, DB_PASSWORD);	
		if(strtolower($_POST["id"]) === "admin")
		{
			include("admin_page.php");
		} else {
        //Receive Guest Information from Database
			if($_POST["option"] == 1)
			{
				$id = strtolower($_POST["id"]);
				$STH = $db->prepare('SELECT * FROM guests WHERE guest_id = :guestid');
				$STH->bindParam(':guestid', $id, PDO::PARAM_INT);
				$STH->execute();
				# setting the fetch mode
				$STH->setFetchMode(PDO::FETCH_ASSOC);
				if ($STH->rowCount() > 0)
				{
					while($row = $STH->fetch()) {
						$real_id = $row['id'];
						$first_name = $row['firstname'];
						$name = $row['firstname'] . " " . $row['lastname'];
						$additional_allowed = $row['additional'];
						$additional_confirmed = $row['additional_confirmed'];
						$requests_allowed = $row['requests'];
						$requests_confirmed = $row['requests_confirmed'];
						if($row['attending'] == 0)
							$confirmed = false;
						else {
							$confirmed = true;
						}
						
						if($row['rsvp_time'] == '0000-00-00 00:00:00')
							$confirmed_time = false;
						else {
							date_default_timezone_set('America/New_York');
							$confirmed_time = new DateTime($row['rsvp_time']);
						}
						?>
						<div class="rsvp-name">
							<form action="pages/rsvp.php" id="rsvp_form">
								<h1>
								<?php
									echo $name;
									if($additional_allowed >= 1)
										echo " + " . $additional_allowed;
								?>
								</h1>
								<p id="rsvp-intro">
								<input type="checkbox" name="attending" value="1" id="attending" <?php if($confirmed){ echo "checked"; }?> /> 
								<?php
								if($additional_allowed >= 1)
								{
									?>
									Will be attending the wedding with
									<select name="additional" id="additional_select">
										<?php
										for($i = 0; $i <= $additional_allowed; $i++)
										{ 
										if($i == 0)
										{ ?>
											<option value="0" <?php if($additional_confirmed == $i) { echo "selected"; }?>>no</option>
										<?php } else { ?>
										<option value="<?php echo $i; ?>" <?php if($additional_confirmed == $i) { echo "selected"; }?>><?php echo convert_number_to_words($i); ?></option>
										<?php }
										} ?>
									</select>
									additional guests.
								<?php } else { ?>
									Will be attending the wedding.
								<?php } ?>
								</p>
								<p><input type="checkbox" name="attending" value="0" id="attending" <?php if(!$confirmed && $confirmed_time){ echo "checked"; }?>/> Will be celebrating at a distance.</p>
								<?php 
								if($confirmed_time){ ?>
									<p><em>You last updated your RSVP information on <?php echo $confirmed_time->format("F jS, Y") . " at " . $confirmed_time->format("g:ia"); ?>.</em></p>
								<?php 
								}
								if($requests_allowed >= 1)
								{ ?>
									<div id="request">
									<h2>Request a Song</h2>
									<p>Please fill out the information below if you would like to request a song to be played during the reception.</p>
									<?php 
									$STB = $db->prepare('SELECT * FROM song_requests WHERE requestor_id = :id');
									$STB->bindParam(':id', $real_id, PDO::PARAM_INT);
									$STB->execute();
									# setting the fetch mode
									$STB->setFetchMode(PDO::FETCH_ASSOC);
									$song_array = array();
									$artist_array = array();
									while($row = $STB->fetch()) {
										array_push($song_array, $row["title"]);
										array_push($artist_array, $row["artist"]);
									}
									for($i = 0; $i < $requests_allowed; $i++)
									{ 
										if(isset($song_array[$i]) && isset($song_array[$i]))
										{
									?>
									<input type="text" name="song[]" maxlength="256" value="<?php echo htmlentities(stripslashes($song_array[$i])); ?>">&nbsp;by
									<input type="text" name="artist[]" maxlenght="256" value="<?php echo htmlentities(stripslashes($artist_array[$i])); ?>"><br />
								<?php } else { ?>
									<input type="text" name="song[]" maxlength="256" placeholder="Song Title">&nbsp;by
									<input type="text" name="artist[]" maxlength="256" placeholder="Artist"><br />
								<?php }
								} echo "</div>";
								} ?>
								<script>
									setCookie("McCownID", "<?php echo $id; ?>", 30);
								</script>
								<br /><br />
								<?php
								if($confirmed){ ?>
									<input type="submit" value="Update RSVP Information">
								<?php } else { ?>
									<input type="submit" value="Submit RSVP Information"><br />
								<?php } ?>
							</form>
							<br /><br />
							<p>Not <?php echo $first_name; ?>? <a href="#rsvp" onclick="forceSwitchPage('rsvp_page.html');deleteCookies();">Go back</a>.</p>
							<br /><br /><br />
						</div>
							<script>
							function checkRequests()
							{
								if($('input:checkbox[id^="attending"]:checked').val() == "1")
								{
									if(document.getElementById("request"))
									{
										$( "#request" ).slideDown("fast");
									}
									if(document.getElementById("additional_select"))
									{
										$("#additional_select").prop("disabled", false);
									}
								} else {
									if(document.getElementById("request"))
									{
										$( "#request" ).slideUp("fast", function(){
											$( "#request" ).hide();
										});
									}
									if(document.getElementById("additional_select"))
									{
										$("#additional_select").prop("disabled", true);
									}
								}
							}
							$(document).ready(function(){
								checkRequests();
							});
							var boxes = $('input:checkbox[id^="attending"]').click(function(){
								boxes.not(this).attr('checked', false);
								checkRequests();
							});
						
								// Detect when form is submitted
								$( "#rsvp_form" ).submit(function( event ) {
									$( "#result" ).hide();
									//$( "#rsvp_landing" ).empty().append("<p>Saving RSVP Data...</p>");
									// Stop form from submitting
									event.preventDefault();
									
									// Get the url and id
									var $form = $( this ),
									attending_data = $form.find( "input[type='checkbox']:checked").val();
									<?php 
									if($additional_allowed >= 1)
									{ ?>
									additional_data = $form.find( "select[name='additional']" ).val(),
									<?php 
									}
									if($requests_allowed >= 1)
									{ ?>
									
									song_data = [];
									$('input[name^="song"]').each(function() {
										song_data.push($(this).val());
									});
									artist_data = [];
									$('input[name^="artist"]').each(function() {
										artist_data.push($(this).val());
									});
									<?php 
									} 
									?>
									url = $form.attr( "action" );
									// Send the data using post
									var posting = $.post( 
										url, 
										{ id : <?php echo $real_id; ?>, attending : attending_data, <?php if($additional_allowed >= 1) { ?> additional : additional_data, <?php } if($requests_allowed >= 1) { ?> 'song[]' : song_data, 'artist[]' : artist_data, <?php } ?> option : 2 } 
									);
									
									// Put the results in a div
									posting.done(function( data ) {
										$( "#result" ).empty().append( data );
										//$( "#rsvp_landing" ).fadeOut("fast");
										$( "#result" ).fadeIn("fast");
									});
								});
								
								$( "#cancel_rsvp" ).submit(function( event ) {
									if (confirm('Are you sure you want to cancel your RSVP? This will remove your name from the wedding guest list and delete any preferences saved on the server.')) {
										$( "#result" ).hide();
										//$( "#rsvp_landing" ).empty().append("<p>Saving RSVP Data...</p>");
										// Stop form from submitting
										event.preventDefault();
										
										// Get the url and id
										var $form = $( this ),
										
										url = $form.attr( "action" );
										// Send the data using post
										var posting = $.post( 
											url, 
											{ id : <?php echo $real_id; ?>, option : 3 } 
										);
										
										// Put the results in a div
										posting.done(function( data ) {
											$( "#result" ).empty().append( data );
											//$( "#rsvp_landing" ).fadeOut("fast");
											$( "#result" ).fadeIn("fast");
										});
									} else { return false; }
								});
							</script>
						<?php		
					}
				} else { ?>
					<h1>Invalid Entry</h1>
					<p>Invalid ID provided. Please ensure you that the ID was typed correctly. If your ID is not working, please contact Corey.</p>
					<script>
					function countdown() {
						var i = document.getElementById('counter');
						if (parseInt(i.innerHTML) <= 1) {
							clearInterval(refresher);
							forceSwitchPage('rsvp_page.html');
						}
						i.innerHTML = parseInt(i.innerHTML)-1;
					}
					var refresher = setInterval(function(){ countdown(); }, 1000);
					
				</script>
				<p>Redirecting to RSVP login page in <span id="counter">4</span> seconds...</p><br /><br />
				<? }
			}
			//Save Guest Information
			if($_POST["option"] == 2)
			{
				try {
					$id = strtolower($_POST["id"]);
					$STH = $db->prepare('SELECT * FROM guests WHERE id = :id');
					$STH->bindParam(':id', $id, PDO::PARAM_INT);
					$STH->execute();
					# setting the fetch mode
					$STH->setFetchMode(PDO::FETCH_ASSOC);
					while($row = $STH->fetch()) {
						$name = $row['firstname'] . " " . $row['lastname'];	
						$additional_allowed = $row['additional'];
						$requests_allowed = $row['requests'];
					}
				} catch (Exception $e) {
					die("An error occurred. Please contact Corey with the following message: " . $e);
				}
				if($_POST["attending"] == 1)
				{
					if(isset($_POST["additional"]) && $_POST["additional"] != "" && $_POST["additional"] != NULL)
						$additional_confirmed = $_POST["additional"];
					else
						$additional_confirmed = 0;
					
					$requests_confirmed = 0;
					if(isset($_POST["song"]))
						foreach($_POST["song"] as $a_song)
							if($a_song != "" &&  $a_song != NULL)
								$requests_confirmed++;
						
					if($additional_confirmed > $additional_allowed)
					{
						$additional_confirmed = $additional_allowed;
						echo "<p>Antihack Measure: Reverted to " . $additional_allowed . " guests confirmed.</p>";
					}
					if($requests_confirmed > $requests_allowed)
					{
						$requests_confirmed = $requests_allowed;
						echo "<p>Antihack Measure: Reverted to " . $requests_allowed . " songs requested.</p>";
					}
					try {
						$STH = $db->prepare('UPDATE guests SET additional_confirmed = :additional_confirmed, requests_confirmed = :requests_confirmed, attending = TRUE, rsvp_time = NOW() WHERE id = :id');
						$STH->bindParam(':additional_confirmed', $additional_confirmed, PDO::PARAM_INT);
						$STH->bindParam(':requests_confirmed', $requests_confirmed, PDO::PARAM_INT);
						$STH->bindParam(':id', $id, PDO::PARAM_INT);
						$STH->execute();
						?>
						<div class="rsvp-name">
							<h1>Thank You</h1>
							<p id="rsvp-intro">
								Thank you for RSVPing to our wedding, we look forward to seeing you there!
							<p>
						<?php
					} catch (Exception $e) {
						die("An error occurred. Please contact Corey with the following message: " . $e);
					}
					if($requests_allowed >= 1)
					{
						?>
						<h2>Song Request</h2>
						<p>You requested the following song<?php if($requests_allowed > 1) echo "s"; ?>:</p>
						<?php
						//Remove previous requests
						$STH = $db->prepare('DELETE FROM song_requests WHERE requestor_id = :id');
						$STH->bindParam(':id', $id, PDO::PARAM_INT);
						$STH->execute();
						
						$songs_requested = false;
						//Insert new requests
						if(isset($_POST["song"]) && $_POST["artist"])
						{
							$songs = $_POST["song"];
							$artists = $_POST["artist"];
							for($i = 0; $i < $requests_allowed; $i++)
							{
								if(($songs[$i] != "" && $artists[$i] != "") && ($songs[$i] != NULL && $artists[$i] != NULL))
								{
									try {
										$STH = $db->prepare("INSERT INTO song_requests (id, requestor, requestor_id, title, artist) VALUES (NULL,  :requestor,  :requestor_id,  :title,  :artist)");
										$STH->bindParam(':requestor', $name, PDO::PARAM_STR);
										$STH->bindParam(':requestor_id', $id, PDO::PARAM_INT);
										$STH->bindParam(':title', $songs[$i], PDO::PARAM_STR);
										$STH->bindParam(':artist', $artists[$i], PDO::PARAM_STR);
										$STH->execute();
										echo("<p><strong>" . htmlentities(stripslashes($songs[$i])) . "</strong> by <strong>" . htmlentities(stripslashes($artists[$i])) . "</strong></p>");
										$songs_requested = true;
									} catch (Exception $e) {
										die("An error occurred. Please contact Corey with the following message: " . $e);
									}
								}
							}
						}
						if(!$songs_requested) echo "<p><em>No songs requested.</em></p>"; 
					} ?>
					<script>
						function countdown() {
							var i = document.getElementById('counter');
							if (parseInt(i.innerHTML) <= 1) {
								clearInterval(refresher);
								switchPage('home_page.html');
							}
							i.innerHTML = parseInt(i.innerHTML)-1;
						}
						var refresher = setInterval(function(){ countdown(); }, 1000);
						
					</script>
					<p>Redirecting to homepage in <span id="counter">6</span> seconds...</p><br /><br /></div>
					<?php 
				} else {
					try {
						$STH = $db->prepare('UPDATE guests SET attending = FALSE, rsvp_time = NOW() WHERE id = :id');
						$STH->bindParam(':id', $id, PDO::PARAM_INT);
						$STH->execute();
						?>
						<div class="rsvp-name">
						<h1>We're Sorry</h1>
						<p id="rsvp-intro">
							Sorry to hear that you cannot make it to our wedding. Your RSVP has been cancelled.
						</p><p>
						If you are able to negotiate a way to come, you may RSVP again using your same unique ID.
						</p>
						<script>
						function countdown() {
							var i = document.getElementById('counter');
							if (parseInt(i.innerHTML) <= 1) {
								clearInterval(refresher);
								switchPage('home_page.html');
							}
							i.innerHTML = parseInt(i.innerHTML)-1;
						}
						var refresher = setInterval(function(){ countdown(); }, 1000);
						
					</script>
					<p>Redirecting to homepage in <span id="counter">6</span> seconds...</p><br /><br />
				 </div>
						<?php
					} catch (Exception $e) {
						die("An error occurred. Please contact Corey with the following message: " . $e);
					}	
				}
			}
		}
    }
?>
