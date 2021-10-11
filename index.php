<?php 
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
$config = include ('config.php');
include ('git_branch_action.php');
?>

<!--Page for work with Git-->
<!--by dragonangel@yandex.ru-->
<html>
<head>
    <title><?=$_SERVER['SERVER_NAME']?></title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Сервер: <?=$_SERVER['SERVER_NAME']?></h1>
       
        <div class="col-md-12 col-sm-12"> 
            <div class="row">
                <!--<div class="col-md-4 col-sm-4">
                    <div class="row">
                        <div class="panel-group" style="margin-right: 5px;">
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <h3>Ветки на этом сервере:</h3>
                                </div>
                            </div>
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <div class="branches_list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>-->
				<?php foreach($config['instances'] as $name => $inst) :?>
                <div class="col-md-6 col-sm-6 instance" id="<?php echo $name;?>">
					<div class="row">
                        <div class="alert alert-info">
							<div class="return-error" style="background:red;color: #000;"></div>
                            <h2>Название инстанса: <?php echo $name;?></h2>
							<h3>Текущая ветка is: <span class="current_branch"></span></h3>
                        </div>
                    </div>
                    <div class="row">
                    
                        <div class="panel-group">
                            <div class="panel panel-primary">
                                <div class="panel-body"><h4>Основные функции:</h4></div>
                            </div>
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <p><select name="branch" class="select_branches selectpicker"></select>
                                    <button class="switch_branch btn btn-primary">Переключить ветку</button></p>
                                </div>
                            </div>
                        </div> 

                        <div class="panel-group">
                            <div class="panel panel-primary">
                                <div class="panel-body"><h4>Дополнительные функции:</h4></div>
                            </div>
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <p>
                                        <button class="pull_origin_branch btn btn-secondary">Pull origin текущей ветки (подтянуть изменения)</button>
                                    </p>
									<p>
                                        <input type="text" class="new_branch" name="new_branch"/>
                                        <button class="create_branch btn btn-success">Создать ветку</button>
                                    </p>
                                    
                                    <p>
                                        <input type="text" class="remove_branch" name="remove_branch"/>
                                        <button class="delete_branch btn btn-danger">Удалить ветку</button>
                                    </p>
                                </div>
								<div class="command_result"></div>
                            </div>
                        </div>
                    </div>
                </div>
				<? endforeach; ?>
            </div>
        </div>
    </div>
<script>

    function getInstance($obj) {
		return $obj.closest('.instance').attr('id');
	}
	
	$(document).ready(function(){
        var instances = $('.instance');
		instances.each(function(id, value) {
			sendRequest('init', null, value.id);
		});
		//sendRequest('init', null, instance);
    });
    
    $('.switch_branch').click(function(){
        var instance = getInstance($(this));
		var switch_branch = $('#'+instance+' .select_branches option:selected').val();
        var current_branch = $('#'+instance+' .current_branch').val();    
        if(switch_branch == current_branch){
            alert( "Нельзя сменить ветвь саму на себя!"+' '+switch_branch+' '+current_branch+' '+instance);
            return;
        }    
        sendRequest('switch', switch_branch, instance);
    });
    
    $('.create_branch').click(function(){
        var instance = getInstance($(this));
		var new_branch = $('#'+instance+' .new_branch').val();
        var current_branch = $('#'+instance+' .current_branch').val();
        
        if(new_branch == ''){
            alert( "Нельзя задать пустое название!"); 
            return;
        }     
        //Нужно проверить все ветви что бы не вызвать ошибок в запросе
        if(new_branch == current_branch){
            alert( "Такая ветвь уже существует!"); 
            return;
        }    
        sendRequest('create', new_branch, instance);
    });
    
    $('.pull_origin_branch').click(function(){
        var instance = getInstance($(this)); 
		var current_branch = $('#'+instance+' .current_branch').val();    
        sendRequest('pull_origin', current_branch, instance);
    });
	
    
    $('.delete_branch').click(function(){
        var instance = getInstance($(this));
		var remove_branch = $('#'+instance+' .remove_branch').val();
        var current_branch = $('#'+instance+' .current_branch').val();
        
        if(remove_branch == ''){
            alert( "Нельзя задать пустое название!"); 
            return;
        }     
    
        if(remove_branch == current_branch){
            alert( "Нельзя удалить текущую ветвь!"); 
            return;
        }    
        sendRequest('delete_branch', remove_branch, instance);
    });
	
	function dump(obj) {
	  var result = ""
	  for (var i in obj)
		result += 'object' + "." + i + " = " + obj[i] + "\n";
	  return result
	}
    
    function sendRequest(action, branch, instance){
        $.ajax({
            type: "POST",
            url: "index.php?action=1",
            data: {
                action: action,
                branch: branch,
				instance: instance
            },
            success: function(response){
				var obj = jQuery.parseJSON(response);
				//alert(response);		//dump(obj)		
                //refresh branch list
                $('.branches_list').html('');
                $.each(obj.branches, function(index, value) {
                    if(value.match(/\*/)){
                        $('#'+instance+' .current_branch').html(value);
                        $('#'+instance+' .branches_list').append('# <span class="label label-primary">'+value+'</span><br>');
                    } else {
                        $('#'+instance+' .branches_list').append('# '+value+'<br>');
                    }
					$('#test').html(obj.answer);
                });
                //refresh branch list in select input
                $('#'+instance+' .select_branches').html('');
                var is_selected = '';
                $.each(obj.branches, function(index, value) {
                    if(value.match(/\*/)){
                        is_selected = 'selected';
                    } else {
                        is_selected = '';  
                    }
                    $('#'+instance+' .select_branches').append('<option '+is_selected+' value="'+value+'">'+value+'</option>');
                });
				if (obj.answer) {
					$.each(obj.answer, function(index, value) {
                    $('#'+instance+' .command_result').prepend('<p>'+value+'</p>');
                });
				}
				
            },
		error: function(response){
				$('#'+instance+' .return-error').html(response);
			}
        });
    }
</script>

<div class="test">

</div>
</body>
</html>
