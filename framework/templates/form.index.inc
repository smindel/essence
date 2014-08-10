<form class="widget light" action="{{{$Me->getAction()}}}" name="{{{$Me->getName()}}}" method="post">
    {{ if(($msg = $Me->getMessage())): }}<div class="message {{{$msg['type']}}}">{{{$msg['text']}}}</div>{{ endif; }}
    {{ foreach($Me->getFields() as $field): }}
        {{{$field->__toString()}}}
    {{ endforeach; }}
</form>