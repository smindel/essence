{{ Resources::add("framework/resources/HasOneFormField.js"); }}
<div class="field {{{get_class($Me)}}}">
    <div class="error">{{{$Me->getError()}}}</div>
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    {{ if ($Me->getClass() && $Me->getParent()->getObject()->id): }}
        <div class="option">
            <a class="button" href="{{{$Me->relationLink()}}}">{{{$Me->getClass()}}} erstellen</a>
        </div>
    {{ endif; }}
    {{ if ($Me->getClass()): }}
        <div class="option">
            <input type="hiddenn" name="{{{$Me->getFullName()}}}" value="{{{ $Me->getObject() ? $Me->getObject()->id : 0 }}}">
            <input type="search" class="HasOneFormFieldOptions" data-data-field-name="{{{$Me->getFullName()}}}" data-edit-link-id="{{{$Me->getFullName()}}}_link" placeholder="{{{ $Me->getObject() ? $Me->getObject()->title() : $Me->getClass() . " verknüpfen" }}}" list="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]">
            <datalist id="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]">
                {{ if ($Me->canSetNull()): }}
                    <option value="0" data-edit-url="{{{$Me->relationLink()}}}">no {{{$Me->getClass()}}}</option>
                {{ endif; }}
                {{ foreach ($Me->getOptions() as $option): }}
                    {{ $name = $Me->getName(); if ($option->id != $Me->getParent()->getObject()->$name->id): }}
                        <option value="{{{$option->id}}}" data-edit-url="{{{$Me->relationLink($option->id)}}}">{{{$option->title()}}}</option>
                    {{ endif; }}
                {{ endforeach; }}
            </datalist>
            <a id="{{{$Me->getFullName()}}}_link"
                {{ if ($Me->getParent()->getObject()->id): }}
                    href="{{{$Me->relationLink(
                        $Me->getObject() ? $Me->getObject()->id : null
                    )}}}"
                {{ endif; }}
            >edit</a>
        </div>
    {{ endif; }}
</div>