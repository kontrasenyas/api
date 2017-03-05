<?php

$app->post('/api/booking', function($request, $response) {
	require_once('dbconnect.php');

	$query = "INSERT INTO `bookings` (`BookingId`, `UserId`, `AdsId`, `CreatedFrom`) VALUES (?, ?, ?, ?)";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("ssss", $BookingId, $UserId, $AdsId, $CreatedFrom);

	$replace = array("/",":", " ");
	$date = substr(date('Y/m/d h:i:s', time()), 2);
	$milliseconds = substr(round(microtime(true) * 1000), 10);
	$randomDigit = mt_rand(10000, 99999);

	$BookingId = str_replace($replace, '', $date . $milliseconds . $randomDigit);
	$UserId = $request->getParsedBody()['UserId'];
	$AdsId = $request->getParsedBody()['AdsId'];
	$CreatedFrom = $request->getParsedBody()['CreatedFrom'];

	//Validation
	$errorMsg = "Error:";

	if ($UserId == null) { $errorMsg .= " UserId must not be empty."; }
	if ($AdsId == null) { $errorMsg .= " AdsId must not be empty."; }
	if ($CreatedFrom == null) { $errorMsg .= " CreatedFrom must not be empty."; }

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

	$this->logger->addInfo("Add Booking");
});

$app->get('/api/booking/{UserId}', function($request, $response) {
	$this->logger->addInfo("Get Booking");
	require_once('dbconnect.php');

	$UserId = $request->getAttribute('UserId');

	$query = "SELECT * FROM `bookings` 
				INNER JOIN `ads` ON bookings.AdsId = ads.AdsId
				INNER JOIN `users` ON ads.UserId = users.UserId
				WHERE bookings.UserId = $UserId";
	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) === 0) {
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => 'Error: No bookings.']));

	}
	else {
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => $data]));
	}
});

$app->put('/api/booking/{BookingId}', function($request) {
	$this->logger->addInfo("Update Booking");
	require_once('dbconnect.php');

	$BookingId = $request->getAttribute('BookingId');
	$UserId = $request->getParsedBody()['UserId'];
	$WhoIsUser = $request->getParsedBody()['WhoIsUser'];
	$Status = $request->getParsedBody()['Status'];

	if ($WhoIsUser == 'Owner') {
		$query = "UPDATE `bookings` SET `Status` = ?, `Remarks` = 'Updated by $WhoIsUser', `IsReadUser` = 0  WHERE `BookingId` = $BookingId";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("s", $Status);
		$stmt->execute();
	}
	else {
		$query = "UPDATE `bookings` SET `Status` = ?, `Remarks` = 'Updated by $WhoIsUser', `IsReadOwner` = 0  WHERE `BookingId` = $BookingId AND `UserId` = $UserId";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("s", $Status);
		$stmt->execute();
	}
});