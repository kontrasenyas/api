<?php
	// define('ROOT', 'C:\xampp\htdocs\ArkilaApi\\');
	// require_once(ROOT . "app/api/dbconnect.php");
	// require '../vendor/autoload.php';
	// session_start();
	// $_SESSION['user_id'] = 17010902060769916377;

	// $dropboxKey = '0htsiv2k9w28guf';
	// $dropboxSecret = 'tzzavmqstno5gyd';
	// $appName = 'ArkilaPHv1.0';

	// $appInfo = new Dropbox\AppInfo($dropboxKey, $dropboxSecret);
	// $csrfTokenStore = new Dropbox\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
	// $webAuth = new Dropbox\WebAuth($appInfo, $appName, 'http://localhost/ArkilaAPI/templates/dropbox_finish.php',  $csrfTokenStore);

	// $queryDropboxToken = "SELECT `dropbox_token` FROM `users` WHERE UserId = '17010902060769916377'";
	// $queryDropboxTokenResult = $mysqli->query($queryDropboxToken);	
	// $rowDropboxToken = $queryDropboxTokenResult->fetch_row();

	// list($accessToken) = $webAuth->finish($_GET);

	// $query = "UPDATE `users` SET `dropbox_token` = ? WHERE `users`.`UserId` = '17010902060769916377';";
	// $stmt = $mysqli->prepare($query);
	// $stmt->bind_param("s", $accessToken);

	// $stmt->execute();

	// header('404.phtml');