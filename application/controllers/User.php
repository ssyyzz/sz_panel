<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->Auth_model->isUser() && $this->uri->segment(2) != 'login' && $this->uri->segment(2) != 'reg') {
            redirect('user/login');
        }
    }
	
	public function index()
	{
		$mainData = array();
		if (count($_POST)>0) {
			switch ($_POST['action']) {
				case 'add':
					if (!$this->Domain_model->addDomain()){
						$mainData['error'] = 1;
					}
					else {
						redirect('user#spisok');
					}
					break;
				case 'change':
					$this->Domain_model->updDomain();
					break;
				case 'delete':
					$this->Domain_model->delDomain();
					break;
				}
		}
		$mainData['username'] = $this->session->userdata('login');
		$mainData['domains'] = $this->Domain_model->getDomain();
		$mainData['head_domain'] = $this->config->item('head_domain');
		$data = array('content' => 'cab.tpl', 'data' => $mainData);
		$this->load->view('main', $data);
	}
	
	public function logoff()
	{
        $this->session->unset_userdata('login');
		$this->session->unset_userdata('password');
		redirect('user/login');
	}
	
	public function login()
	{
		if ($this->Auth_model->isUser()) redirect('user');
		$mainData = array();
		if (count($_POST)>0) {
			if ($this->Auth_model->loginUser()) {
				redirect('user');
			}
			else {
				$mainData['error'] = 1;
			}
		}
		$data = array('content' => 'login.tpl', 'data' => $mainData);
		$this->load->view('main', $data);
	}
	
	public function reg()
	{
		if ($this->Auth_model->isUser()) redirect('user');
		$mainData = array();
		if (count($_POST)>0) {
			$answer = $this->Auth_model->addUser();
			$mainData['error'] = $answer['error'];
			$mainData['message'] = $answer['message'];
		}
		$data = array('content' => 'reg.tpl', 'data' => $mainData);
		$this->load->view('main', $data);
	}
}
