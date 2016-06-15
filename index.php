<!DOCTYPE html>
<html>
<head>
<title>CyberDrill'16 Disk Page</title>
<style>
.stage {

}

.top-30 {
	margin-top: 50px;
}

td {
	padding: 5px;
}

tr:nth-child(even) {background: #e8e8e8}
tr:nth-child(odd) {background: #d2d2d2}

</style>
</head>
<body>
<?php
date_default_timezone_set("Asia/Kuala_Lumpur");
session_start();

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
$ip_address = get_client_ip();
$time = date('Y-m-d h:i:s',time());
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$user_agent = "";
if (isset($_POST['submit'])) {
	$file_title = htmlentities($_POST['fileTitle']);
	$rand_prefix = hash('crc32b', time()) . substr(md5(basename($_FILES["fileToUpload"]["name"])),10,20);
	$target_dir = "files/";
	$saved_filename = str_rot13(substr($rand_prefix,4,15)) . "_" . basename($_FILES["fileToUpload"]["name"]);
	$saved_filename = str_replace(" ", "_", $saved_filename);
	$target_file = $target_dir . $saved_filename;
	$url_to_file = "http://" . $_SERVER['HTTP_HOST'] . "/files/$saved_filename";
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded. The file is here:<br/>$url_to_file<br/><br/>";
    } else {
        echo "Sorry, there was an error uploading your file.<br/><br/>";
    }

    if (isset($_SESSION['uploaded_file']))  {
		$_SESSION['uploaded_file'] .= "||$time::$file_title::$url_to_file";
	}
	else {
		$_SESSION['uploaded_file'] = "$time::$file_title::$url_to_file";
	}

	//$logfile = fopen("uploaded_log_d7c087.txt", "w") or die("Unable to open file!");
	$txt = "$file_title , $saved_filename , $url_to_file , $time , $ip_address , $user_agent \n ";
	//fwrite($logfile, $txt);
	//fclose($logfile);
	file_put_contents("uploaded_log_d7c087.txt","$txt", FILE_APPEND);
}

?>


<div class="stage">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
    File Title: 
    <input type="text" name="fileTitle" size="35"><br/>
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload"><br/>
    <input type="submit" value="Upload" name="submit">
</form>

<div class="top-30">
<?php
if (isset($_SESSION['uploaded_file']))  {
	$files = explode("||",$_SESSION['uploaded_file']);
	echo "Your uploaded files are:<br/>";
	echo "<table><tr><th>Time</th><th>Title</th><th>URL</th></tr>";
	foreach ($files as $value) {
		// echo $value;
		// echo "<br/>";
		$col = explode("::",$value);
		echo "<tr>";
		echo "<td>" . $col[0] ."</td>";
		echo "<td>" . $col[1] ."</td>";
		echo "<td>" . $col[2] ."</td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<br/>The files will remain on the server till end of this CyberDrill exercise. Please note that you won't be able to see the above list anymore if you clear your browser's cookie";
}

?>
</div>
</div>
</body>
</html>
