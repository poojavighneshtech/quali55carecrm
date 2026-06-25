<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">  
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            }

        th, td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
</style>  
</head>
<body>
    <hr>
    <center>Weekly Reprt of Leads</center>
    <hr>
    <table border="1">
        <thead>
            <tr>
                <th rowspan="2">User/Lead Owner</th>
                @foreach ($date_count as $key=> $date)
                    <th colspan="3">{{$date}}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($date_count as $key=> $date)
                    <th>In Process</th>
                    <th>Converted</th>
                    <th>Closed</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($user_data as $key=>$user)
                <tr>
                    <td>{{$user['username']}}</td>
                    @foreach ($date_count as $key=> $date)
                        @for($i=0;$i<count($user['in_process_lead']);$i++)
                            <td>{{$user['in_process_lead'][$i]}}</td>
                            <td>{{$user['converted_lead'][$i]}}</td>
                            <td>{{$user['closed_lead'][$i]}}</td>
                        @endfor
                    @endforeach

                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>