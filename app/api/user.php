<?php

use \Firebase\JWT\JWT;

$app->post('/api/user', function($request, $response) {

	$this->logger->addInfo("Registration");
	require_once('dbconnect.php');
	$query = "
		INSERT INTO `users` (`UserId`, `FirstName`, `MiddleName`, `LastName`, `BirthDate`, `Gender`, `EmailAddress`, `UserName`, `Password`, `MobileNo1`, `MobileNo2`, `TelNo`, `ProfilePicturePath`, `Province`, `Municipality`, `Barangay`, `StreetNo`, `CreatedAt`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
	";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("ssssssssssssssssss", $UserId, $FirstName, $MiddleName, $LastName, $BirthDate, $Gender, $EmailAddress, $UserName, $Password, $MobileNo1, $MobileNo2, $TelNo, $ProfilePicturePath, $Province, $Municipality, $Barangay, $StreetNo, $CreatedAt);

	$replace = array("/",":", " ");
	$date = substr(date('Y/m/d h:i:s', time()), 2);
	$milliseconds = substr(round(microtime(true) * 1000), 10);
	$randomDigit = mt_rand(10000, 99999);

	$UserId = str_replace($replace, '', $date . $milliseconds . $randomDigit);
	$FirstName = strtoupper($request->getParsedBody()['FirstName']);
	$MiddleName = strtoupper($request->getParsedBody()['MiddleName']  != null ? $request->getParsedBody()['MiddleName'] : "");
	$LastName = $request->getParsedBody()['LastName'];
	$BirthDate = $request->getParsedBody()['BirthDate'];
	$Gender = $request->getParsedBody()['Gender'];
	$EmailAddress = $request->getParsedBody()['EmailAddress'];
	$UserName = $request->getParsedBody()['UserName'];
	$Password = password_hash($request->getParsedBody()['Password'], PASSWORD_BCRYPT);
	$MobileNo1 = $request->getParsedBody()['MobileNo1'];
	$MobileNo2 = $request->getParsedBody()['MobileNo2'] != null ? $request->getParsedBody()['MobileNo2'] : "";
	$TelNo = $request->getParsedBody()['TelNo'] != null ? $request->getParsedBody()['TelNo'] : "";
	$ProfilePicturePath = $request->getParsedBody()['ProfilePicturePath'] != null ? $request->getParsedBody()['ProfilePicturePath'] : "";
	$Province = $request->getParsedBody()['Province'];
	$Municipality = $request->getParsedBody()['Municipality'];
	$Barangay = $request->getParsedBody()['Barangay'] != null ? $request->getParsedBody()['Barangay'] : "";
	$StreetNo = $request->getParsedBody()['StreetNo'] != null ? $request->getParsedBody()['StreetNo'] : "";
	$CreatedAt = $request->getParsedBody()['CreatedAt'];

	//Validation
	$errorMsg = "Error:";

	$queryUserName = "SELECT COUNT(UserId) FROM `users` WHERE UserName = '$UserName'";
	$queryUserNameResult = $mysqli->query($queryUserName);	
	$rowUserName = $queryUserNameResult->fetch_row();
	if ($rowUserName[0] > 0) { $errorMsg .= " Username already exists."; }

	$queryEmailAddress = "SELECT COUNT(UserId) FROM `users` WHERE EmailAddress = '$EmailAddress'";
	$queryEmailAddressResult = $mysqli->query($queryEmailAddress);	
	$rowEmailAddress = $queryEmailAddressResult->fetch_row();
	if ($rowEmailAddress[0] > 0)  { $errorMsg .= " EmailAddress already exists."; }

	
	if (strlen($UserName) < 8) { $errorMsg .= " UserName must be minimum of 8 characters."; }
	if (strlen($request->getParsedBody()['Password']) < 8) { $errorMsg .= " Password must be minimum of 8 characters."; }
	if (strlen($MobileNo1) != 11) { $errorMsg .= " Mobile Number must be 11 digits."; }
	if (strlen($MobileNo2) != 11) { $errorMsg .= " Alternative Mobile Number must be 11 digits."; }

	if (preg_match('/^[A-Za-z0-9_ -]+$/', $FirstName) == false) { $errorMsg .= " FirstName is invalid."; }
	if (preg_match('/^$|^[A-Za-z0-9_ -]+$/', $MiddleName) == false) { $errorMsg .= " MiddleName is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $LastName) == false) { $errorMsg .= " LastName is invalid."; }
	if (DateTime::createFromFormat("Y-m-d", $BirthDate) == false) {	$errorMsg .= " BirthDate is invalid (Correct format is Y-m-d)."; }
	if (preg_match('/^[MmFf]+$/', $Gender) == false) { $errorMsg .= " Gender must be M or F only.";	}
	if (!filter_var($EmailAddress, FILTER_VALIDATE_EMAIL)) { $errorMsg .= " Invalid EmailAddress.";	}
	if (preg_match('/^[0-9-]+$/', $MobileNo1) == false) { $errorMsg .= " Mobile Number is invalid."; }
	if (preg_match('/^[0-9-]+$/', $MobileNo2) == false) { $errorMsg .= " Alternative Mobile Number is invalid."; }
	if (preg_match('/^$|^[A-Za-z0-9_ -]+$/', $TelNo) == false) { $errorMsg .= " TelNo is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $Province) == false) { $errorMsg .= " Province is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $Municipality) == false) { $errorMsg .= " Municipality is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $Barangay) == false) { $errorMsg .= " Barangay is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $StreetNo) == false) { $errorMsg .= " StreetNo is invalid."; }

	if ($FirstName == null) { $errorMsg .= " FirstName must not be empty."; }
	if ($LastName == null) { $errorMsg .= " LastName must not be empty."; }
	if ($BirthDate == null) { $errorMsg .= " BirthDate must not be empty."; }
	if ($Gender == null) { $errorMsg .= " Gender must not be empty."; }
	if ($EmailAddress == null) { $errorMsg .= " EmailAddress must not be empty."; }
	if ($UserName == null) { $errorMsg .= " UserName must not be empty."; }
	if ($MobileNo1 == null) { $errorMsg .= " MobileNo must not be empty."; }
	if ($Province == null) { $errorMsg .= " Province must not be empty."; }
	if ($Municipality == null) { $errorMsg .= " Municipality must not be empty."; }
	if ($CreatedAt == null) { $CreatedAt .= " CreatedAt must not be empty."; }

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

$app->put('/api/user/{UserId}', function($request, $response) {

	require_once('dbconnect.php');
	$UserId = $request->getAttribute('UserId');
	$query = "UPDATE `users` SET `FirstName` = ?, `MiddleName` = ?, `LastName` = ?, `BirthDate` = ?, `EmailAddress` = ?, `UserName` = ?, `MobileNo1` = ?, `MobileNo2` = ?, `TelNo` = ?, `Province` = ?, `Municipality` = ?, `Barangay` = ?, `StreetNo` = ? WHERE `users`.`UserId` = '$UserId';";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("sssssssssssss", $FirstName, $MiddleName, $LastName, $BirthDate, $EmailAddress, $UserName, $MobileNo1, $MobileNo2, $TelNo, $Province, $Municipality, $Barangay, $StreetNo);

	$FirstName = $request->getParsedBody()['FirstName'];
	$MiddleName = $request->getParsedBody()['MiddleName'];
	$LastName = $request->getParsedBody()['LastName'];
	$BirthDate = $request->getParsedBody()['BirthDate'];
	$EmailAddress = $request->getParsedBody()['EmailAddress'];
	$UserName = $request->getParsedBody()['UserName'];
	$MobileNo1 = $request->getParsedBody()['MobileNo1'];
	$MobileNo2 = $request->getParsedBody()['MobileNo2'];
	$TelNo = $request->getParsedBody()['TelNo'];
	$Province = $request->getParsedBody()['Province'];
	$Municipality = $request->getParsedBody()['Municipality'];
	$Barangay = $request->getParsedBody()['Barangay'];
	$StreetNo = $request->getParsedBody()['StreetNo'];

	//Validation
	$errorMsg = "Error:";

	$queryUserName = "SELECT UserId FROM `users` WHERE UserName = '$UserName'";
	$queryUserNameResult = $mysqli->query($queryUserName);	
	$rowUserName = $queryUserNameResult->fetch_row();
	if ($rowUserName[0] != $UserId) { $errorMsg .= " Username already exists."; }

	$queryEmailAddress = "SELECT UserId FROM `users` WHERE EmailAddress = '$EmailAddress'";
	$queryEmailAddressResult = $mysqli->query($queryEmailAddress);
	$rowEmailAddress = $queryEmailAddressResult->fetch_row();
	if ($rowEmailAddress[0] != $UserId)  { $errorMsg .= " EmailAddress already exists."; }

	if (strlen($UserName) < 8) { $errorMsg .= " UserName must be minimum of 8 characters."; }
	if (strlen($request->getParsedBody()['Password']) < 8) { $errorMsg .= " Password must be minimum of 8 characters."; }
	if (strlen($MobileNo1) != 11) { $errorMsg .= " Mobile Number must be 11 digits."; }
	if (strlen($MobileNo2) != 11) { $errorMsg .= " Alternative Mobile Number must be 11 digits."; }

	if (preg_match('/^[A-Za-z0-9_ -]+$/', $FirstName) == false) { $errorMsg .= " FirstName is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $MiddleName) == false) { $errorMsg .= " MiddleName is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $LastName) == false) { $errorMsg .= " LastName is invalid."; }
	if (DateTime::createFromFormat("Y-m-d", $BirthDate) == false) {	$errorMsg .= " BirthDate is invalid (Correct format is Y-m-d)."; }
	if (preg_match('/^[MmFf]+$/', $Gender) == false) { $errorMsg .= " Gender must be M or F only.";	}
	if (!filter_var($EmailAddress, FILTER_VALIDATE_EMAIL)) { $errorMsg .= " Invalid EmailAddress.";	}
	if (preg_match('/^[0-9-]+$/', $MobileNo1) == false) { $errorMsg .= " Mobile Number is invalid."; }
	if (preg_match('/^[0-9-]+$/', $MobileNo2) == false) { $errorMsg .= " Alternative Mobile Number is invalid."; }
	if (preg_match('/^[0-9-]+$/', $TelNo) == false) { $errorMsg .= " TelNo is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $Province) == false) { $errorMsg .= " Province is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $Municipality) == false) { $errorMsg .= " Municipality is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $Barangay) == false) { $errorMsg .= " Barangay is invalid."; }
	if (preg_match('/^[A-Za-z0-9_ -]+$/', $StreetNo) == false) { $errorMsg .= " StreetNo is invalid."; }

	if ($FirstName == null) { $errorMsg .= " FirstName must not be empty."; }
	if ($LastName == null) { $errorMsg .= " LastName must not be empty."; }
	if ($BirthDate == null) { $errorMsg .= " BirthDate must not be empty."; }
	if ($EmailAddress == null) { $errorMsg .= " EmailAddress must not be empty."; }
	if ($UserName == null) { $errorMsg .= " UserName must not be empty."; }
	if ($MobileNo1 == null) { $errorMsg .= " MobileNo must not be empty."; }
	if ($Province == null) { $errorMsg .= " Province must not be empty."; }
	if ($Municipality == null) { $errorMsg .= " Municipality must not be empty."; }

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

$app->get('/api/user/{UserId}', function($request, $response){
	$this->logger->addInfo("User details");
	require_once('dbconnect.php');
	$UserId = $request->getAttribute('UserId');
	
	$query = "Select UserId, FirstName, MiddleName, LastName, BirthDate, EmailAddress, UserName, MobileNo1, MobileNo2, TelNo, Province, Municipality, Barangay, StreetNo from users where UserId = '$UserId' LIMIT 1";
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	$num_rows = mysqli_num_rows($result);

	if ($num_rows !== 0) {
		$data = $row;
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode([$data]));
	}
	else {
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => 'Error: Invalid user.']));
	}
});

$app->post('/api/login', function($request, $response){
	$this->logger->addInfo("Registration");
	require_once('dbconnect.php');

	$UserName = $request->getParsedBody()['UserName'];
	$Password = $request->getParsedBody()['Password'];

	$queryUserName = "SELECT Count(Username) FROM `users` WHERE Username = '$UserName' LIMIT 1";
	$queryUserNameResult = $mysqli->query($queryUserName);
	$rowUserName = $queryUserNameResult->fetch_row();

	if ($rowUserName[0] > 0) {
		$queryPassword = "SELECT Password FROM `users` WHERE Username = '$UserName' LIMIT 1";
		$queryPasswordResult = $mysqli->query($queryPassword);
		$rowPassword = $queryPasswordResult->fetch_row();

		$isCorrect = $passwordIsCorrect = password_verify($Password, $rowPassword[0]);



		if ($isCorrect == 1) {

			$tokenId    = base64_encode(mcrypt_create_iv(32));
		    $issuedAt   = time();
		    $notBefore  = $issuedAt + 10;             //Adding 10 seconds
		    $expire     = $notBefore + 60;            // Adding 60 seconds
		    $serverName = 'serverName'; // Retrieve the server name from config file

			$secretKey = getenv('JWT_SECRET');

			$data = [
		        'iat'  => $issuedAt,         // Issued at: time when the token was generated
		        'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
		        'iss'  => $serverName,       // Issuer
		        'nbf'  => $notBefore,        // Not before
		        //'exp'  => $expire,           // Expire
		        'data' => [                  // Data related to the signer user
		            //'userId'   => '1', // userid from the users table
		            'userName' => $UserName, // User name
        		]
    		];
			JWT::$leeway = 60; // $leeway in seconds
    		$jwt = JWT::encode(
			        $data,      //Data to be encoded in the JWT
			        $secretKey, // The signing key
			        'HS256'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
	        );
        	//echo $secretKey;
        	$decoded = JWT::decode($jwt, $secretKey, array('HS256'));
        	$array = json_decode(json_encode($decoded), true);

        	//var_dump($array);
        	//echo $array["data"]["userName"];

		    $unencodedArray = ['jwt' => $jwt];
		    //echo $jwt;
		   	return $response->withStatus(200)
        	->withHeader('Content-Type', 'application/json')
        	->write(json_encode(['token' => $jwt]));
		}
		else{			
			return $response->withStatus(200)
        	->withHeader('Content-Type', 'application/json')
        	->write(json_encode(['response' => 'Invalid Username/Password']));
		}
	}
	else{
		return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode(['response' => 'Invalid Username/Password']));
	}
});