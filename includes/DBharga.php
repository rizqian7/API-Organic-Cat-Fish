<?php 

class DBharga
{
	
	// get the connection link
	function __construct() {
		require_once dirname(__FILE__) . '/DBconnect.php';
		$db = new DBconnect;
		$this->con = $db->connect();
	}

	// public function getAllHargaBeli() {
	// 	$stmt = $this->con->prepare("SELECT id_harga, harga_beli, harga_jual FROM tbl_harga");
	// 	$stmt->execute();
	// 	$stmt->bind_result($id_harga, $harga_beli, $harga_jual);
	// 	$prices = array();
	// 	while ($stmt->fetch()) {
	// 		$price = array();
	// 		$price['id_harga']	 = $id_harga;
	// 		$price['harga_beli'] = $harga_beli;
	// 		$price['harga_jual'] = $harga_jual;
	// 		return $price;
	// 	}
	// 	return $prices;
	// }

	public function getHargaBeli() {
		$stmt = $this->con->prepare("SELECT harga_beli FROM tbl_harga");
		$stmt->execute();
		$stmt->bind_result($harga_beli);
		$prices = array();
		while ($stmt->fetch()) {
			$price = array();
			$price['harga_beli'] = $harga_beli;
			return $price;
		}
		return $prices;
	}
}

?>