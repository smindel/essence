<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <base href="{{{BASE_URL}}}">
    <title>essence</title>
    {{{ Resources::css() }}}
</head>
<body data-fyi-url="{{ if (isset($Me)) echo $Me->currentLink(); }}">
    <header>
        <a href="{{{$Me->link()}}}" class="logo">ESSENCE</a>
        <a class="toggle menu">toggle menu</a>
        <a href="backend/panel/admin/edit/user/{{{Authentication::user()->id}}}">{{{Authentication::user()->Name}}}</a>
        <a href="authentication/logout">logout</a>
    </header>
    <nav class="left">
        {{{$Me->Menu()}}}
    </nav>
    <nav class="right"></nav>
    <main>
        {{{ $_CONTENT_ }}}
    </main>
    <footer>
        &copy; 2014 PIT
    </footer>
    {{{ Resources::js() }}}
</body>
</html>