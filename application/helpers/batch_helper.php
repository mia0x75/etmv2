<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * Splits an insert query into several sub queries batches
	 * @param  [type] $table 
	 * @param  [type] $keys  
	 * @param  [type] $data  
	 * @return void        
	 */
    function batch($table, $keys, $data) 
    {
    	$ci =& get_instance();
   		$ci->load->database(); 
		// split data in chunks of 50
		$data = array_chunk($data, 50);

		foreach ($data as $row) {
			$values = array();
			foreach ($row as $array) {
		    	array_push($values, "(".implode(',', $array).")");
			}
			$sql = "REPLACE INTO ".$table." (".implode(', ', $keys).") 
				VALUES ".implode(', ', $values);

			$q = $ci->db->query($sql);
		}
    }
?>