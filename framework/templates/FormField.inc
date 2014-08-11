<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <div class="field">
        <input type="{{{$Me->getHtmlType()}}}" id="{{{$Me->getName()}}}" name="{{{$Me->getFullName()}}}" value="{{{$Me->getValue()}}}" data-fyi-url="{{{$Me->currentLink()}}}">
        <div class="message error">{{{$Me->getError()}}}</div>
    </div>
</div>