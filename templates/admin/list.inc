<!DOCTYPE html>
<html>
<meta charset='utf-8'>
<base href="{{{BASE_URL}}}">
<head>
</head>
<body>
<h1>Liste <small><a href="{{{$Me->link()}}}">zurück</a></small></h1>
<li>
    {{foreach ($Links as $url => $label):}}
        <li><a href="{{{$url}}}">{{{$label}}}</a></li>
    {{endforeach;}}
</ul>
</body>
</html>