<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <base href="{{{BASE_URL}}}">
    <link href='static/admin.css' rel='stylesheet' type='text/css'>
</head>
<body>
<h1>Bearbeiten <small><a href="{{{$Me->link('list', get_class($Form->getObject()))}}}">zurück</a></small></h1>
{{{$Form}}}
</body>
</html>