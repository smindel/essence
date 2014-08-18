<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <div class="field">

        {{{$Me->getAutocompleteControl()}}}

        <div class="message error">{{{$Me->getError()}}}</div>
    </div>
</div>