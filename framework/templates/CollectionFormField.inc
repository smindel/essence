<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <div class="field">

        {{{$Me->getCollectionControl()}}}

        <div class="message error">{{{$Me->getError()}}}</div>
    </div>
</div>