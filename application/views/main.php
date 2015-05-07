<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$smarty = new Smarty();
if (isset($data)) {
	$smarty->assign('data', $data);
}
if (isset($content)){
	$body = $smarty->fetch($content);
	$smarty->assign('content', $body);
}
$smarty->display('main.tpl');
?>