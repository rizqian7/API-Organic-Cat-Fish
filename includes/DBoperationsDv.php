<?php 

class DBoperationsDv
{

	private $con;

	function __construct()
	{
		require_once dirname(__FILE__) . '/DBconnect.php';
		$db = new DBconnect;
		$this->con = $db->connect(); 
	}

	public function userLoginDv($username, $password){
		if($this->isUsernameExistsDv($username)){
			$hashed_password = $this->getUsersPasswordByUsernameDv($username); 
			if(password_verify($password, $hashed_password)){
				return USER_AUTHENTICATED;
			}else{
				return USER_PASSWORD_DO_NOT_MATCH; 
			}
		}else{
			return USER_NOT_FOUND; 
		}
	}

	private function isUsernameExistsDv($username) {
		$stmt = $this->con->prepare("SELECT id_driver FROM tbl_driver WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->store_result();
		return $stmt->num_rows > 0;
	}

	private function getUsersPasswordByUsernameDv($username){
		$stmt = $this->con->prepare("SELECT password FROM tbl_driver WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute(); 
		$stmt->bind_result($password);
		$stmt->fetch(); 
		return $password;
	}

	public function getUserByUsernameDv($username){
		$stmt = $this->con->prepare("SELECT id_driver, no_ktp, nama_lengkap, no_hp, alamat, username FROM tbl_driver WHERE username = ?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$stmt->bind_result($id_driver, $no_ktp, $nama_lengkap, $no_hp, $alamat, $username);
		$stmt->fetch();

		$user = array();
		$user['id_driver'] 			= $id_driver;
		$user['no_ktp'] 			= $no_ktp;
		$user['nama_lengkap'] 		= $nama_lengkap;
		$user['no_hp'] 				= $no_hp;
		$user['alamat'] 			= $alamat;
		$user['username'] 			= $username;
		// $user['status'] 			= $status;
		return $user;
	}

	//update biodata
	public function updateUserDv($no_ktp, $nama_lengkap, $no_hp, $alamat, $username, $id_driver) {
		$stmt = $this->con->prepare("UPDATE tbl_driver SET no_ktp = ?, nama_lengkap = ?, no_hp = ?, alamat = ?,  username =? WHERE id_driver = ? ");
		$stmt->bind_param("sssssi", $no_ktp, $nama_lengkap, $no_hp, $alamat, $username, $id_driver);
		if($stmt->execute())
			return true; 
		return false; 
	}

	//get data panen
	public function getAllPanen($id_driver){
		$stmt = $this->con->prepare("SELECT tbl_panen.id_panen AS id_panen, tbl_peternaklele.id_peternaklele AS id_peternaklele, tbl_panen.waktu_panen AS waktu_panen, tbl_panen.berat_panen AS berat_panen, tbl_panen.jumlah_kolam AS jumlah_kolam, tbl_peternaklele.nama_lengkap AS nama, tbl_peternaklele.no_hp AS no_hp, tbl_panen.jenis_pakan AS jenis_pakan FROM tbl_panen, tbl_peternaklele  WHERE tbl_panen.id_peternak = tbl_peternaklele.id_peternaklele AND tbl_panen.id_driver = $id_driver");
		$stmt->execute();
		$stmt->bind_result($id_panen, $id_peternaklele, $waktu_panen, $berat_panen, $jumlah_kolam, $nama_lengkap, $no_hp, $jenis_pakan);
		$users = array();	
		while($stmt->fetch()){
			$data = array();
			$data['id_panen'] 		= $id_panen;
			$data['id_peternaklele']	= $id_peternaklele;
			$data['waktu_panen'] 	= $waktu_panen;
			$data['berat_panen'] 	= $berat_panen;
			$data['jumlah_kolam'] 	= $jumlah_kolam;
			$data['nama']	= $nama_lengkap;
			$data['no_hp']			= $no_hp;
			$data['jenis_pakan'] 	= $jenis_pakan;
			array_push($users, $data);
		}
		return $users;
	}

	//get data pemesanan
	public function getAllPemesanan($id_driver){
		$stmt = $this->con->prepare("SELECT tbl_pemesanan.id_pemesanan AS id_pemesanan, tbl_rumahmakan.id_rumahmakan AS id_rumahmakan, tbl_pemesanan.waktu_pesan AS waktu_pesan, tbl_pemesanan.berat_pesan AS berat_pesan, tbl_rumahmakan.nama_rumahmakan AS nama_rm, tbl_rumahmakan.no_hp AS no_hp, tbl_pemesanan.jenis_ukuran AS jenis_ukuran FROM tbl_pemesanan, tbl_rumahmakan WHERE tbl_pemesanan.id_rumahmakan = tbl_rumahmakan.id_rumahmakan AND tbl_pemesanan.id_driver = $id_driver");
		$stmt->execute();
		$stmt->bind_result($id_pemesanan, $id_rumahmakan, $waktu_pesan, $berat_pesan, $nama_rm, $no_hp, $jenis_ukuran);
		$users = array();	
		while($stmt->fetch()){
			$data = array();
			$data['id_pemesanan'] 	= $id_pemesanan;
			$data['id_rumahmakan'] 	= $id_rumahmakan;
			$data['waktu_pesan'] 	= $waktu_pesan;
			$data['berat_pesan'] 	= $berat_pesan;
			$data['nama_rm'] 		= $nama_rm;
			$data['no_hp'] 			= $no_hp;
			$data['jenis_ukuran'] 	= $jenis_ukuran;
			array_push($users, $data);
		}
		return $users;
	}
}

?>