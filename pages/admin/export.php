<?php
if (isset($_COOKIE["McCownUser"]) && isset($_COOKIE["McCownPass"])) {
	include_once("creds.php");
	if (($_COOKIE["McCownUser"] == hash('md5', $username)) && ($_COOKIE["McCownPass"] == hash('md5', $password . $hash)))
	{
		downloadProceed();
	} else $need_login = true;
} else $need_login = true;

if($need_login)
	die("Forbidden");

function mysqli_field_name($result, $field_offset)
{
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}
function downloadProceed() {
	if(isset($_GET["option"]) && $_GET["option"] != "")
	{
	$option = $_GET["option"];
	if($option == "songs")
		$query = 'SELECT title, artist, requestor FROM song_requests';
	elseif($option == "invites")
		$query = 'SELECT firstname, lastname, guest_id, additional, requests FROM guests';
	elseif($option == "attending")
		$query = 'SELECT firstname, lastname, guest_id, additional, additional_confirmed, requests, requests_confirmed, rsvp_time FROM guests WHERE attending = 1';
	else
		die("Invalid option provided.");
		
	include_once("../../../includes/sql.inc.php");
	$db = new mysqli("localhost", DB_USER, DB_PASSWORD, "mccownwedding");
    $result = $db->query($query);
    if (!$result) die('Couldn\'t fetch records');
    $num_fields = mysqli_num_fields($result);
    $headers = array();
    for ($i = 0; $i < $num_fields; $i++) {
        $headers[] = mysqli_field_name($result , $i);
    }
    $fp = fopen('php://output', 'w');
    if ($fp && $result) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $option . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        fputcsv($fp, $headers);
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            fputcsv($fp, array_values($row));
        }
        die;
    }
	}
}
?>
