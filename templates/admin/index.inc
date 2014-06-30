<!DOCTYPE html>
<html>
<meta charset='utf-8'>
<base href="{{{BASE_URL}}}">
<head>
</head>
<body>
<h1>Index</h1>
<ul>
    {{foreach ($Links as $url => $label):}}
        <li><a href="{{{$url}}}">{{{$label}}}</a></li>
    {{endforeach;}}
</ul>
</body>
</html>