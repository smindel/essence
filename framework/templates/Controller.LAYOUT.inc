<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <base href="{{{BASE_URL}}}">
    <link rel="stylesheet" type="text/css" href="framework/resources/controller.css">
    <link rel="stylesheet" type="text/css" href="static/admin.css">
    {{{ Resources::css() }}}
</head>
<body data-fyi-url="{{ if (isset($Me)) echo $Me->currentLink(); }}">
    {{{ $_CONTENT_ }}}
    {{{ Resources::js() }}}
</body>
</html>