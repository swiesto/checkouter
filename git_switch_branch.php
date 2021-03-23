<!--Page for work with Git-->
<!--by swiesto@gmail.com-->
<html>
<head>
    <title><?=$_SERVER['SERVER_NAME']?></title>
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/js/jquery-latest.js"></script>
</head>
<body>
    <div class="container">
        <h1>Сервер: <?=$_SERVER['SERVER_NAME']?></h1>
       
        <div class="col-md-12 col-sm-12"> 
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="row">
                        <div class="panel-group" style="margin-right: 5px;">
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <h3>Ветки на этом сервере:</h3>
                                </div>
                            </div>
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <div id="branches_list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="row">
                        <div class="alert alert-info">
                            <h3>Текущая ветка is: <span id="current_branch"></span></h3>
                        </div>
                    </div>
                    <div class="row">
                    
                        <div class="panel-group">
                            <div class="panel panel-primary">
                                <div class="panel-body"><h4>Основные функции:</h4></div>
                            </div>
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <select id="select_branches" name="branch" class="selectpicker"></select>
                                    <button id="switch_branch" class="btn btn-primary">Переключить ветку</button>
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
                                        <input type="text" id="new_branch" name="new_branch"/>
                                        <button id="create_branch" class="btn btn-success">Создать ветку</button>
                                    </p>
                                    <p>
                                        <button id="pull_origin_branch" class="btn btn-default">Pull origin текущей ветки</button>
                                    </p>
                                    <p>
                                        <input type="text" id="remove_branch" name="remove_branch"/>
                                        <button id="delete_branch" class="btn btn-danger">Удалить ветку</button>
                                    </p>
									<p>
                                        <input type="text" id="comand_text" name="comand_text"/>
                                        <button id="execute_comand" class="btn btn-danger">Выполнить команду</button>
                                    </p>
									
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>

    $(document).ready(function(){
        sendRequest('init', null);
    });
    
    $('#switch_branch').click(function(){
        var switch_branch = $('#select_branches option:selected').val();
        var current_branch = $('#current_branch').val();    
        if(switch_branch == current_branch){
            alert( "Нельзя сменить ветвь саму на себя!");
            return;
        }    
        sendRequest('switch', switch_branch);
    });
    
    $('#create_branch').click(function(){
        var new_branch = $('#new_branch').val();
        var current_branch = $('#current_branch').val();
        
        if(new_branch == ''){
            alert( "Нельзя задать пустое название!"); 
            return;
        }     
        //Нужно проверить все ветви что бы не вызвать ошибок в запросе
        if(new_branch == current_branch){
            alert( "Такая ветвь уже существует!"); 
            return;
        }    
        sendRequest('create', new_branch);
    });
    
    $('#pull_origin_branch').click(function(){
        var current_branch = $('#current_branch').val();    
        sendRequest('pull_origin', current_branch);
    });
	
	$('#execute_comand').click(function(){
        var comand_text = $('#comand_text').val();    
        sendRequest('pull_origin', current_branch);
    });
    
    $('#delete_branch').click(function(){
        var remove_branch = $('#remove_branch').val();
        var current_branch = $('#current_branch').val();
        
        if(remove_branch == ''){
            alert( "Нельзя задать пустое название!"); 
            return;
        }     
    
        if(remove_branch == current_branch){
            alert( "Нельзя удалить текущую ветвь!"); 
            return;
        }    
        sendRequest('delete_branch', remove_branch);
    });
	
	function dump(obj) {
  var result = ""
  for (var i in obj)
    result += 'object' + "." + i + " = " + obj[i] + "\n";
  return result
}
    
    function sendRequest(action, branch){
        $.ajax({
            type: "POST",
            url: "git_branch_action.php",
            data: {
                action: action,
                branch: branch
            },
            success: function(response){
				var obj = jQuery.parseJSON(response);
				//alert(response);		//dump(obj)		
                //refresh branch list
                $('#branches_list').html('');
                $.each(obj.branches, function(index, value) {
                    if(value.match(/\*/)){
                        $('#current_branch').html(value);
                        $('#branches_list').append('# <span class="label label-primary">'+value+'</span><br>');
                    } else {
                        $('#branches_list').append('# '+value+'<br>');
                    }
					$('#test').html(obj.answer);
                });
                //refresh branch list in select input
                $('#select_branches').html('');
                var is_selected = '';
                $.each(obj.branches, function(index, value) {
                    if(value.match(/\*/)){
                        is_selected = 'selected';
                    } else {
                        is_selected = '';  
                    }
                    $('#select_branches').append('<option '+is_selected+' value="'+value+'">'+value+'</option>');
                });
            }
        });
    }
</script>

<div id="test">

</div>
</body>
</html>
