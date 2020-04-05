<?php

class User extends CI_Controller {

	public function signup() {
		$name = $this->input->post('name');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$adminCode = $this->input->post('admin_code');
		if ($this->db->get_where('admins', array(
			'code' => $adminCode
		))->num_rows() > 0) {
			echo -1;
			return;
		}
		$this->db->insert('admins', array(
			'name' => $name,
			'email' => $email,
			'password' => $password,
			'admin_code' => $adminCode
		));
		echo 1;
	}
	
	public function update_fcm_token() {
		$userID = intval($this->input->post('user_id'));
		$fcmToken = $this->input->post('token');
		$this->db->where('id', $userID);
		$this->db->update('users', array(
			'fcm_token' => $fcmToken
		));
	}
}
