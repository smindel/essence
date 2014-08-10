<ul class="widget light">
    {{foreach ($Links as $link):}}
        <li class="{{{$link['class']}}}"><a href="{{{$link['link']}}}">{{{$link['title']}}}</a></li>
    {{endforeach;}}
</ul>