<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <base href="{{{BASE_URL}}}">
    <title>essence</title>
    <link rel="shortcut icon" href="{{{BASE_URL}}}static/favicon.ico">
    {{{ Resources::css() }}}
</head>
<body data-fyi-url="{{ if (isset($Me)) echo $Me->currentLink(); }}">
    <header>
        <a href="{{{$Me->link()}}}" class="logo">ESSENCE</a>
    </header>
    <nav class="left">
        {{{$Me->Menu()}}}
    </nav>
    <nav class="right"></nav>
    <main>
        {{{ $_CONTENT_ }}}
    </main>
    <footer>
        <a href="backend/panel/UserManager/edit/user/{{{Authentication::user()->id}}}"><span class="awesome"></span> {{{Authentication::user()->Name}}}</a>
        <a href="authentication/logout"><span class="awesome"></span> logout</a>
    </footer>
    {{{ Resources::js() }}}
</body>
</html>