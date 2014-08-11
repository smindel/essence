{{ Resources::add("framework/resources/HasOneFormField.js"); }}
{{ Resources::add("framework/resources/HasOneFormField.css"); }}
<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <div class="field">
        <div class="autocomplete">
            <input class="autocomplete-value" type="hidden" name="{{{$Me->getFullName()}}}_copy" value="{{{$Me->getValue() ?: 0}}}">
            <input class="autocomplete-display" data-url="{{{$Me->currentlink() . 'fields/' . $Me->getName() . '/suggest/'}}}" data-suggestions-id="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Suggestions]" placeholder="{{{ $Me->getValue() ? $Me->getObject()->title() : ($Me->getClass() . ' verknüpfen') }}}">
            <a class="autocomplete-link" data-link-base="{{{$Me->relationLink()}}}"
                {{ if ($Me->getValue()): }}
                    href="{{{$Me->relationLink(
                        $Me->getValue()
                    )}}}"
                {{ endif; }}
            >{{ if ($Me->getValue()) echo "edit"; }}</a>
            <div class="autocomplete-suggestions empty" id="{{{$Me->getParent()->getName()}}}[{{{$Me->getName()}}}_Suggestions]"></div>
        </div>
        <div class="message error">{{{$Me->getError()}}}</div>
    </div>
</div>