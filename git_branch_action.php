<?php
/**
* Script for run git actions on test servers
* @author Samorodskiy Ivan swiesto@gmail.com
*/

    $shell_str = '';
	$answer = [];
    
    if(isset($_POST['action'])) {
        $action = $_POST['action'];
    }
    if(isset($_POST['branch'])) {
        $branch = $_POST['branch'];
    }
       
    switch ($action) {        
        case 'switch':
            $shell_str = sprintf("git checkout %s", $branch);
            break;            
        case 'create':
            $shell_str = sprintf("git checkout -b %s", $branch);
            break; 
        case 'pull_origin':
            $shell_str = sprintf("git pull origin %s", $branch);
            break;
        case 'delete_branch':
            $shell_str = sprintf("git branch -D %s", $branch);
            break;
		case 'execute_comand':
            $shell_str = sprintf("%s", $branch);
            break;
    }
    
    if($shell_str){
        exec($shell_str, $answer);
    }
    
    //Get all branches
    $str = "git branch";
    $branches = explode("\n", shell_exec($str));
    
    //Remove empty elements
    $branches = array_diff($branches, ['']);
	$ret['branches'] = $branches;
	$ret['answer'] = $answer;
        
    echo json_encode($ret);