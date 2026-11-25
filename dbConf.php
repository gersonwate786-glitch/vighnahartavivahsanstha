<?php
error_reporting(0);
//error_reporting( E_ALL );
mysqli_report(MYSQLI_REPORT_OFF);

if (file_exists(__DIR__ . '/dbConf.live.php')) {
	require_once __DIR__ . '/dbConf.live.php';
} else {
	$httpHost = isset($_SERVER['HTTP_HOST']) ? strtolower(explode(':', $_SERVER['HTTP_HOST'])[0]) : '';
	$isLocal = ($httpHost === 'localhost' || $httpHost === '127.0.0.1');

	if ($isLocal) {
		define('DB_HOST', 'localhost');
		define('DB_USER', 'root');
		define('DB_PASSWORD', '');
		define('DB_DATABASE', 'matrimony_local');
	} else {
		define('DB_HOST', 'localhost');
		define('DB_USER', 'u548077475_vighnaharta1');
		define('DB_PASSWORD', 'Sima@1806#');
		define('DB_DATABASE', 'u548077475_vighnaharta');
	}
}

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
if ($db) {
	$connection = mysqli_select_db($db, DB_DATABASE);
} else {
	$connection = false;
}
?>
<?php date_default_timezone_set("Asia/Kolkata");?>
<?php 
    if (!isset($_SESSION['lang'])){
		$_SESSION['lang'] = "en_main";
	}else if (isset($_GET['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])) {
		if ($_GET['lang'] == "en"){
			$_SESSION['lang'] = "en";
            header("Location:index.php");
		}else if ($_GET['lang'] == "hi"){
			$_SESSION['lang'] = "hi";
            header("Location:index.php");
		}else if ($_GET['lang'] == "te"){
			$_SESSION['lang'] = "te";
            header("Location:index.php");
		}else if ($_GET['lang'] == "mr"){
			$_SESSION['lang'] = "mr";
            header("Location:index.php");
		}
		else if ($_GET['lang'] == "ta"){
			$_SESSION['lang'] = "ta";
            header("Location:index.php");
		}
        else if ($_GET['lang'] == "kn"){
			$_SESSION['lang'] = "kn";
            header("Location:index.php");
		}
	}
	//echo 'choose language:-'.$_SESSION['lang'];
	require_once "languages/" . $_SESSION['lang'] . ".php";
?>