<form action="{{{$Me->action()}}}">
    <input name="SecurityID" value="andy" type="hidden">
    {{ foreach($Me->fields as $field): }}
        {{{$field}}}
    {{ endforeach; }}
</form>