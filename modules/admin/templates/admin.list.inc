<h1>{{{$Model}}}-Übersicht</h1>
<ul>
    {{foreach ($Links as $link):}}
        <li class="{{{$link['class']}}}"><a href="{{{$link['link']}}}">{{{$link['title']}}}</a></li>
    {{endforeach;}}
</ul>
</ul>