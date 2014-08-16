<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}">{{{$Me->getLabel()}}}</label>
    <div class="field">

        {{ require($this->compile('collection')); }}

        {{ if ($Me->getClass()): }}
            {{ require($this->compile('autocomplete')); }}
        {{ endif; }}

        <div class="message error">{{{$Me->getError()}}}</div>
    </div>
</div>