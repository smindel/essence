<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <base href="{{{BASE_URL}}}">
    <link href='static/admin.css' rel='stylesheet' type='text/css'>
</head>
<body>
<h1>{{{get_class($Form->getObject())}}} '{{{$Form->getObject()->title()}}}' bearbeiten <small><a href="{{{$Me->link('list', get_class($Form->getObject()))}}}" class="icon" data-icon="up">up</a>  <a href="authentication/logout" class="icon" data-icon="logout">logout</a></small></h1>
{{{$Form->html()}}}
</body>
</html>