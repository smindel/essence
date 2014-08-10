<div class="field {{{get_class($Me)}}}">
    <div class="message error">{{{$Me->getError()}}}</div>
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <input type="{{{$Me->getHtmlType()}}}" id="{{{$Me->getName()}}}" name="{{{$Me->getFullName()}}}" data-fyi-url="{{{$Me->currentLink()}}}">
</div>