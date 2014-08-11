window.onload = function() {

    var find = function (selector, parent) {
        parent = parent || document;
        switch (selector.substr(0,1)) {
            case '#': return document.getElementById(selector.substr(1)) ? [document.getElementById(selector.substr(1))] : [];
            case '.': return parent.getElementsByClassName(selector.substr(1));
            default:  return parent.getElementsByTagName(selector);
        }
    }

    var closest = function (element, selector) {
        switch (selector.substr(0,1)) {
            case '#': if (element.id == selector.substr(1)) return element; break;
            case '.':
                var classnames = element.className.split(' ');
                for(var i = 0; i < classnames.length; i++) {
                    if(classnames[i] == selector.substr(1)) return element;
                }
                break;
            default:  return element; break;
        }
        return element.parentElement ? closest(element.parentElement, selector) : undefined;
    }

    var bind = function (elements, eventname, handler) {
        for(var i = 0; i < elements.length; i++) {
            elements[i].addEventListener(eventname, handler, false);
        }
    }

    function ajax(url, callback) {
        var xmlhttp = new XMLHttpRequest();

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                if(xmlhttp.status == 200){
                    callback(xmlhttp.responseText);
                } else {
                    callback(false);
                }
            }
        }

        xmlhttp.open('GET', url, true);
        xmlhttp.setRequestHeader('x-requested-with', 'XmlHttpRequest');
        xmlhttp.send();
    }

    var autocomplete = function(event) {
        var target = event.target;
        var container = closest(target, '.fieldholder');
        if (event.type == 'blur') {
            window.setTimeout(function(){
                find('.autocomplete-display', container)[0].value = '';
                find('.autocomplete-suggestions', container)[0].className = 'autocomplete-suggestions empty';
            }, 300);
            event.preventDefault();
            return false;
        }
        var options = find('.autocomplete-option', container);
        var option = 0;
        if (event.type == 'click') {
            for (var i = 0; i < options.length; i++) {
                if (options[i] == event.target) {
                    option = i + 1;
                    options[i].className = 'autocomplete-option highlight';
                } else {
                    options[i].className = 'autocomplete-option';
                }
            }
        } else {
            bind([target], 'blur', autocomplete);
            for (var i = 0; i < options.length; i++) if (options[i].className == 'autocomplete-option highlight') option = i + 1;
        }
        if (event.keyCode == 13 || event.type == 'click') {
            var selected = options[option - 1];
            var value = selected.getAttribute('data-id');
            var label = selected.textContent;

            find('.autocomplete-value', container)[0].value = value;
            find('.autocomplete-display', container)[0].value = '';
            find('.autocomplete-display', container)[0].setAttribute('placeholder', label);
            find('.autocomplete-link', container)[0].setAttribute('href', find('.autocomplete-link', container)[0].getAttribute('data-link-base') + value);
            find('.autocomplete-suggestions', container)[0].className = 'autocomplete-suggestions empty';

            console.log([value, label]);
            event.preventDefault();
            return false;
        } else if (event.keyCode == 38) {
            if (option > 1) {
                if (option) options[option - 1].className = 'autocomplete-option';
                option--;
                options[option - 1].className = 'autocomplete-option highlight';
            }
            event.preventDefault();
            return false;
        } else if (event.keyCode == 40) {
            if (options.length > option) {
                if (option) options[option - 1].className = 'autocomplete-option';
                option++;
                options[option - 1].className = 'autocomplete-option highlight';
            }
            event.preventDefault();
            return false;
        }
        window.setTimeout(function() {
            var url = target.getAttribute('data-url');
            var suggestionscontainer = document.getElementById(target.getAttribute('data-suggestions-id'));
            var encodedvalue = encodeURIComponent(target.value)
            ajax(url + encodedvalue, function (text) {
                suggestionscontainer.innerHTML = text;
                suggestionscontainer.setAttribute('class', 'autocomplete-suggestions');
                bind(find('.autocomplete-option'), 'click', autocomplete);
            });
        }, 50);
    }

    bind(find('.autocomplete'), 'keypress', autocomplete);
}