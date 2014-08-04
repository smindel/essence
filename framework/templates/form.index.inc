<form class="widget light" action="{{{$Me->getAction()}}}" name="{{{$Me->getName()}}}" method="post">
    {{ foreach($Me->getFields() as $field): }}
        {{{$field->__toString()}}}
    {{ endforeach; }}
</form>