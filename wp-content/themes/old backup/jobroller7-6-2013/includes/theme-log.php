<?php

class jrLog {  
 
	var $log_file;
	var $fp;
	
	function jrLog() {
		$this->log_file = get_template_directory() . '/log/jobroller_log.txt'; 
		$this->fp = null;
	}
	
	// write message to the log file  
	function write_log($message) {  

		$logging_enabled = get_option('jr_enable_log');
		
		if ($logging_enabled=='yes') :
			// if file pointer doesn't exist, then open log file  
			if (!$this->fp) $this->open_log();  
			// define script name  
			$script_name = basename($_SERVER['PHP_SELF']);  
			$script_name = substr($script_name, 0, -4); 
			// define current time  
			$time = date_i18n('H:i:s');  
			// write current time, script name and message to the log file  
			fwrite($this->fp, "$time ($script_name) $message\n");  
		endif;
	}  
	
	// open log file  
	function open_log() {  
		// define log file path and name  
		$lfile = $this->log_file;  
		// open log file for writing only; place the file pointer at the end of the file  
		// if the file does not exist, attempt to create it  
		$this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");  
	}  
	
	// clear log file
	function clear_log() { 
		$lfile = $this->log_file;  
		$fp = @fopen($lfile, 'w'); 
		@fclose($fp); 
	}
  
}