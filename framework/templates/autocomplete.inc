{{ Resources::add("framework/resources/jquery-2.1.1.min.js", 100); }}
{{ Resources::add("framework/resources/autocomplete.js", 10); }}
{{ Resources::add("framework/resources/autocomplete.css"); }}
<input
    class="autocomplete"
    type="text"
    id="{{{$id}}}"
    name="{{{$name}}}"
    value="{{{$value}}}"
    data-autocomplete-url="{{{$url}}}"
    data-edit-url="{{{$link}}}"
    placeholder="{{{$label}}}"
    {{ if($required): }} required{{ endif; }}
>
