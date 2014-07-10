<h1>{{{$Model}}}-Übersicht <small><a href="{{{$Me->link()}}}" class="icon" data-icon="up">up</a> <a href="authentication/logout" class="icon" data-icon="logout">logout</a></small></h1>
<ul>
    {{foreach ($Links as $link):}}
        <li class="{{{$link['class']}}}"><a href="{{{$link['link']}}}">{{{$link['title']}}}</a></li>
    {{endforeach;}}
</ul>
</ul>