<form action="{{{$Me->getAction()}}}">
    {{ foreach($Me->getFields() as $field): }}
        {{{$field->html()}}}
    {{ endforeach; }}
</form>