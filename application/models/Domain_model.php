<?php

class Domain_model extends CI_Model
{
    function addDomain()
    {
		$this->db->where('name', $_POST['domain']);
		$query = $this->db->get('domains');
        if ($query->num_rows() == 0) {
			mkdir("/var/www/html/vhosts/".$_POST['domain'], 0777);
			$site = 'Это сайт '.$_POST['domain'].'<br>Он принадлежит '.$this->session->userdata('login');
			file_put_contents("/var/www/html/vhosts/".$_POST['domain']."/index.html", $site);
			$this->db = $this->load->database('vsftp', TRUE);
			$this->db->set('user', $_POST['domain']);
			$this->db->set('pass', md5($_POST['password']));
			$this->db->insert('accounts');
			$ftpid = $this->db->insert_id();
			$this->db = $this->load->database('default', TRUE);
			$this->db->set('name', $_POST['domain']);
			$this->db->set('user', $this->Auth_model->idUser());
			$this->db->set('ftp', $ftpid);
			$this->db->insert('domains');
            return true;
        } else {
            return false;
        }
	}
	
    function updDomain()
    {
		$userid = $this->Auth_model->idUser();
		$this->db->where('id', $_POST['iddomain']);
		$this->db->where('user', $userid);
		$query = $this->db->get('domains');
		$row = $query->row();
		$ftpid = $row->ftp;
		$this->db = $this->load->database('vsftp', TRUE);
		$this->db->set('pass', md5($_POST['newpass']));
		$this->db->where('id', $ftpid);
		$this->db->update('accounts');
		$this->db = $this->load->database('default', TRUE);
	}
	
	function delDomain()
    {
		$userid = $this->Auth_model->idUser();
		$this->db->where('id', $_POST['iddomain']);
		$this->db->where('user', $userid);
		$query = $this->db->get('domains');
		$row = $query->row();
		$ftpid = $row->ftp;
		$name = $row->name;
		$this->db = $this->load->database('vsftp', TRUE);
		$this->db->where('id', $ftpid);
		$this->db->delete('accounts');
		$this->db = $this->load->database('default', TRUE);
		$this->db->where('id', $_POST['iddomain']);
		$this->db->where('user', $userid);
		$this->db->delete('domains');
		$this->rrmdir("/var/www/html/vhosts/".$name);
	}
		
	function getDomain(){
		$userid = $this->Auth_model->idUser();
		$this->db->where('user', $userid);
		$query = $this->db->get('domains');
		$domains = $query->result_array();
		return $domains;
	}
	
	function rrmdir($dir) { 
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object); 
				} 
			} 
		reset($objects); 
		rmdir($dir); 
   } 
 }
}