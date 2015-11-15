<?php
require_once('././scripts/database.php');

class Funds{
	
	// Get List of All Team Members
	public function get_all_funds(){
		$funds = get_all_funds_from_db();
		return $funds;
	}
}


?>