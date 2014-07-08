<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <base href="{{{BASE_URL}}}">
    <link href='static/admin.css' rel='stylesheet' type='text/css'>
</head>
<body>
<h1>Hauptmenü</h1>
<ul>
    {{foreach ($Links as $url => $label):}}
        <li class="list"><a href="{{{$url}}}">{{{$label}}}</a></li>
    {{endforeach;}}
</ul>
</body>
</html>