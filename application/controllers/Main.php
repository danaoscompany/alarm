<?php

include 'fcm.php';

class Main extends CI_Controller {

	public function login() {
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		if ($this->db->get_where('users', array(
			'email' => $email
		))->num_rows() > 0) {
			$results = $this->db->get_where('users', array(
				'email' => $email,
				'password' => $password
			))->result_array();
			if (count($results) > 0) {
				$row = $results[0];
				$row['role'] = 'user';
				echo json_encode($row);
			} else {
				echo -1;
			}
		} else if ($this->db->get_where('admins', array(
			'email' => $email
		))->num_rows() > 0) {
			$results = $this->db->get_where('admins', array(
				'email' => $email,
				'password' => $password
			))->result_array();
			if (count($results) > 0) {
				$row = $results[0];
				$row['role'] = 'admin';
				echo json_encode($row);
			} else {
				echo -1;
			}
		}
	}

	public function signup() {
		$name = $this->input->post('name');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$role = intval($this->input->post('role'));
		if ($this->db->get_where('admins', array(
			'email' => $email
		))->num_rows() > 0) {
			echo -1;
			return;
		}
		if ($this->db->get_where('users', array(
			'email' => $email
		))->num_rows() > 0) {
			echo -1;
			return;
		}
		if ($role == 0) { //Admin
			$rowNum = $this->db->get('admins')->num_rows()+1;
			$this->db->insert('admins', array(
				'name' => $name,
				'email' => $email,
				'password' => $password,
				'code' => STR_PAD($rowNum, 4, 0, STR_PAD_LEFT)
			));
		} else if ($role == 1) { // User
			$adminCode = $this->input->post('admin_code');
			if ($this->db->get_where('admins', array(
					'code' => $adminCode
				))->num_rows() > 0) {
				echo -2;
				return;
			}
			$this->db->insert('users', array(
				'name' => $name,
				'email' => $email,
				'password' => $password,
				'admin_code' => $adminCode
			));
		}
		echo 1;
	}
	
	public function get() {
		$name = $this->input->post('name');
		echo json_encode($this->db->get($name)->result_array());
	}
	
	public function get_by_id() {
		$name = $this->input->post('name');
		$id = intval($this->input->post('id'));
		echo json_encode($this->db->get_where($name, array(
			'id' => $id
		))->result_array());
	}
	
	public function get_by_id_name() {
		$name = $this->input->post('name');
		$idName = $this->input->post('id_name');
		$id = intval($this->input->post('id'));
		echo json_encode($this->db->get_where($name, array(
			$idName => $id
		))->result_array());
	}
	
	public function get_by_id_name_string() {
		$name = $this->input->post('name');
		$idName = $this->input->post('id_name');
		$id = $this->input->post('id');
		echo json_encode($this->db->get_where($name, array(
			$idName => $id
		))->result_array());
	}
	
	public function fcm_test() {
	  $adminID = 1;
	  $token = $this->db->get_where('admins', array(
	      'id' => $adminID
	    ))->row_array()['fcm_token'];
	  send_message($token, 'Judul pesan', 'Isi pesan', 'myaction', array(
	      'name' => 'Dana'
	    ));
	}
}
