<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p>Dear customer your password changed successfully<p>
    <p>Username: {{$data['userData']->contact_no}}</p>
    <p>New Password: <strong>{{$data['password']}}</strong></p>
</body>
</html>