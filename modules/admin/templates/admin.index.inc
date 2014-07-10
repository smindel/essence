<h1>Hauptmenü <small><a href="authentication/logout" class="icon" data-icon="logout">logout</a></small></h1>
<ul>
    {{foreach ($Links as $url => $label):}}
        <li class="list"><a href="{{{$url}}}">{{{$label}}}</a></li>
    {{endforeach;}}
</ul>