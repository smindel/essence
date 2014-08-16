<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <div class="field">

        {{ require($this->compile('autocomplete')); }}

        <div class="message error">{{{$Me->getError()}}}</div>
    </div>
</div>