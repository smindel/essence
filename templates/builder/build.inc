<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <base href="{{{BASE_URL}}}">
    <link href='static/builder.css' rel='stylesheet' type='text/css'>
</head>
<body>
<h1>Builder</h1>
<ul>
    {{foreach ($messages as $message):}}
        <li class="{{{$message['type']}}}">{{{$message['text']}}}</li>
    {{endforeach;}}
</ul>
</body>
</html>