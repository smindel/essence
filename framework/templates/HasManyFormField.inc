<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <div class="field">
        {{ if ($Me->getParent()->getObject()->id): }}
            {{ if ($Me->getClass()): }}
                <div class="option">
                    <a class="button" href="{{{$Me->relationLink()}}}">{{{$Me->getClass()}}} erstellen</a>
                </div>
            {{ endif; }}
            {{ if ($Me->getClass()): }}
                <div class="option">
                    <input type="search" placeholder="{{{$Me->getClass()}}} zuordnen" list="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]" onselect="document.location.href = '{{{$Me->relationLink()}}}' + this.value;">
                    <datalist id="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]">
                        {{ foreach ($Me->getRemainingOptions() as $option): }}
                            <option value="{{{$option->id}}}">{{{$option->title()}}}</option>
                        {{ endforeach; }}
                    </datalist>
                </div>
            {{ endif; }}
        {{ endif; }}
        {{ foreach ($Me->getSelected() as $option): }}
            <div class="option">
                <a{{ if ($Me->getParent()->getObject()->id): }} href="{{{$Me->relationLink($option->id)}}}"{{ endif; }}>{{{$option->title()}}}</a>
            </div>
        {{ endforeach; }}
        <div class="message error">{{{$Me->getError()}}}</div>
    </div>
</div>