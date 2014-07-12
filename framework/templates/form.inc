<form action="{{{$Me->getAction()}}}" name="{{{$Me->getName()}}}">
    {{ foreach($Me->getFields() as $field): }}
        {{{$field->html()}}}
    {{ endforeach; }}
</form>