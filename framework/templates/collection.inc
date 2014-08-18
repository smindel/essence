{{ Resources::add("framework/resources/jquery-2.1.1.min.js", 100); }}
{{ Resources::add("framework/resources/collection.js", 10); }}
{{ Resources::add("framework/resources/collection.css"); }}
<table class="collection">
    <tbody>
        {{ if ($class): }}
            <tr id="{{{$class}}}[0]">
                <td>
                    <a href="{{{$link(0)}}}">
                        new {{{$class}}}
                    </a>
                </td>
            </tr>
        {{ endif; }}
        {{ foreach ($values as $option): }}
            <tr id="{{{$class}}}[{{{$option->id}}}]"><td><a href="{{{$link($option->id)}}}">{{{$option->title()}}}</a></td></tr>
        {{ endforeach; }}
    </tbody>
</table>