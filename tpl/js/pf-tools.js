Pace.options = {
	ajax: true
};
$('.login-btn').click(function(){
	$.ajax({type: 'POST',dataType: 'json',url: '/api/login',success: function(result){
		if(result['code'] == 0)
			location.reload();
		else
			$('#login-message').html(result['message']);
	}, error: function(result, textStatus, errorThrown){
		$('#login-text').html('Ошибка при создании запроса');
	}, data: 'login='+$('#login-login').val()+'&password='+$('#login-password').val()+(($("#login-remember").is(':checked'))?'&remember=1':'')});
	return false;
});
$('.btn-logout').click(function(){
	$.ajax({type: 'GET',dataType: 'json',url: '/api/logout',success: function(result){
			location.reload();
	}, error: function(result, textStatus, errorThrown){
		alert('Ошибка при создании запроса');
	}});
	return false;
});
$('.relogin-btn').click(function(){
	$.ajax({type: 'POST',dataType: 'json',url: '/api/relogin',success: function(result){
		if(result['code'] == 0)
			location.reload();
		else
			$('#relogin-message').html(result['message']);
	}, error: function(result, textStatus, errorThrown){
		$('#relogin-text').html('Ошибка при создании запроса');
	}, data: 'password='+$('#relogin-password').val()});
	return false;
});
function updateTasks () {
	Pace.ignore(
		function () {
			$.ajax({type: 'GET',dataType: 'json',url: '/api/tasks',success: function(result){
				$('.tasks_count').html(result['params']['count']);
				$('.tasks_list_header').html(result['message']);
				setTimeout(updateTasks, 5000);
			}, error: function(result, textStatus, errorThrown){
				console.error('Tasks: Ошибка при создании запроса');
			}});
		}
	);
}
var last_message_all = 0;
function updateAllChat () {
	Pace.ignore(
		function () {
			$.ajax({type: 'GET',dataType: 'json',url: '/api/chat/0/'+last_message_all,success: function(result){
				if (result['code'] == 0) {
					if(result['message'].length) {
						var currentHeight = $("#all_chat_mes").height();
		//				$("#all_chat_mes").slimscroll();
						var isdown = ($('#all_chat_mes').parent().scrollTop() >= (currentHeight - $('#all_chat_mes').parent().height()-100));

						$("#all_chat_mes").append(result['message']);
						last_message_all = result['params']['last_id'];
						if (isdown)
							$("#all_chat_mes").animate({ scrollTop: $(document).height() }, "slow");
					}
				}
				setTimeout(updateAllChat, 512);
			}, error: function(result, textStatus, errorThrown){
				console.error('AllChat: Ошибка при создании запроса');
			}});
		}
	);
}
function sendAllChat (){
	$.ajax({type: 'POST',dataType: 'json',url: '/api/chat_message/0',success: function(result){
		if(result['code'] == 0)
			$('#chat_reply').val('');
		else
			console.error(result['message']);
	}, error: function(result, textStatus, errorThrown){
		console.error('Ошибка при создании запроса');
	}, data: 'message='+$('#chat_reply').val()});
	return false;
}
$('#all_chat_mes').slimScroll().bind('slimscroll', function(e, pos){
	console.log("Reached " + pos);
});
