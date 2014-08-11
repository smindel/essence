{{ if ($Me->canSetNull()): }}
    <div class="autocomplete-option" data-id="0">no {{{$Me->getClass()}}}</div>
{{ elseif (!count($suggestions)): }}
    <div>nothing</div>
{{ endif; }}
{{ foreach ($suggestions as $id => $title): }}
    <div class="autocomplete-option" data-id="{{{$id}}}">{{{$title}}}</div>
{{ endforeach; }}