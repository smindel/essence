{{ Resources::add("framework/resources/jquery-2.1.1.min.js", 100); }}
{{ Resources::add("framework/resources/HasOneFormField.js", 10); }}
{{ Resources::add("framework/resources/HasOneFormField.css"); }}
<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <div class="field">

        <input
            class="ochse"
            type="text"
            id="{{{$Me->getName()}}}"
            name="{{{$Me->getFullName()}}}"
            value="{{{$Me->getValue()}}}"
            data-autocomplete-url="{{{$Me->currentlink() . 'fields/' . $Me->getName() . '/suggest/'}}}"
            placeholder="{{{ $Me->getValue() ? $Me->getObject()->title() : ($Me->getClass() . ' verknüpfen') }}}"
            {{ if($Me->getRequired()): }} required{{ endif; }}
        >

        <div class="message error">{{{$Me->getError()}}}</div>
    </div>
</div>