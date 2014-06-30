<h1>Builder</h1>
<ul>
    {{foreach ($messages as $message):}}
        <li class="{{{$message['type']}}}">{{{$message['text']}}}</li>
    {{endforeach;}}
</ul>