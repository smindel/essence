<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <base href="{{{BASE_URL}}}">
    {{{ Resources::css() }}}
</head>
<body data-fyi-url="{{ if (isset($Me)) echo $Me->currentLink(); }}">
    {{{ $_CONTENT_ }}}
    {{{ Resources::js() }}}
</body>
</html>