{{ Resources::add("framework/resources/jquery-2.1.1.min.js", 100); }}
{{ Resources::add("framework/resources/collection.js", 10); }}
{{ Resources::add("framework/resources/collection.css"); }}
<table class="collection">
    <tbody>
        {{ if ($Me->getParent()->getObject()->id && $Me->getClass()): }}
            <tr id="{{{$Me->getClass()}}}[0]"><td><a href="{{{$Me->relationLink(0)}}}">new {{{$Me->getClass()}}}</a></td></tr>
        {{ endif; }}
        {{ foreach ($Me->getSelected() as $option): }}
            <tr id="{{{$Me->getClass()}}}[{{{$option->id}}}]"><td><a href="{{{$Me->relationLink($option->id)}}}">{{{$option->title()}}}</a></td></tr>
        {{ endforeach; }}
    </tbody>
</table>