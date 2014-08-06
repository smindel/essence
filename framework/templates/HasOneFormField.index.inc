<div class="field {{{get_class($Me)}}}">
    <div class="error">{{{$Me->getError()}}}</div>
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    {{ if ($Me->getParent()->getObject()->id): }}
        {{ if ($Join): }}
            <div class="option">
                <input name="{{{$Me->getFullName()}}}" value="{{{$Value->id}}}" placeholder="{{{$Join}}} zuordnen" list="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]" onselect="console.log(this.value);">
                <datalist id="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]">
                    {{ if ($SetNull): }}
                        <option value="0">{{{$SetNull}}}</option>
                    {{ endif; }}
                    {{ foreach ($Options as $option): }}
                        {{ $name = $Me->getName(); if ($option->id != $Object->$name->id): }}
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
    {{ if ($Value): }}
        <a
            {{ if ($Me->getParent()->getObject()->id): }}
                href="{{{$Me->relationLink($Value->id)}}}"
            {{ endif; }}
        >{{{$Value->title()}}}</a>
    {{ endif; }}
</div>