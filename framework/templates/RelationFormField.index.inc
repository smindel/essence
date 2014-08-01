<div class="field {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <ul id="{{{$Me->getName()}}}">
        {{ if ($Create): }}
            <li class="create">
                <a href="{{{$Me->relationLink()}}}">{{{$Create}}} erstellen</a>
            </li>
        {{ endif; }}
        {{ foreach ($Value as $option): }}
            <li class="edit">
                <a href="{{{$Me->relationLink($option->id)}}}">{{{$option->title()}}}</a>
            </li>
        {{ endforeach; }}
    </ul>
</div>