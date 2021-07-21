<?php 

class DBoperationsRM
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
	public function createUserRM($no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $alamat, $username, $password) {
		if(!$this->isUsernameExistsRM($username))
		{
			$stmt = $this->con->prepare("INSERT INTO tbl_rumahmakan(no_ktp, nama_lengkap, no_hp, nama_rumahmakan, alamat, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param("sssssss", $no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $alamat, $username, $password);

			if($stmt->execute()){
				return USER_CREATED;
			} else {
				return USER_FAILURE;
			}	
		}
		return USER_EXISTS;
	}

	public function userLoginRM($username, $password){
		if($this->isUsernameExistsRM($username)){
			$hashed_password = $this->getUsersPasswordByUsernameRM($username);
			if(password_verify($password, $hashed_password)){
				return USER_AUTHENTICATED;
			} else {
				return USER_PASSWORD_DO_NOT_MATCH; 
			}
		} else {
			return USER_NOT_FOUND; 
		}
	}

	private function getUsersPasswordByUsernameRM($username){
		$stmt = $this->con->prepare("SELECT password FROM tbl_rumahmakan WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute(); 
		$stmt->bind_result($password);
		$stmt->fetch(); 
		return $password;
	}

	private function isUsernameExistsRM($username) {
		$stmt = $this->con->prepare("SELECT id_rumahmakan FROM tbl_rumahmakan WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->store_result();
		return $stmt->num_rows > 0;
	}

	public function getAllUsersRM() {
		$stmt = $this->con->prepare("SELECT id_rumahmakan, no_ktp, nama_lengkap, no_hp, nama_rumahmakan, alamat, username FROM tbl_rumahmakan");
		$stmt->execute();
		$stmt->bind_result($id_rumahmakan, $no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $alamat, $username);
		$users = array();	
		while($stmt->fetch()){
			$user = array();
			$user['id_rumahmakan'] 		= $id_rumahmakan;
			$user['no_ktp'] 			= $no_ktp;
			$user['nama_lengkap'] 		= $nama_lengkap;
			$user['no_hp'] 				= $no_hp;
			$user['nama_rumahmakan'] 	= $nama_rumahmakan;
			$user['alamat'] 			= $alamat;
			$user['username'] 			= $username;
			array_push($users, $user);
		}
		return $users;
	}

	public function updateUserRM($no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $username, $id_rumahmakan) {
		$stmt = $this->con->prepare("UPDATE tbl_rumahmakan SET no_ktp = ?, nama_lengkap = ?, no_hp = ?, nama_rumahmakan = ?,  username =? WHERE id_rumahmakan = ? ");
		$stmt->bind_param("sssssi", $no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $username, $id_rumahmakan);
		if($stmt->execute())
			return true; 
		return false; 
	}

	public function updatePasswordRM($currentPassword, $newPassword, $username) {
		$hashed_password = $this->getUsersPasswordByUsernameRM($username);
		if(password_verify($currentPassword, $hashed_password)){
			$hash_password = password_hash($newPassword, PASSWORD_DEFAULT);
			$stmt = $this->con->prepare("UPDATE tbl_rumahmakan SET password = ? WHERE username = ?");
			$stmt->bind_param("ss", $hash_password, $username);

			if($stmt->execute())
				return PASSWORD_CHANGED;
			return PASSWORD_NOT_CHANGED;
		}else{
			return PASSWORD_DO_NOT_MATCH; 
		}
	}

	public function deleteUser($id) {
		$stmt = $this->con->prepare("DELETE FROM tbl_rumahmakan WHERE id_rumahmakan = ?");
		$stmt->bind_param("i", $id);
		if($stmt->execute())
			return true;
		return false;
	}

	public function getUserByUsernameRM($username){
		$stmt = $this->con->prepare("SELECT id_rumahmakan, no_ktp, nama_lengkap, no_hp, nama_rumahmakan, alamat, username FROM tbl_rumahmakan WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($id_rumahmakan, $no_ktp, $nama_lengkap, $no_hp, $nama_rumahmakan, $alamat, $username);
		$stmt->fetch();
		
		$user = array();
		$user['id_rumahmakan'] 		= $id_rumahmakan;
		$user['no_ktp'] 			= $no_ktp;
		$user['nama_lengkap'] 		= $nama_lengkap;
		$user['no_hp'] 				= $no_hp;
		$user['nama_rumahmakan'] 	= $nama_rumahmakan;
		$user['alamat'] 			= $alamat;
		$user['username'] 			= $username;
		return $user;
	}

	// Penjemputan Ikan Lele
	public function updatePesanan($id_rumahmakan, $waktu_pesan, $berat_pesan, $jenis_ukuran) {
		$stmt = $this->con->prepare("INSERT INTO tbl_pemesanan(id_rumahmakan, waktu_pesan, berat_pesan, jenis_ukuran) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("isis", $id_rumahmakan, $waktu_pesan, $berat_pesan, $jenis_ukuran);

		if($stmt->execute()){
			return TRANSAKSI_SUCCESS;
		} else {
			return TRANSAKSI_FAILED;
		}
	}
}
	
?>