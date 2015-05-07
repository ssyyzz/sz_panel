<?php
require_once "Smarty/Smarty.class.php";
class templates extends Smarty {
	function __construct() {
		parent::__construct();
		$this->template_dir = "templates/";
		$this->compile_dir  = "templates_c/";
	}
}
?>