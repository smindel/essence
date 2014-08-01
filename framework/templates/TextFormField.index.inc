<div class="field {{{get_class($Me)}}}">
    <div class="error">{{{$Me->getError()}}}</div>
    <label for="{{{$Me->name}}}">{{{$Me->label}}}</label>
    {{ if ($Me->getRows() == 1): }}
        <input type="{{{$Me->getHtmlType()}}}" id="{{{$Me->getName()}}}" name="{{{$Me->getFullName()}}}" value="{{{$this->getValue()}}}">
    {{ else; }}
        <textarea id="{{{$Me->getName()}}}" name="{{{$Me->getFullName()}}}" rows="{{{$Me->getRows()}}}">{{{$Me->getValue()}}}</textarea>";
    {{ endif; }}
</div>