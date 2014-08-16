{{ Resources::add("framework/resources/jquery-2.1.1.min.js", 100); }}
{{ Resources::add("framework/resources/autocomplete.js", 10); }}
{{ Resources::add("framework/resources/autocomplete.css"); }}
<input
    class="autocomplete"
    type="text"
    id="{{{$Me->getName()}}}"
    name="{{{$Me->getFullName()}}}"
    value="{{{$Me->getValue()}}}"
    data-autocomplete-url="{{{$Me->currentlink() . 'fields/' . $Me->getName() . '/suggest/'}}}"
    placeholder="{{{ $Me->getValue() ? $Me->getObject()->title() : 'no ' . ($Me->getClass()) }}}"
    {{ if($Me->getRequired()): }} required{{ endif; }}
>
