<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">    
</head>
<body>
    <hr>
    <center>Daily Reprt of Leads</center>
    <hr>
    <table >
        <thead>
            <th>User/Lead Owner</th>
            <th>Total Leads</th>
            <th>In Process Leads</th>
            <th>Converted Leads</th>
            <th>Closed Leads</th>
        </thead>
        <tbody>
            @foreach($admin_mail as $adm_mail)
                <tr>
                    <td><center>{{$adm_mail['username']}}</center></td>
                    <td><center>{{$adm_mail['total']}}</center></td>
                    <td><center>{{$adm_mail['in_process']}}</center></td>
                    <td><center>{{$adm_mail['converted']}}</center></td>
                    <td><center>{{$adm_mail['closed']}}</center></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>