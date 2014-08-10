<div class="field {{{get_class($Me)}}}">
    <div class="message error">{{{$Me->getError()}}}</div>
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    {{ if ($Me->getRows() == 1): }}
        <input type="{{{$Me->getHtmlType()}}}" id="{{{$Me->getName()}}}" name="{{{$Me->getFullName()}}}" value="{{{$Me->getValue()}}}">
    {{ else: }}
        <textarea id="{{{$Me->getName()}}}" name="{{{$Me->getFullName()}}}" rows="{{{$Me->getRows()}}}">{{{$Me->getValue()}}}</textarea>
    {{ endif; }}
</div>