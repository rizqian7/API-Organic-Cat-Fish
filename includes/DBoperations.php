<?php 

class DBoperations 
{
	private $con;

	// get the connection link
	function __construct() {
		require_once dirname(__FILE__) . '/DBconnect.php';
		$db = new DBconnect;
		$this->con = $db->connect();
	}

	// Create operation
	// Function for insert data to database
	public function createUser($no_ktp, $nama_lengkap, $no_hp, $nama_usaha, $jumlah_kolam, $jumlah_produksi, $alamat, $username, $password) {
		if(!$this->isUsernameExists($username))
		{
			$stmt = $this->con->prepare("INSERT INTO tbl_peternaklele(no_ktp, nama_lengkap, no_hp, nama_usaha, jumlah_kolam, jumlah_produksi, alamat, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param("ssssiisss", $no_ktp, $nama_lengkap, $no_hp, $nama_usaha, $jumlah_kolam, $jumlah_produksi, $alamat, 
				$username, $password);

			if($stmt->execute()){
				return USER_CREATED;
			} else {
				return USER_FAILURE;
			}	
		}
		return USER_EXISTS;
	}

	public function userLogin($username, $password){
		if($this->isUsernameExists($username)){
			$hashed_password = $this->getUsersPasswordByUsername($username);
			if(password_verify($password, $hashed_password)){
				return USER_AUTHENTICATED;
			}else{
				return USER_PASSWORD_DO_NOT_MATCH; 
			}
		}else{
			return USER_NOT_FOUND; 
		}
	}

	private function getUsersPasswordByUsername($username){
		$stmt = $this->con->prepare("SELECT password FROM tbl_peternaklele WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute(); 
		$stmt->bind_result($password);
		$stmt->fetch(); 
		return $password;
	}

	public function getAllUsers() {
		$stmt = $this->con->prepare("SELECT id_peternaklele,no_ktp, nama_lengkap, no_hp, nama_usaha, jumlah_kolam, jumlah_produksi, username FROM tbl_peternaklele");
		$stmt->execute();
		$stmt->bind_result($id_peternaklele, $no_ktp, $nama_lengkap, $no_hp, $nama_usaha, $jumlah_kolam, $jumlah_produksi, $username);
		$users = array();	
		while($stmt->fetch()){
			$user = array();
			$user['id_peternaklele'] 	= $id_peternaklele;
			$user['no_ktp'] 			= $no_ktp;
			$user['nama_lengkap'] 		= $nama_lengkap;
			$user['no_hp'] 				= $no_hp;
			$user['nama_usaha'] 		= $nama_usaha;
			$user['jumlah_kolam'] 		= $jumlah_kolam;
			$user['jumlah_produksi'] 	= $jumlah_produksi;
			$user['username'] 			= $username;
			array_push($users, $user);
		}
		return $users;
	}

	public function updateUser($no_ktp, $nama_lengkap, $no_hp, $nama_usaha, $username, $id_peternaklele) {
		$stmt = $this->con->prepare("UPDATE tbl_peternaklele SET no_ktp = ?, nama_lengkap = ?, no_hp = ?, nama_usaha = ?,  username =? WHERE id_peternaklele = ? ");
		$stmt->bind_param("sssssi", $no_ktp, $nama_lengkap, $no_hp, $nama_usaha, $username, $id_peternaklele);
		if($stmt->execute())
			return true; 
		return false; 
	}

	public function updatePassword($currentPassword, $newPassword, $username) {
		$hashed_password = $this->getUsersPasswordByUsername($username);
		if(password_verify($currentPassword, $hashed_password)){
			$hash_password = password_hash($newPassword, PASSWORD_DEFAULT);
			$stmt = $this->con->prepare("UPDATE tbl_peternaklele SET password = ? WHERE username = ?");
			$stmt->bind_param("ss", $hash_password, $username);

			if($stmt->execute())
				return PASSWORD_CHANGED;
			return PASSWORD_NOT_CHANGED;
		}else{
			return PASSWORD_DO_NOT_MATCH; 
		}
	}

	public function deleteUser($id) {
		$stmt = $this->con->prepare("DELETE FROM tbl_peternaklele WHERE id_peternaklele = ?");
		$stmt->bind_param("i", $id);
		if($stmt->execute())
			return true;
		return false;
	}

	public function getUserByUsername($username){
		$stmt = $this->con->prepare("SELECT id_peternaklele, no_ktp, nama_lengkap, no_hp, nama_usaha, jumlah_kolam, jumlah_produksi,  username FROM tbl_peternaklele WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($id_peternaklele, $no_ktp, $nama_lengkap, $no_hp, $nama_usaha, $jumlah_kolam, $jumlah_produksi,  $username);
		$stmt->fetch();
		
		$user = array();
		$user['id_peternaklele'] 	= $id_peternaklele;
		$user['no_ktp'] 			= $no_ktp;
		$user['nama_lengkap'] 		= $nama_lengkap;
		$user['no_hp'] 				= $no_hp;
		$user['nama_usaha'] 		= $nama_usaha;
		$user['jumlah_kolam'] 		= $jumlah_kolam;
		$user['jumlah_produksi'] 	= $jumlah_produksi;
		$user['username'] 			= $username;
		return $user;
	}

	private function isUsernameExists($username) {
		$stmt = $this->con->prepare("SELECT id_peternaklele FROM tbl_peternaklele WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->store_result();
		return $stmt->num_rows > 0;
	}

	// Penjemputan Ikan Lele
	public function updatePanen($id_peternak, $waktu_panen, $berat_panen, $jumlah_kolam, $jenis_pakan) {
		$stmt = $this->con->prepare("INSERT INTO tbl_panen(id_peternak, waktu_panen, berat_panen, jumlah_kolam, jenis_pakan) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("isiis", $id_peternak, $waktu_panen, $berat_panen, $jumlah_kolam, $jenis_pakan);

		if($stmt->execute()){
			return TRANSAKSI_SUCCESS;
		} else {
			return TRANSAKSI_FAILED;
		}
	}

	public function getMapsLatlng($latitude, $longitude,  $id_peternaklele){
		$stmt = $this->con->prepare("UPDATE tbl_peternaklele SET latitude = ?, longitude = ?, WHERE id_peternaklele = ?");
		$stmt->bind_param("ddi", $latitude, $longitude, $id_peternaklele);

		if($stmt->execute()){
			return true;
		} return false;
	}

	public function getPenjemputanData($id_peternak){
		$stmt = $this->con->prepare("SELECT id_penjemputan, waktu_panen, berat_panen, jumlah_kolam, jenis_pakan, status FROM tbl_penjemputan WHERE id_peternak= $id_peternak");
		$stmt->execute();
		$stmt->bind_result($id_penjemputan, $waktu_panen, $berat_panen, $jumlah_kolam, $jenis_pakan, $status);
		$users = array();	
		while($stmt->fetch()){
			$user = array();
			$user['id_penjemputan'] 	= $id_penjemputan;
			$user['waktu_panen'] 		= $waktu_panen;
			$user['berat_panen'] 		= $berat_panen;
			$user['jumlah_kolam'] 		= $jumlah_kolam;
			$user['jenis_pakan'] 		= $jenis_pakan;
			$user['status'] 			= $status;
			array_push($users, $user);
		}
		return $users;
	}

	public function getAllHistory() {
		$stmt = $this->con->prepare("SELECT id_penjemputan, id_peternak, waktu_panen, berat_panen, jumlah_kolam, jenis_pakan, status FROM tbl_penjemputan");
		$stmt->execute();
		$stmt->bind_result($id_penjemputan, $id_peternak, $waktu_panen, $berat_panen, $jumlah_kolam, $jenis_pakan, $status);
		$users = array();	
		while($stmt->fetch()){
			$user = array();
			$user['id_penjemputan'] 	= $id_penjemputan;
			$user['id_peternak']		= $id_peternak;
			$user['waktu_panen'] 		= $waktu_panen;
			$user['berat_panen'] 		= $berat_panen;
			$user['jumlah_kolam'] 		= $jumlah_kolam;
			$user['jenis_pakan'] 		= $jenis_pakan;
			$user['status'] 			= $status;
			array_push($users, $user);
		}
		return $users;
	}


    // **************************************************************************************************************************

	// public function createUserRM($no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $alamat, $username, $password) {
	// 	if(!$this->isUsernameExists($username))
	// 	{
	// 		$stmt = $this->con->prepare("INSERT INTO tbl_rumahmakan(no_ktp, nama_lengkap, no_hp, nama_rumahmakan, alamat, username, password) VALUES (?, ?, ?, ?, ?, ?, ?");
	// 		$stmt->bind_param("sssssss", $no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $alamat, $username, $password);

	// 		if($stmt->execute()){
	// 			return USER_CREATED;
	// 		} else {
	// 			return USER_FAILURE;
	// 		}	
	// 	}
	// 	return USER_EXISTS;
	// }
	
	//*******************************************************************************************************************************
	

}

?>