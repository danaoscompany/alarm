<?php

include 'fcm.php';

class Admin extends CI_Controller {

	public function signup() {
		$name = $this->input->post('name');
		$email = $this->input->post('email');
		if ($this->db->get_where('admins', array(
			'email' => $email
		))->num_rows() > 0) {
			echo -1;
			return;
		}
		$password = $this->input->post('password');
		$rowNum = $this->db->get('admins')->num_rows()+1;
		$this->db->insert('admins', array(
			'name' => $name,
			'email' => $email,
			'password' => $password,
			'code' => STR_PAD($rowNum, 4, 0, STR_PAD_LEFT)
		));
		echo 1;
	}
	
	public function update_fcm_token() {
		$adminID = intval($this->input->post('admin_id'));
		$fcmToken = $this->input->post('token');
		$this->db->where('id', $adminID);
		$this->db->update('admins', array(
			'fcm_token' => $fcmToken
		));
	}
	
	public function set_alarm() {
	  $adminID = intval($this->input->post('admin_id'));
	  $alarmType = intval($this->input->post('alarm_type'));
	  $on = intval($this->input->post('on'));
	  $users = $this->db->get_where('admins', array(
	      'id' => $adminID
	    ))->result_array();
	  $title = "";
	  $clickAction = "";
	  if ($on == 0) {
	    $title = "Alarm mati";
	    $clickAction = "com.prod.alarm.ALERT_OFF";
	  } else if ($on == 1) {
	    $title = "Alarm menyala";
	    $clickAction = "com.prod.alarm.ALERT_ON";
	  }
	  for ($i=0; $i<sizeof($users); $i++) {
	    $user = $users[$i];
	    $fcmToken = $user['fcm_token'];
	    send_message($fcmToken, $title, 'Ketuk untuk melihat info', $clickAction, array(
	        'alarm_type' => "" . $alarmType
	      ));
	  }
	}
	
	public function switch_alarm() {
		$adminID = intval($this->input->post('admin_id'));
		$alarmActive = intval($this->db->get_where('admins', array(
		))->row_array()['alarm_active']);
		if ($alarmActive == 0) {
			$alarmActive = 1;
		} else {
			$alarmActive = 0;
		}
		$fcmToken = $this->db->get_where('admins', array(
			'id' => $adminID
		))->row_array()['fcm_token'];
		$this->db->where('id', $adminID);
		$this->db->update('admins', array(
			'alarm_active' => $alarmActive
		));
    $url = "https://fcm.googleapis.com/fcm/send";
    $token = $fcmToken;
    $serverKey = 'AAAAckBCIKU:APA91bF8yKTSWvRLuYpCe2rbQYrZKsn9Lmg5iRGMc6oXaSlTw1xUcv9E-xCyweIhFlK_CzgoxOe0T4qOoEdi0xodNJhQHGrxfzQJkQG3BMASB7k3MSYaat83V2WF8JNQoUmcJg-r9TnC';
    $title = "This is title";
    $body = "This is text";
    $notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1');
    $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
    $json = json_encode($arrayToSend);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key='. $serverKey;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    //Send the request
    $response = curl_exec($ch);
    //Close request
    curl_close($ch);
	}
}
