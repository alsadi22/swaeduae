@extends('layouts.email')
<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject ?? 'Notification' }}</title>
</head>
<body>
    <p>{{ $messageBody }}</p>
</body>
</html>
