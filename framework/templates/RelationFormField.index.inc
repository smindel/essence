<div class="field {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <ul id="{{{$Me->getName()}}}">
        {{ if ($Create && $Me->getParent()->getObject()->id): }}
            <li class="create">
                <a href="{{{$Me->relationLink()}}}">{{{$Create}}} erstellen</a>
            </li>
        {{ endif; }}
        {{ foreach ($Value as $option): }}
            <li class="edit">
                <a{{ if ($Me->getParent()->getObject()->id): }} href="{{{$Me->relationLink($option->id)}}}"{{ endif; }}>{{{$option->title()}}}</a>
            </li>
        {{ endforeach; }}
    </ul>
</div>