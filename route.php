<?php

class Route 
{
	private $_uri = array();
	private $_method = array();	

	public function add($uri, $method = null) {
		$this->_uri[] = '/' . trim($uri, '/');
		
		if ($method != null) {
		  $this->_method[] = $method;
		}
	}

	public function submit() {
	       
		print_r($this->_method);
		echo $uriGetParam = isset($_GET['uri']) ? '/' . $_GET['uri'] : '/';
		
	        foreach($this->_uri as $key => $value) 	
		{
		 if (pref_match("#^$value$#", $uriGetParam))
		  {
			if (is_string($this->_Method[$key])) {
				$useMethod = $this->_method[$key];
				echo $useMethod;
				new $useMethod();
			}	
		  }
		}
	}
}

