<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../includes/DBoperations.php';
require '../includes/DBoperationsRM.php';
require '../includes/DBoperationsDv.php';
require '../includes/DBharga.php';

$app = new \Slim\App([
	'settings' => [
		'displayErrorDetails' => true
	]
]);

// User Registration
// Peternak Lele
$app->post('/createuser', function(Request $request, Response $response) {
	if (!haveEmptyParameters(array('no_ktp', 'nama_lengkap', 'no_hp', 'nama_usaha', 'jumlah_kolam', 'jumlah_produksi', 'alamat', 
		'username', 'password'), $request, $response)) {
		
		$request_data = $request->getParsedBody();

		$no_ktp 			= $request_data['no_ktp'];
		$nama_lengkap 		= $request_data['nama_lengkap'];
		$no_hp 				= $request_data['no_hp'];
		$nama_usaha 		= $request_data['nama_usaha'];
		$jumlah_kolam 		= $request_data['jumlah_kolam'];
		$jumlah_produksi 	= $request_data['jumlah_produksi'];
		$alamat 			= $request_data['alamat'];
		$username 			= $request_data['username'];
		$password			= $request_data['password'];

		$hash_password = password_hash($password, PASSWORD_DEFAULT);

		$db = new DBoperations;

		$result = $db->createUser($no_ktp, $nama_lengkap, $no_hp, $nama_usaha, $jumlah_kolam, $jumlah_produksi, $alamat, $username, $hash_password);
		if ($result == USER_CREATED) {

			$message = array();
			$message['error'] = false;
			$message['message'] = "User created successfully";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(201);

		} else if ($result == USER_FAILURE) {

			$message = array();
			$message['error'] = true;
			$message['message'] = "Some error occured";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(422);

		} else if ($result == USER_EXISTS) {

			$message = array();
			$message['error'] = true;
			$message['message'] = "User already exists";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(422);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});
//Rumah Makan
$app->post('/createuserrm', function(Request $request, Response $response) {
	if (!haveEmptyParameters(array('no_ktp', 'nama_lengkap', 'no_hp', 'nama_rumahmakan', 'alamat', 'username', 'password'), $request, $response)) {
		
		$request_data = $request->getParsedBody();

		$no_ktp 			= $request_data['no_ktp'];
		$nama_lengkap 		= $request_data['nama_lengkap'];
		$no_hp 				= $request_data['no_hp'];
		$nama_rumahmakan	= $request_data['nama_rumahmakan'];
		$alamat 			= $request_data['alamat'];
		$username 			= $request_data['username'];
		$password			= $request_data['password'];

		$hash_password = password_hash($password, PASSWORD_DEFAULT);

		$db = new DBoperationsRM;

		$result = $db->createUserRM($no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $alamat, $username, $hash_password);
		if ($result == USER_CREATED) {

			$message = array();
			$message['error'] = false;
			$message['message'] = "User created successfully";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(201);

		} else if ($result == USER_FAILURE) {

			$message = array();
			$message['error'] = true;
			$message['message'] = "Some error occured";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(422);

		} else if ($result == USER_EXISTS) {

			$message = array();
			$message['error'] = true;
			$message['message'] = "User already exists";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(422);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});

// User Login
// Peternak Lele
$app->post('/userlogin', function(Request $request, Response $response) {
	if (!haveEmptyParameters(array('username', 'password'), $request, $response)){
		$request_data 	= $request->getParsedBody();
		$username 		= $request_data['username'];
		$password		= $request_data['password'];

		$db 	= new DBoperations;
		$result = $db->userLogin($username, $password);

		if($result == USER_AUTHENTICATED) {
			$user 	= $db->getUserByUsername($username);
			
			$response_data = array();
			$response_data['error'] 	= false;
			$response_data['message'] 	= "Login Successful";
			$response_data['user']		= $user;

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if($result == USER_NOT_FOUND){
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= "User not exist";

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if($result == USER_PASSWORD_DO_NOT_MATCH) {
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= "Password Salah";

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});
// Rumah Makan
$app->post('/userloginrm', function(Request $request, Response $response) {
	if (!haveEmptyParameters(array('username', 'password'), $request, $response)){
		$request_data 	= $request->getParsedBody();
		$username 		= $request_data['username'];
		$password		= $request_data['password'];

		$db 	= new DBoperationsRM;
		$result = $db->userLoginRM($username, $password);

		if($result == USER_AUTHENTICATED) {
			$user 	= $db->getUserByUsernameRM($username);
			
			$response_data = array();
			$response_data['error'] 	= false;
			$response_data['message'] 	= "Login Successful";
			$response_data['user']		= $user;

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if($result == USER_NOT_FOUND){
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= "User not exist";

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if($result == USER_PASSWORD_DO_NOT_MATCH) {
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= "Password Salah";

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});
//Driver
$app->post('/userlogindv', function(Request $request, Response $response) {
	if (!haveEmptyParameters(array('username', 'password'), $request, $response)){
		$request_data 	= $request->getParsedBody();
		$username 		= $request_data['username'];
		$password		= $request_data['password'];

		$db 	= new DBoperationsDv;
		$result = $db->userLoginDv($username, $password);

		if($result == USER_AUTHENTICATED) {
			$user 	= $db->getUserByUsernameDv($username);
			
			$response_data = array();
			$response_data['error'] 	= false;
			$response_data['message'] 	= "Login Successful";
			$response_data['user']		= $user;

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if($result == USER_NOT_FOUND){
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= "User not exist";

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if($result == USER_PASSWORD_DO_NOT_MATCH) {
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= "Password Salah";

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});


// Get All User
// Peternak Lele
$app->get('/allusers', function(Request $request, Response $response) {
	$db 	= new DBoperations;
	$users 	= $db->getAllUsers();
	
	$response_data = array();
	$response_data['error'] = false;
	$response_data['users']	= $users;
	$response->write(json_encode($response_data));

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(200);
});
// Rumah Makan
$app->get('/allusersrm', function(Request $request, Response $response) {
	$db 	= new DBoperationsRM;
	$users 	= $db->getAllUsersRM();
	
	$response_data = array();
	$response_data['error'] = false;
	$response_data['users']	= $users;
	$response->write(json_encode($response_data));

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(200);
});

// Update User
// Peternak Lele
$app->put('/updateuser/{id}', function(Request $request, Response $response, array $args){
	$id = $args['id'];

	if (!haveEmptyParameters(array('no_ktp', 'nama_lengkap', 'no_hp', 'nama_usaha', 'username'), $request, $response)) {

		$request_data = $request->getParsedBody();

		$no_ktp 			= $request_data['no_ktp'];
		$nama_lengkap 		= $request_data['nama_lengkap'];
		$no_hp 				= $request_data['no_hp'];
		$nama_usaha 		= $request_data['nama_usaha'];
		$username 			= $request_data['username'];

		$db = new DBoperations;

		if($db->updateUser($no_ktp, $nama_lengkap, $no_hp, $nama_usaha, $username, $id)) {
			
			$user = $db->getUserByUsername($username);
			$response_data = array();
			$response_data['error'] 	= false;
			$response_data['message'] 	= 'User updated successfully';
			$response_data['user'] 		= $user;

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else {
			$user = $db->getUserByUsername($username);
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= 'Please try again later';
			$response_data['user'] 		= $user;

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});
// Rumah Makan
$app->put('/updateuserrm/{id}', function(Request $request, Response $response, array $args){
	$id = $args['id'];

	if (!haveEmptyParameters(array('no_ktp', 'nama_lengkap', 'no_hp', 'nama_rumahmakan', 'username'), $request, $response)) {

		$request_data = $request->getParsedBody();

		$no_ktp 			= $request_data['no_ktp'];
		$nama_lengkap 		= $request_data['nama_lengkap'];
		$no_hp 				= $request_data['no_hp'];
		$nama_rumahmakan 	= $request_data['nama_rumahmakan'];
		$username 			= $request_data['username'];

		$db = new DBoperationsRM;

		if($db->updateUserRM($no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $username, $id)) {
			
			$user = $db->getUserByUsernameRM($username);
			$response_data = array();
			$response_data['error'] 	= false;
			$response_data['message'] 	= 'User updated successfully';
			$response_data['user'] 		= $user;

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else {
			$user = $db->getUserByUsernameRM($username);
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= 'Please try again later';
			$response_data['user'] 		= $user;

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});
// Driver
$app->put('/updateuserdv/{id}', function(Request $request, Response $response, array $args){
	$id = $args['id'];

	if (!haveEmptyParameters(array('no_ktp', 'nama_lengkap', 'no_hp', 'alamat', 'username'), $request, $response)) {
		$request_data = $request->getParsedBody();

		$no_ktp 		= $request_data['no_ktp'];
		$nama_lengkap 	= $request_data['nama_lengkap'];
		$no_hp 			= $request_data['no_hp'];
		$alamat 		= $request_data['alamat'];
		$username 		= $request_data['username'];

		$db = new DBoperationsDv;

		if($db->updateUserDv($no_ktp, $nama_lengkap, $no_hp, $alamt, $username, $id)) {
			
			$user = $db->getUserByUsernameDv($username);
			$response_data = array();
			$response_data['error'] 	= false;
			$response_data['message'] 	= 'User updated successfully';
			$response_data['user'] 		= $user;

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else {
			$user = $db->getUserByUsernameDv($username);
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= 'Please try again later';
			$response_data['user'] 		= $user;

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});
// Update Password
// Peternak Lele
$app->put('/updatepassword', function(Request $request, Response $response) {

	if (!haveEmptyParameters(array('currentPassword', 'newPassword', 'username'), $request, $response)){
		$request_data 		= $request->getParsedBody();
		$currentPassword 	= $request_data['currentPassword'];
		$newPassword		= $request_data['newPassword'];
		$username 			= $request_data['username'];

		$db = new DBoperations;
		$result = $db->updatePassword($currentPassword, $newPassword, $username);

		if($result == PASSWORD_CHANGED) {
			$response_data = array();
			$response_data['error'] 	= false;
			$response_data['message']	= 'Password Changed';
			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if ($result == PASSWORD_DO_NOT_MATCH) {
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message']	= 'Password Not Same';
			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if ($result == PASSWORD_NOT_CHANGED) {
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message']	= 'Password Not Changed';
			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		}
	}

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});
// Rumah makan
$app->put('/updatepasswordrm', function(Request $request, Response $response) {

	if (!haveEmptyParameters(array('currentPassword', 'newPassword', 'username'), $request, $response)){
		$request_data 		= $request->getParsedBody();
		$currentPassword 	= $request_data['currentPassword'];
		$newPassword		= $request_data['newPassword'];
		$username 			= $request_data['username'];

		$db = new DBoperationsRM;
		$result = $db->updatePasswordRM($currentPassword, $newPassword, $username);

		if($result == PASSWORD_CHANGED) {
			$response_data = array();
			$response_data['error'] 	= false;
			$response_data['message']	= 'Password Changed';
			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if ($result == PASSWORD_DO_NOT_MATCH) {
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message']	= 'Password Not Same';
			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else if ($result == PASSWORD_NOT_CHANGED) {
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message']	= 'Password Not Changed';
			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		}
	}

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});

// Delete User
$app->delete('/deleteuser/{id}', function(Request $request, Response $response, array $args){
	$id = $args['id'];
	$db = new DBoperations;
	$response_data = array();

	if($db->deleteUser($id)) {
		$response_data['error'] 	= false;
		$response_data['message']	= 'User has been deleted';
	} else {
		$response_data['error'] 	= true;
		$response_data['message']	= 'Please try again later';
	}
	$response->write(json_encode($response_data));

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(200);
});

// Update Panen
// PeternakLele
$app->post('/updatepanen', function(Request $request, Response $response) {

	if (!haveEmptyParameters(array('id_peternak', 'waktu_panen', 'berat_panen', 'jumlah_kolam','jenis_pakan'), $request, $response)) {
		
		$request_data = $request->getParsedBody();

		$id_peternak 	= $request_data['id_peternak'];
		$waktu_panen 	= $request_data['waktu_panen'];
		$berat_panen 	= $request_data['berat_panen'];
		$jumlah_kolam 	= $request_data['jumlah_kolam'];
		$jenis_pakan 	= $request_data['jenis_pakan'];

		$db = new DBoperations;

		$result = $db->updatePanen($id_peternak, $waktu_panen, $berat_panen, $jumlah_kolam, $jenis_pakan);
		if ($result == TRANSAKSI_SUCCESS) {

			$message = array();
			$message['error'] = false;
			$message['message'] = "Success";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);

		} else if ($result == TRANSAKSI_FAILED) {

			$message = array();
			$message['error'] = true;
			$message['message'] = "Some error occured";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(422);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});

// Rumah Makan
$app->post('/updatepemesanan', function(Request $request, Response $response) {

	if (!haveEmptyParameters(array('id_rumahmakan', 'waktu_pesan', 'berat_pesan', 'jenis_ukuran'), $request, $response)) {
		
		$request_data = $request->getParsedBody();

		$id_rumahmakan 	= $request_data['id_rumahmakan'];
		$waktu_pesan 	= $request_data['waktu_pesan'];
		$berat_pesan 	= $request_data['berat_pesan'];
		$jenis_ukuran 	= $request_data['jenis_ukuran'];

		$db = new DBoperationsRM;

		$result = $db->updatePesanan($id_rumahmakan, $waktu_pesan, $berat_pesan, $jenis_ukuran);
		if ($result == TRANSAKSI_SUCCESS) {

			$message = array();
			$message['error'] = false;
			$message['message'] = "Success";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);

		} else if ($result == TRANSAKSI_FAILED) {

			$message = array();
			$message['error'] = true;
			$message['message'] = "Some error occured";

			$response->write(json_encode($message));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(422);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});

// // Get Harga Ikan
// $app->get('/getAllHargaBeli', function(Request $request, Response $response){
// 	$db 	= new DBharga;
// 	$harga 	= $db->getAllHargaBeli();

// 	$response_data = array();
// 	$response_data['error'] = false;
// 	$response_data['harga'] = $harga;
// 	$response->write(json_encode($response_data));

// 	return $response->withHeader('Content-type', 'application/json')
// 					->withStatus(200);
// });

// Get Harga Beli Ikan
$app->get('/getHargaBeli', function(Request $request, Response $response){
	$db 	= new DBharga;
	$harga 	= $db->getHargaBeli();

	$response_data 	= array();
	$response_data['error'] = false;
	$response_data['harga'] = $harga;
	$response->write(json_encode($response_data));

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(200);
});


// Get Maps Latitude Longitude
$app->put('/getmapslatlng/{id}', function(Request $request, Response $response, array $args){
	$id = $args['id'];

	if (!haveEmptyParameters(array('latitude', 'longitude'), $request, $response)) {

		$request_data = $request->getParsedBody();

		$latitude  	= $request_data['latitude'];
		$longitude 	= $request_data['longitude'];

		$db = new DBoperations;

		if($db->getmapslatlng($latitude, $longitude, $id)) {
			
			$response_data = array();
			$response_data['error'] 	= false;
			$response_data['message'] 	= 'Alamat Tersimpan';

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		} else {
			$response_data = array();
			$response_data['error'] 	= true;
			$response_data['message'] 	= 'Please try again later';

			$response->write(json_encode($response_data));

			return $response->withHeader('Content-type', 'application/json')
							->withStatus(200);
		}
	}
	return $response->withHeader('Content-type', 'application/json')
					->withStatus(422);
});

// Get Penjemputan data
$app->get('/getpenjemputan/{id}', function(Request $request, Response $response, array $args){
	$id 	= $args['id'];

	$db 	= new DBoperations;

	$users 	= $db->getpenjemputanData($id);
	
	$response_data = array();
	$response_data['error'] = false;
	$response_data['users']	= $users;
	$response->write(json_encode($response_data));

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(200);
});

$app->get('/allhistory', function(Request $request, Response $response) {
	$db 	= new DBoperations;
	$users 	= $db->getAllHistory();
	
	$response_data = array();
	$response_data['error'] = false;
	$response_data['users']	= $users;
	$response->write(json_encode($response_data));

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(200);
});

// Driver get data
// get data panen
$app->get('/getallpanen/{id}', function(Request $request, Response $response, array $args) {
	$id 	= $args['id'];
	$db 	= new DBoperationsDv;
	$users 	= $db->getAllPanen($id);
	
	$response_data = array();
	$response_data['error'] = false;
	$response_data['users']	= $users;
	$response->write(json_encode($response_data));

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(200);
});
// Get Data pemesanan
$app->get('/getallpemesanan/{id}', function(Request $request, Response $response, array $args) {
	$id 	= $args['id'];
	$db 	= new DBoperationsDv;
	$users 	= $db->getAllPemesanan($id);
	
	$response_data = array();
	$response_data['error'] = false;
	$response_data['users']	= $users;
	$response->write(json_encode($response_data));

	return $response->withHeader('Content-type', 'application/json')
					->withStatus(200);
});

function haveEmptyParameters($required_params, $request, $response) {
	$error = false;
	$error_params = '';
	$request_params = $request->getParsedBody();

	foreach($required_params as $param)
	{
		if (!isset($request_params[$param]) || strlen($request_params[$param])<=0)
		{
			$error = true;
			$error_params .= $param . ', ';
		}
	}

	if ($error) {
		$error_detail = array();
		$error_detail['error'] = true;
		$error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
		$response->write(json_encode($error_detail));
	}
	return $error;
}


$app->run();