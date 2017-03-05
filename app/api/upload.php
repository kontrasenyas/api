<?php

$app->post('/api/upload', function($request, $response) {

	$this->logger->addInfo("Upload");
	require_once('dbconnect.php');

	$query = "INSERT INTO `adsimage` (`AdsId`, `Path`, `CreatedBy`, `CreatedFrom`) VALUES (?, ?, ?, ?)";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("ssss", $AdsId, $Path, $CreatedBy, $CreatedFrom);

	$AdsId = $request->getParsedBody()['AdsId'];
	$Path = $request->getParsedBody()['Path'];
	$CreatedBy = $request->getParsedBody()['CreatedBy'];
	$CreatedFrom = $request->getParsedBody()['CreatedFrom'];
	$base64 = $request->getParsedBody()['Base64'];

	// Validation
	$errorMsg = "Error:";

	if (!check_base64_image($base64)) {
	    $errorMsg .= ' Invalid image!';
	}

	if ($AdsId == null) {
		$errorMsg .= " AdsId is required.";
	}
	if ($Path == null) {
		$errorMsg .= " Path is required.";
	}
	if ($CreatedBy == null) {
		$errorMsg .= " CreatedBy is required.";
	}
	if ($CreatedFrom == null) {
		$errorMsg .= " CreatedFrom is required.";
	}

	if ($errorMsg == "Error:") {
		$stmt->execute();
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => 'success']));
	}

	else{
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => $errorMsg]));
	}

});

function check_base64_image($base64) {

	set_error_handler(function ($no, $msg, $file, $line) {
	    throw new ErrorException($msg, 0, $no, $file, $line);
	});

	$im = base64_decode($base64);

	try {
	    $img = imagecreatefromstring($im);
	    if (!$img) {
        	return false;
    	}

	    imagepng($img, 'tmp.png');
	    $info = getimagesize('tmp.png');

	    unlink('tmp.png');

	    if ($info[0] > 0 && $info[1] > 0 && $info['mime']) {
	        return true;
	    }

	    return false;

	} catch (Exception $e) {
	 	//echo $e->getMessage();
	 	//$this->logger->addInfo($e->getMessage());
	}    
}



//Dropbox

// $app->get('/api/upload', function ($request) {
// 	require_once('dbconnect.php');
// 	session_start();
// 	$_SESSION['user_id'] = 17010902060769916377;

// 	//$UserId = $request->getParsedBody()['UserId'];

// 	$dropboxKey = '0htsiv2k9w28guf';
// 	$dropboxSecret = 'tzzavmqstno5gyd';
// 	$appName = 'ArkilaPHv1.0';

// 	$appInfo = new Dropbox\AppInfo($dropboxKey, $dropboxSecret);
// 	$csrfTokenStore = new Dropbox\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
// 	$webAuth = new Dropbox\WebAuth($appInfo, $appName, getenv("DROPBOX_FINISH"),  $csrfTokenStore);

// 	$queryDropboxToken = "SELECT `dropbox_token` FROM `users` WHERE UserId = '$UserId'";
// 	$queryDropboxTokenResult = $mysqli->query($queryDropboxToken);	
// 	$rowDropboxToken = $queryDropboxTokenResult->fetch_row();

// 	//dropbox_auth.php

// 	if ($rowDropboxToken[0] != "") {
// 		$client = new Dropbox\Client($rowDropboxToken[0], $appName, 'URF-8');

		
// 		try {
// 			$client->getAccountInfo();
// 		} catch (Dropbox\Exception_InvalidAccessToken $e) {
// 			$authUrl = $webAuth->start();
// 			header('Location: ' . $authUrl );
// 			exit();
// 		}
// 	}
// 	else{
// 		$authUrl = $webAuth->start();
// 		header('Location: ' . $authUrl );
// 		exit();
// 	}
// 	var_dump($client->getAccountInfo());

// 	define('ROOT', 'C:\xampp\htdocs\ArkilaApi\\');

// 	$file = fopen(ROOT . "files/safe_image.jpg", 'rb');
// 	$size = filesize(ROOT . "files/safe_image.jpg");

// 	$client->uploadFile('/photo.jpg', Dropbox\Writemode::add(), $file, $size);
// });

// $app->post('/api/upload', 'uploadFIle');

// function uploadFile () {
//     if (!isset($_FILES['uploads'])) {
//         echo "No files uploaded!!";
//         return;
//     }
//     $imgs = array();

//     $files = $_FILES['uploads'];
//     $cnt = count($files['name']);

//     for($i = 0 ; $i < $cnt ; $i++) {
//         if ($files['error'][$i] === 0) {
//             $name = uniqid('img-'.date('Ymd').'-');
//             if (move_uploaded_file($files['tmp_name'][$i], 'uploads/' . $name) === true) {
//                 $imgs[] = array('url' => '/uploads/' . $name, 'name' => $files['name'][$i]);
//             }

//         }
//     }

//     $imageCount = count($imgs);

//     if ($imageCount == 0) {
//        echo 'No files uploaded!!  <p><a href="/">Try again</a>';
//        return;
//     }

//     $plural = ($imageCount == 1) ? '' : 's';

//     foreach($imgs as $img) {
//         printf('%s <img src="%s" width="50" height="50" /><br/>', $img['name'], $img['url']);
//     }
// }

