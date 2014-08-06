{{ Resources::add("framework/resources/HasOneFormField.js"); }}
<div class="field {{{get_class($Me)}}}">
    <div class="error">{{{$Me->getError()}}}</div>
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    {{ if ($Create && $Me->getParent()->getObject()->id): }}
        <div class="option">
            <a class="button" href="{{{$Me->relationLink()}}}">{{{$Create}}} erstellen</a>
        </div>
    {{ endif; }}
    {{ if ($Join): }}
        <div class="option">
            <input type="hidden" name="{{{$Me->getFullName()}}}" value="{{{ $Value ? $Value->id : 0 }}}">
            <input class="HasOneFormFieldOptions" data-data-field-name="{{{$Me->getFullName()}}}" data-edit-link-id="{{{$Me->getFullName()}}}_link" placeholder="{{{ $Value ? $Value->title() : "$Join verknüpfen" }}}" list="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]">
            <datalist id="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]">
                {{ if ($SetNull): }}
                    <option value="0" data-edit-url="{{{$Me->relationLink()}}}">{{{$SetNull}}}</option>
                {{ endif; }}
                {{ foreach ($Options as $option): }}
                    {{ $name = $Me->getName(); if ($option->id != $Object->$name->id): }}
                        <option value="{{{$option->id}}}" data-edit-url="{{{$Me->relationLink($option->id)}}}">{{{$option->title()}}}</option>
                    {{ endif; }}
                {{ endforeach; }}
            </datalist>
            <a id="{{{$Me->getFullName()}}}_link"
                {{ if ($Me->getParent()->getObject()->id): }}
                    href="{{{$Me->relationLink($Value->id)}}}"
                {{ endif; }}
            >edit</a>
        </div>
    {{ endif; }}
</div>