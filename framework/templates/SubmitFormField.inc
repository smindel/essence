<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}"></label>
    <div class="field">
        <button type="{{{$Me->getHtmlType()}}}" name="{{{$Me->getFullName()}}}" value="{{{$Me->getLabel()}}}">{{{$Me->getLabel()}}}</button>
    </div>
</div>