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
            <tr>
                <td><center>{{$username}}</center></td>
                <td><center>{{$total}}</center></td>
                <td><center>{{$in_process}}</center></td>
                <td><center>{{$converted}}</center></td>
                <td><center>{{$closed}}</center></td>
            </tr>
        </tbody>
    </table>
</body>
</html>