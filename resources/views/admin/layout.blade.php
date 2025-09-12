<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body{background:#f8f9fa;}
        .admin-wrapper{display:flex;min-height:100vh;}
        .admin-sidebar{width:260px;}
        .admin-content{flex:1;padding:1rem;}
    </style>
</head>
<body>
    @includeIf('admin._topbar')
    <div class="admin-wrapper">
        @includeIf('admin.partials.sidebar')
        <main class="admin-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
