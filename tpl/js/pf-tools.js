// To make Pace works on Ajax calls
$(document).ajaxStart(function() {
	Pace.restart();
});
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
