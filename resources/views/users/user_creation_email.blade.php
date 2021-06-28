<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Add User</title>
</head>
<body>
	<table border="1" cellpadding="0" cellspacing="0" style="background:white; border-top:8px solid #2D87BA; font-family:verdana,arial,helvetica,sans-serif; font-size:14px; margin:0 auto; padding:0; width:100%">
	<tbody>
		<tr>
			<td>
			<table border="1" cellpadding="0" cellspacing="5" style="width:100%">
				<tbody>
					<tr>
						<td>
						<div style="height:180px; overflow: hidden;">
						<div style="padding-top:40px; padding-left: 20px; float: left; width: 30%">
							<img src="http://www.visrox.com/newsite/wp-content/uploads/2016/04/visrox-new-logo.png">
						</div>
						</div>
							
						<div style="color:black; padding-left: 20px; padding-bottom: 0px">
						<h2><?php echo date('d-m-Y'); ?> <span style="font-size:16px"></span></h2>
						</div>
						<div style="color:black; padding-left: 150px;">

						<p>New User has been added in Almuftionline Control Panel. Please see below account details.Please see below details of User.</p>

						Email    : {{$email}} <br><br>
						Password : {{$password}} <br><br>

						<span style="float:right;padding-right:300px;padding-bottom:20px">Regards<br>
						Jamia Tur Rasheed <span>

						</div>
						
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>	
</body>
</html>
