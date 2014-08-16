<form class="widget light" action="{{{$Me->getAction()}}}" name="{{{$Me->getName()}}}" method="post">
    {{ if(($msg = $Me->getMessage())): }}<div class="message {{{$msg['type']}}}">{{{$msg['text']}}}</div>{{ endif; }}

    {{ foreach($Me->getFieldsByFieldSet() as $fieldsetname => $fieldset): }}
        {{ if ($fieldsetname[0] != '_'): }}<fieldset><legend>{{{$fieldsetname}}}</legend>{{ endif; }}
            {{ foreach($fieldset as $field): }}
                {{{$field->__toString()}}}
            {{ endforeach; }}
        {{ if ($fieldsetname[0] != '_'): }}</fieldset>{{ endif; }}
        {{ if ($fieldsetname == '_FORM_HEADER_' && count($fieldset)): }}<hr>{{ endif; }}
    {{ endforeach; }}
</form>