{{ if ($Me->getShowHolder()): }}
<div class="fieldholder {{{get_class($Me)}}}">
    <label for="{{{$Me->getName()}}}"></label>
    <div class="field HtmlFormField">
        {{{$Me->getValue()}}}
    </div>
</div>
{{ else: }}
    {{{$Me->getValue()}}}
{{ endif; }}