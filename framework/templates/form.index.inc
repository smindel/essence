<form class="widget light" action="{{{$Me->getAction()}}}" name="{{{$Me->getName()}}}" method="post">
    {{{implode(' > ', $Me->getBreadcrumbs())}}}
    <h1 class="form-title">{{{$Me->getTitle()}}}</h1>
    <div class="tab-base"></div>
    {{ if(($msg = $Me->getMessage())): }}<div class="message {{{$msg['type']}}}">{{{$msg['text']}}}</div>{{ endif; }}

    {{ foreach($Me->getFieldsByFieldSet() as $fieldsetname => $fieldset): }}
        {{ if ($fieldsetname[0] != '_'): }}
            {{ Resources::add("framework/resources/jquery-2.1.1.min.js", 100); }}
            {{ Resources::add("framework/resources/tabs.js", 10); }}
            {{ Resources::add("framework/resources/tabs.css"); }}
            <fieldset data-name="{{{$fieldsetname}}}">
        {{ endif; }}
            {{ foreach($fieldset as $field): }}
                {{{$field->__toString()}}}
            {{ endforeach; }}
        {{ if ($fieldsetname[0] != '_'): }}</fieldset>{{ endif; }}
    {{ endforeach; }}
</form>