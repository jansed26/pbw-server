<?php

class RequestController extends AppController {
    var $name = 'Request';
    var $uses = array('User','Statistic');
		
	function check_login() {
		$this->autoRender = false;
		
		if(!empty($_POST)) {
			$password = Security::hash($_POST['password'], null, true);
			
			$response = array();
			
			$user = $this->User->find('first', array('fields' => array('User.id, User.username, User.password, User.bodyweight, User.total_credits'), 'conditions' => array('User.username' => $_POST['username'], 'User.password' => $password)));
			
			$statistics = $this->Statistic->find('all', array('conditions' => array('Statistic.users_id' => $user['User']['id']), 'order' => array('Statistic.start_time' => 'DESC')));
			
			$response['user'] = $user['User'];
			$response['statistics'] = $statistics;
						
			echo json_encode($response);			
		}else{
			echo "error";
		}
	}
	
	function register_user() {
		$this->autoRender = false;
		
		if(!empty($_POST)) {
			$userCount = $this->User->find('count', array('conditions' => array('User.username' => $_POST['username'])));
			
			if($userCount == 0) {
				$user = $this->User->create();
				$user['User']['username'] = $_POST['username'];
				$user['User']['password'] = Security::hash($_POST['password'], null, true);
				$user['User']['bodyweight'] = $_POST['bodyweight'];
				$this->User->save($user);

				echo json_encode($user['User']);			
			}else{
				echo "error";	
			}			
		}else{
			echo "error";
		}
	}
	
	function submit_statistics() {
		$this->autoRender = false;
		
		if(!empty($_POST)) {
			$user = $this->User->find('first', array('conditions' => array('User.username' => $_POST['username'], 'User.password' => $_POST['password'])));
			
			if(!empty($user)) {
				$statistic = $this->Statistic->create();
				$statistic['Statistic']['users_id'] = $user['User']['id'];
				$statistic['Statistic']['start_time'] = $_POST['start_time'];
				$statistic['Statistic']['total_seconds'] = $_POST['total_seconds'];
				$statistic['Statistic']['distance'] = $_POST['distance'];
				$statistic['Statistic']['speed'] = $_POST['speed'];
				$statistic['Statistic']['calories_burned'] = $_POST['calories_burned'];
				$statistic['Statistic']['credits'] = $_POST['credits'];
				$statistic['Statistic']['workout_mode'] = $_POST['workout_mode'];
				$this->Statistic->save($statistic);
				
				$user['User']['total_credits'] += $statistic['Statistic']['credits'];
				$this->User->save($user);

				echo json_encode($user['User']);			
			}else{
				echo "error";	
			}			
		}else{
			echo "error";
		}
	}
	
	function save_settings() {
		$this->autoRender = false;
		
		if(!empty($_POST)) {
			$user = $this->User->find('first', array('conditions' => array('User.username' => $_POST['username'], 'User.password' => $_POST['old_password'])));			
			if(!empty($user)) {
				if(!empty($_POST['password'])) {
					$user['User']['password'] = Security::hash($_POST['password'], null, true);						
				}
				$user['User']['bodyweight'] = $_POST['bodyweight'];
				$this->User->save($user);

				echo json_encode($user['User']);			
			}else{
				echo "error";	
			}			
		}else{
			echo "error";
		}
	}		
}