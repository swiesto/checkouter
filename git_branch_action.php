<?php
/**
* Script for run git actions on test servers
* @author dragonangel@yandex.ru
*/
	if (isset($_GET['action'])) {
		$shell_str = '';
		$answer = [];
		
		if(!empty($_POST['instance']) && !empty($config['instances'][$_POST['instance']])) {
			$instance = $_POST['instance'];
			$preCommand = 'cd '.$config['instances'][$instance]['path'].' &&';
			if (!file_exists($instance)) {
				$file = fopen($instance, 'w+');
				fwrite($file, 'locked');
			} else {
				http_response_code(400);
				echo 'Уже выполняется другая команда на инстансе';
				die();
			}
		} else {
			http_response_code(400);
			echo 'Не задан инстанс';
			die();
		}
		
		try {
			if(isset($_POST['action'])) {
				$action = $_POST['action'];
			}
			if(isset($_POST['branch'])) {
				$branch = $_POST['branch'];
			}
			   
			switch ($action) {        
				case 'switch':
					$branchParts = explode('/', $branch);
					if (!empty($branchParts[0]) && trim($branchParts[0]) == 'remotes' && !empty($branchParts[1]) && trim($branchParts[1]) == 'origin') {
						unset($branchParts[0], $branchParts[1]);
						$branchLocal = implode('/', $branchParts);
						$actionComand = $preCommand.sprintf("git checkout -b %s %s", $branchLocal, $branch);
					} else {
						$actionComand = $preCommand.sprintf("git checkout %s", $branch);
					}
					
					foreach ($config['instances'][$instance]['comands'] as $comand) {
						if ($comand == '{action}') {
							$commands[] = $actionComand;
						} else {
							$commands[] = $preCommand.$comand;
						}
					}
					break;            
				case 'create':
					$commands[] = $preCommand.sprintf("git checkout -b %s", $branch);
					break; 
				case 'pull_origin':
					$actionComand = $preCommand.sprintf("git pull origin %s", $branch);
					
					foreach ($config['instances'][$instance]['comands'] as $comand) {
						if ($comand == '{action}') {
							$commands[] = $actionComand;
						} else {
							$commands[] = $preCommand.$comand;
						}
					}
					break;
				case 'delete_branch':
					//$commands[] = $preCommand.sprintf("git branch -D %s", $branch);
					break;
				case 'execute_comand':
					//$commands[] = $preCommand.sprintf("%s", $branch);
					break;
			}
			$code = 0;
			$answer = [];
			if(!empty($commands)){
				foreach ($commands as $command) {
					$answer[] = $command;
					if(!exec($command, $answer, $code)) {
						/*if ($code != 129) {
							$resultCmd = '';
							if (is_array($answer)) {
								$resultCmd = implode("\n", $answer);
							}
							throw new \Exception('Команда '.$command.' завершилась с кодом '.$code."\n".$resultCmd, 400);
							break;
						}*/
					}
				}
				
			}
			
			//Get all branches
			$str = $preCommand."git branch -a";
			$branches = explode("\n", shell_exec($str));
			foreach ($branches as $ind => $branch) {
				if (empty($branch) || strstr($branch, 'HEAD')) {
					unset($branches[$ind]);
				}
			}
			
			$ret['branches'] = $branches;
			$ret['answer'] = $answer;
				
			
		} catch(\Exception $e) {
			http_response_code(400);
			echo $e->getMessage();
			unlink($instance);
			die();
		}
		unlink($instance);
		echo json_encode($ret);
		die();
	}
    