<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <base href="{{{BASE_URL}}}">
    <link href='static/admin.css' rel='stylesheet' type='text/css'>
</head>
<body>
<h1>Liste <small><a href="{{{$Me->link()}}}">zurück</a></small></h1>
<ul>
    {{foreach ($Links as $link):}}
        <li class="{{{$link['class']}}}"><a href="{{{$link['link']}}}">{{{$link['title']}}}</a></li>
    {{endforeach;}}
</ul>
</ul>
</body>
</html>