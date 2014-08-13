<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <div class="field">
        {{ if ($Me->getRows() == 1): }}
            <input type="{{{$Me->getHtmlType()}}}" id="{{{$Me->getName()}}}" name="{{{$Me->getFullName()}}}" value="{{{$Me->getValue()}}}"{{ if($Me->getRequired()): }} required{{ endif; }}>
        {{ else: }}
            <textarea id="{{{$Me->getName()}}}" name="{{{$Me->getFullName()}}}" rows="{{{$Me->getRows()}}}"{{ if($Me->getRequired()): }} required{{ endif; }}>{{{$Me->getValue()}}}</textarea>
        {{ endif; }}
        <div class="message error">{{{$Me->getError()}}}</div>
    </div>
</div>