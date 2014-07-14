<ul>
    {{foreach ($Me as $item):}}
        <li class="{{{$item['Status']}}}">
            <a href="{{{$item['Link']}}}">{{{$item['Title']}}}</a>
            {{if ($item['Status'] != 'link' && $item['Menu']):}}
                <ul>
                    {{foreach ($item['Menu'] as $subitem):}}
                        <li class="{{{$subitem['Status']}}}">
                            <a href="{{{$subitem['Link']}}}">{{{$subitem['Title']}}}</a>
                        </li>
                    {{endforeach;}}
                </ul>
            {{endif;}}
        </li>
    {{endforeach;}}
</ul>