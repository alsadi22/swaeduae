<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reports</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2>Volunteer Reports</h2>
    <table>
        <thead>
            <tr>
                <th>Volunteer</th>
                <th>Event</th>
                <th>Hours</th>
                <th>Checked In</th>
                <th>Checked Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $row)
            <tr>
                <td>{{ $row['Volunteer'] }}</td>
                <td>{{ $row['Event'] }}</td>
                <td>{{ $row['Hours'] }}</td>
                <td>{{ $row['Checked In'] }}</td>
                <td>{{ $row['Checked Out'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
