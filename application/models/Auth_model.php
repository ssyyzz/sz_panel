<?php

class Auth_model extends CI_Model
{
    function isUser()
    {
        $this->db->where('login', $this->session->userdata('login'));
        $this->db->where('password', $this->session->userdata('password'));
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function loginUser()
    {
		$session_data = array('login' => $_POST['email'], 'password' => md5($_POST['password']));
        $this->session->set_userdata($session_data);
        return $this->isUser();
    }
    
	function addUser()
    {
		if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$return = array('error' => 1, 'message' => 'Неверный формат Email');
		}
		else {
			$this->db->where('login', $_POST['email']);
			$query = $this->db->get('users');
			if ($query->num_rows() > 0) {
				$return = array('error' => 1, 'message' => 'Такой Email уже зарегистрирован');
			}
			else {
				$this->db->set('login', $_POST['email']);
				$this->db->set('password', md5($_POST['password']));
				$this->db->insert('users');
				$return = array('error' => 0, 'message' => 'Вы успешно зарегистрированы.<br><a href="/index.php/user/login">Перейти ко входу?</a>');
			}
		}
		return $return;
    }
	
    function idUser()
    {
        $this->db->where('login', $this->session->userdata('login'));
        $this->db->where('password', $this->session->userdata('password'));
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
			$row = $query->row();
            return $row->id;
        } else {
            return false;
        }
    }
}
