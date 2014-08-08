{{ Resources::add("framework/resources/HasOneFormField.js"); }}
<div class="field {{{get_class($Me)}}}">
    <div class="error">{{{$Me->getError()}}}</div>
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    {{ if ($Me->getClass()): }}
        <div class="option">
            <input type="hidden" name="{{{$Me->getFullName()}}}" value="{{{$Me->getValue() ?: 0}}}">
            <input type="search" class="HasOneFormFieldOptions" data-data-field-name="{{{$Me->getFullName()}}}" data-edit-link-id="{{{$Me->getFullName()}}}_link" placeholder="{{{ $Me->getValue() ? $Me->getObject()->title() : ($Me->getClass() . ' verknüpfen') }}}" list="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]">
            <datalist id="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Options]">
                {{ if ($Me->canSetNull()): }}
                    <option value="0" data-edit-url="">no {{{$Me->getClass()}}}</option>
                {{ endif; }}
                {{ foreach ($Me->getOptions() as $option): }}
                    <option value="{{{$option->id}}}" data-edit-url="{{{$Me->relationLink($option->id)}}}">{{{$option->title()}}}</option>
                {{ endforeach; }}
            </datalist>
            <a id="{{{$Me->getFullName()}}}_link"
                {{ if ($Me->getValue()): }}
                    href="{{{$Me->relationLink(
                        $Me->getValue()
                    )}}}"
                {{ endif; }}
            >{{ if ($Me->getValue()) echo "edit"; }}</a>
        </div>
    {{ endif; }}
</div>