<div class="field {{{get_class($Me)}}}">
    <div class="error">{{{$Me->getError()}}}</div>
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    {{ if ($Me->getParent()->getObject()->id): }}
        {{ if ($Join): }}
            <div class="option">
                <input placeholder="{{{$Join}}} zuordnen" list="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]" onselect="document.location.href = '{{{$Me->relationLink()}}}' + this.value;">
                <datalist id="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]">
                    {{ foreach ($Options as $option): }}
                        {{ if (!$option->$Remotefield || $option->$Remotefield->id != $Object->id): }}
                            <option value="{{{$option->id}}}">{{{$option->title()}}}</option>
                        {{ endif; }}
                    {{ endforeach; }}
                </datalist>
            </div>
        {{ endif; }}
        {{ if ($Create): }}
            <div class="option">
                <a class="button" href="{{{$Me->relationLink()}}}">{{{$Create}}} erstellen</a>
            </div>
        {{ endif; }}
    {{ endif; }}
    {{ foreach ($Value as $option): }}
        <div class="option">
            <a{{ if ($Me->getParent()->getObject()->id): }} href="{{{$Me->relationLink($option->id)}}}"{{ endif; }}>{{{$option->title()}}}</a>
        </div>
    {{ endforeach; }}
</div>