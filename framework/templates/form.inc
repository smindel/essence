<form action="{{{$Me->action()}}}">
    {{ foreach($Me->getFields() as $field): }}
        {{{$field->html()}}}
    {{ endforeach; }}
</form>