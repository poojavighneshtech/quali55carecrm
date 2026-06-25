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
    <center>Weekly Report of Orders</center>
    <hr>
    <table border="1">
        <thead>
            <tr>
                <th>Date</th>
                <th>Completed</th>
                <th>InCompleted</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($period_date as $key=>$date)
                <tr>
                    <td>{{$date}}</td>
                    <td>{{$completed[$key]}}</td>
                    <td>{{$incomplete[$key]}}</td>
                    <td>{{$day_by_total[$key]}}</td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td><strong>Total Orders</strong></td>
                <td><strong>{{$total_orders}}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>