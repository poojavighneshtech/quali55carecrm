<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>User Details</title>
</head>
<body>
	<table border="" width="100%">
		<tr>
			<td colspan="">
				<img src="{{url('/')}}/assets/images/logo_small.png" alt="Company logo" style="width: 300px; height:70px" />
			</td>
			<td style="text-align: right">
				508,509/5,<br />
				Surya house,<br />
				Vidyavihar (East)
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h3>User Detail</h3>
			</td>
		</tr>
		<tr>
			<td>
				<Strong>Name: </Strong> {{$request->get('name')}}
			</td>
			<td rowspan="3">
				<strong>Address: </strong>
				<address>
					{{$request->get('addr_line_1')}},{{$request->get('addr_line_2')}},{{$request->get('landmark')}},{{$request->get('area')}},{{$request->get('city')}},{{$request->get('state')}},{{$request->get('country')}}, - {{$request->get('pincode')}},
				</address>
			</td>
			
		</tr>
		<tr>
			<td>
				<Strong>Contact No: </Strong> {{$request->get('contact_no')}}
			</td>
		</tr>
		<tr>
			<td>
				<Strong>Secondary Contact No: </Strong> {{$request->get('secondary_contact_no')}}
			</td>
		</tr>
		<tr>
			<td>
				<Strong>Email: </Strong> {{$request->get('email')}}
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h3>Credentials Details</h3>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Username: </strong> {{$request->get('contact_no')}}
			</td>
			<td>
				<strong>Password: </strong> {{$password}}
			</td>
		</tr>
	</table>
</body>
</html>