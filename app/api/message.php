<?php

$app->get('/api/message/all/{UserId}', function($request, $response) {
	require_once('dbconnect.php');

	$UserId = $request->getAttribute('UserId');

	// $query = "SELECT DISTINCT C.user_two, U2.FirstName, U2.LastName, R.reply FROM `conversation` C 
	// 			INNER JOIN `users` U1 ON C.user_one = U1.UserId or C.user_two = U1.UserId
	// 			INNER JOIN `users` U2 ON C.user_two = U2.UserId 
	// 			INNER JOIN                 
 //                	(
 //                		SELECT Reply FROM `conversation_reply` WHERE user_id_fk = '$UserId' GROUP BY reply order by cr_id desc LIMIT 1
 //                     ) R
	// 			WHERE U1.UserId = '$UserId'";

	$query = "SELECT u.UserId, c.c_id, u.FirstName, u.LastName FROM conversation c, users u 
				WHERE (CASE WHEN c.user_one = '$UserId' THEN c.user_two = u.UserId WHEN c.user_two = '$UserId' THEN c.user_one= u.UserId END ) 
				AND ( c.user_one ='$UserId' OR c.user_two ='$UserId' ) Order by c.c_id DESC Limit 20";
	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) === 0) {
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => 'Error: No message.']));
	}
	else {
		//$data = $result->fetch_all(MYSQLI_ASSOC);
		//header('Content-Type: application/json');
		//echo json_encode($data);
		while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
		{
			$c_id=$row['c_id'];
			$user_id=$row['UserId'];
			$FirstName = $row['FirstName'];
			$LastName = $row['LastName'];

			$cquery= "SELECT R.cr_id,R.time,R.reply FROM conversation_reply R WHERE R.c_id_fk='$c_id' ORDER BY R.cr_id DESC LIMIT 1";
			$result2 = $mysqli->query($cquery);
			$crow=mysqli_fetch_array($result2,MYSQLI_ASSOC);
			$cr_id=$crow['cr_id'];
			$reply=$crow['reply'];
			$time=$crow['time'];

			$data[] = $row + $crow;
		}
		$this->logger->addInfo($UserId . " view all Message", $data );

		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => $data]));
	}
});

$app->post('/api/message/t/{UserId}', function($request, $response) {
	require_once('dbconnect.php');

	$sentTo = $request->getAttribute('UserId');
	$sentFrom = $request->getParsedBody()['CurrentUser'];

	$querycidfk = "SELECT c_id FROM conversation WHERE user_one = '$sentFrom' AND user_two = '$sentTo' LIMIT 1";
	$querycidfkResult = $mysqli->query($querycidfk);
	$rowcidfk = $querycidfkResult->fetch_row();

	$c_id_fk = $rowcidfk[0];

	$queryConversation = "SELECT R.cr_id, R.time, R.reply, U.UserId, U.UserName, U.EmailAddress FROM users U, conversation_reply R WHERE R.user_id_fk = U.UserId AND R.c_id_fk = '$c_id_fk' ORDER BY R.cr_id DESC";

	$result = $mysqli->query($queryConversation);

	if (mysqli_num_rows($result) === 0) {
		echo "Error: No message.";
	}
	else {
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => $data]));
	}
	$this->logger->addInfo($sentFrom . " view Message of " . $sentTo);
});

$app->post('/api/message', function($request, $response) {
	require_once('dbconnect.php');

	$sentTo = $request->getParsedBody()['SentTo'];
	$sentFrom = $request->getParsedBody()['SentFrom'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$msg = $request->getParsedBody()['Message'];



	//Validation
	$errorMsg = "Error:";

	if ($sentTo == null) { $errorMsg .= " sentTo must not be empty."; }
	if ($sentFrom == null) { $errorMsg .= " sentFrom must not be empty."; }
	if ($msg == null) { $errorMsg .= " message must not be empty."; }

	if ($errorMsg == "Error:") {
		$queryValidate = "SELECT c_id FROM `conversation` WHERE (user_one = '$sentFrom' and user_two = '$sentTo') or (user_one = '$sentTo' and user_two = '$sentFrom')";
		$queryValidateResult = $mysqli->query($queryValidate);

		if (mysqli_num_rows($queryValidateResult) == 0) {
			$queryConversation = "INSERT INTO `conversation` (`user_one`, `user_two`, `ip`) VALUES (?, ?, ?)";
			$stmt = $mysqli->prepare($queryConversation);
			$stmt->bind_param("sss", $sentFrom, $sentTo, $ip);

			$stmt->execute();
		}
		//Select the latest conversation
		$querySelectConvesation = "SELECT c_id FROM `conversation` WHERE (`user_one` = '$sentFrom') or (`user_two` = '$sentFrom') ORDER BY c_id DESC limit 1";
		$querySelectConvesationResult = $mysqli->query($querySelectConvesation);
		$rowSelectConvesation = $querySelectConvesationResult->fetch_row();

		$c_id_fk = $rowSelectConvesation[0];

		$queryConversationReply = "INSERT INTO `conversation_reply` (`reply`, `user_id_fk`, `ip`, `c_id_fk`) VALUES (?, ?, ?, ?)";
		$stmt2 = $mysqli->prepare($queryConversationReply);
		$stmt2->bind_param("ssss", $msg, $sentFrom, $ip, $c_id_fk);

		$stmt2->execute();
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => 'success']));
	}
	else {
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => $errorMsg]));
	}
});