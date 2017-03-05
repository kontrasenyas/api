<?php

$app->get('/api/ads', function($request, $response) {
	$this->logger->addInfo("Get Function for ads");
	require_once('dbconnect.php');

	$query = "select * from ads order by DateCreated";
	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) === 0) {
		echo "Error: No ads.";
	}
	else {
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => $data]));
	}
});
$app->get('/api/ads/{UserId}', function($request, $response) {
	$this->logger->addInfo("Get Function for ads of specific user");
	require_once('dbconnect.php');
	$UserId = $request->getAttribute('UserId');
	
	$query = "Select * from ads where UserId = '$UserId'";
	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) === 0) {
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => 'Error: No ads.']));
	}
	else {
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => $data]));
	}
});

$app->post('/api/ads', function($request, $response) {
	$this->logger->addInfo("Post Function for ads");
	require_once('dbconnect.php');
	$this->logger->addInfo("Something interesting happened");
	$query = "INSERT INTO `ads` (`AdsId`, `UserId`, `Title`, `Capacity`, `CarType`, `OtherDetails`) VALUES (?,?,?,?,?,?)";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("sssiss", $AdsId, $UserId, $Title, $Capacity, $CarType, $OtherDetails);

	$replace = array("/",":", " ");
	$date = substr(date('Y/m/d h:i:s', time()), 2);
	$milliseconds = substr(round(microtime(true) * 1000), 10);
	$randomDigit = mt_rand(10000, 99999);

	$AdsId = str_replace($replace, '', $date . $milliseconds . $randomDigit);
	$UserId = $request->getParsedBody()['UserId'];
	$Title = $request->getParsedBody()['Title'];
	$Capacity = $request->getParsedBody()['Capacity'];
	$CarType = $request->getParsedBody()['CarType'];
	$OtherDetails = $request->getParsedBody()['OtherDetails'];

	//Validation
	$errorMsg = "Error:";

	$queryTitle = "SELECT COUNT(AdsId) FROM `ads` WHERE Title = '$Title' AND UserId = '$UserId'";
	$queryTitleResult = $mysqli->query($queryTitle);
	$rowTitle = $queryTitleResult->fetch_row();

	if ($rowTitle[0] > 0) {	$errorMsg .= " You have duplicate post."; }

	if (preg_match('/^[A-Za-z0-9_ -]+$/', $Title) == false) { $errorMsg .= " Title is invalid."; }
	if (preg_match('/^[0-9-]+$/', $Capacity) == false) { $errorMsg .= " Capacity is invalid."; }

	if ($Title == null) { $errorMsg .= " Title must not be empty."; }
	if ($Capacity == null) { $errorMsg .= " Capacity must not be empty."; }


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

$app->post('/api/ads/{AdsId}', function($request, $response) {
	$this->logger->addInfo("Update Function for ads");
	require_once('dbconnect.php');

	$UserId = $request->getParsedBody()['UserId'];
	$AdsId = $request->getAttribute('AdsId');
	$query = "UPDATE `ads` SET `Title` = ?, `Capacity` = ?, `CarType` = ?, `OtherDetails` = ? WHERE `ads`.`AdsId` = '$AdsId' AND `ads`.`UserId` = '$UserId'";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("ssss", $Title, $Capacity, $CarType, $OtherDetails);

	$Title = $request->getParsedBody()['Title'];
	$Capacity = $request->getParsedBody()['Capacity'];
	$CarType = $request->getParsedBody()['CarType'];
	$OtherDetails = $request->getParsedBody()['OtherDetails'];

	//Validation
	$errorMsg = "Error:";

	$queryTitle = "SELECT COUNT(AdsId) FROM `ads` WHERE Title = '$Title' AND UserId = '$UserId'";
	$queryTitleResult = $mysqli->query($queryTitle);
	$rowTitle = $queryTitleResult->fetch_row();

	if ($rowTitle[0] > 0) {	$errorMsg .= " You have duplicate post."; }

	if ($Title == null) { $errorMsg .= " Title must not be empty."; }
	if ($Capacity == null) { $errorMsg .= " Capacity must not be empty."; }

	if (preg_match('/^[A-Za-z0-9_ -]+$/', $Title) == false) { $errorMsg .= " Title is invalid."; }
	if (preg_match('/^[0-9-]+$/', $Capacity) == false) { $errorMsg .= " Capacity is invalid."; }

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

$app->post('/api/ads/delete/', function($request, $response) {
	$this->logger->addInfo("Delete Function for ads");
	require_once('dbconnect.php');

	$AdsId = $request->getParsedBody()['AdsId'];
	$UserId = $request->getParsedBody()['UserId'];
	$query = "UPDATE `ads` SET `IsActive` = b'1' WHERE `ads`.`AdsId` = '$AdsId' AND `ads`.`UserId` = '$UserId'";
	$stmt = $mysqli->prepare($query);

	//Validation
	$errorMsg = "Error:";

	$stmt->execute();
	return $response->withStatus(200)
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode(['response' => 'success']));

});