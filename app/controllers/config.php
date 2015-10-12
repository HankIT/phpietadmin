<?php namespace phpietadmin\app\controllers;
use phpietadmin\app\core;

    class config extends core\BaseController {
		public function vg() {
			$this->view('config/vg');
		}

		/**
		 *
		 * Displays the phpietadmin user config menu
		 *
		 * @param	$param1 string
		 * @return      void
		 *
		 */
		public function user($param1) {
			switch ($param1) {
				case 'show':
					$users = $this->model('User');
					$data = $users->return_data();

					if ($data !== false) {
						$this->view('config/user_table', $data);
					} else {
						$this->view('message', array('message' => 'No user available!', 'type' => 'warning'));
					}
					break;
				case 'delete':
					if (isset($_POST['username'])) {
						$user = $this->model('User', $_POST['username']);
						$user->delete();
						echo json_encode($user->logging->get_action_log());
					}
					break;
				case 'add':
					if (isset($_POST['username'], $_POST['password'])) {
						$user = $this->model('User', $_POST['username']);
						$user->add($_POST['password']);
						echo json_encode($user->logging->get_action_log());
					}
					break;
				case 'change':
					if (isset($_POST['username'], $_POST['row'], $_POST['value'])) {
						$user = $this->model('User', $_POST['username']);
						$user->change($_POST['row'], $_POST['value']);
						echo json_encode($user->logging->get_action_log());
					}
					break;
				default:
					$this->view('message', array('message' => 'Invalid url', 'type' => 'warning'));
			}
		}

		public function show($param) {
			switch ($param) {
				case 'iet':
					$data = $this->base_model->database->get_config_by_category('iet');
					break;
				case 'misc':
					$data = $this->base_model->database->get_config_by_category('misc');
					break;
				case 'bin':
					$data = $this->base_model->database->get_config_by_category('bin');
					break;
				case 'logging':
					$data = $this->base_model->database->get_config_by_category('logging');
					break;
				default:
					$this->view('message', array('message' => 'Invalid url', 'type' => 'warning'));
			}

			if (isset($data) && !empty($data) && $data !== false) {
				$this->view('config/configtable', $data);
			}
		}

		public function edit_config() {
			if (isset($_GET['option'], $_GET['value'])) {
				$config = $this->model('Config', $_GET['option']);
				$config->change_config('value', $_GET['value']);
				echo json_encode($config->logging->get_action_result());
			}
		}
	}