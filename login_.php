<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Private Project Prototype</title>
	<link href="c/log-style.css" rel="stylesheet" type="text/css" media="all">
	
	<script type="text/javascript" src="js/jquery-latest.min.js"></script>
	
	<script type="text/javascript">
	
	$(document).ready(function(){
		$("#login").click(function(){
			
			var action = $("#log-form").attr('action');
			var form_data = {
				username : $("#username").val(),
				password : $("#password").val(),
				is_ajax: 1
			};
			
			$.ajax({
				type: "POST",
				url: action,
				data: form_data,
				success: function(response)
				{
					if(response == "success")
						$("#log-form").slideUp('slow', function(){
							$("#message").html('<p class="success">You have successfully<br />logged in indeed</p><p>Redirecting now ...</p>');
                            document.location.href="admin.php"
						});
					else
						$("#message").html('<p class="error">ERROR : Invalid username and/or password - Oops</p>');
				}	
			});
			return false;
		});
	});
	</script>

</head>

<body>
	<div class="log-container">
		<h1>Admin Area</h1>
		<form action="yaka-log.php" id="log-form" name="log-form" method="post">
			
			<div>
				<label for="username">Username :</label>
				<input type="text" name="username" id="username" placeholder="username"/>
			</div>
			
			<div>
				<label for="password">Password :</label>
				<input type="password" name="password" id="password" placeholder="password" />
			</div>
			
			<div>				
				<button type="submit" id="login">enter master</button>
			</div>
			
		</form>
		<div id="message"></div>
	</div>
</body>
</html>