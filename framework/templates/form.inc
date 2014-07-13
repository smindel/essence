<form action="{{{$Me->getAction()}}}" name="{{{$Me->getName()}}}" data-fyi-url="{{{$Me->currentLink()}}}">
    {{ foreach($Me->getFields() as $field): }}
        {{{$field->html()}}}
    {{ endforeach; }}
</form>