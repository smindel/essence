<div class="field {{{get_class($Me)}}}">
    <div class="error">{{{$Me->getError()}}}</div>
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <!-- <ul id="{{{$Me->getName()}}}"> -->
        {{ foreach ($Options as $option): }}
            <div class="option">
                <input
                    name="{{{$Me->getFullName()}}}"
                    id="{{{$Me->getName()}}}[{{{$option->id}}}]"
                    type="radio"
                    value="{{{$option->id}}}" {{{($option->id == $Me->getValue() ? ' checked' : '')}}}>
                <a{{ if ($Me->getParent()->getObject()->id): }} href="{{{$Me->relationLink($option->id)}}}"{{ endif; }}>{{{$option->title()}}}</a>
            </div>
        {{ endforeach; }}
    <!-- </ul> -->
</div>