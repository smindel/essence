(function($) {

    $.fn.autocomplete = function(options) {

        var opts = $.extend({}, $.fn.autocomplete.defaults, options);

        return this.each(function() {
            $.fn.autocomplete.init($(this), opts);
        });
    };

    $.fn.autocomplete.defaults = {
        url: function (elem) { return elem.data('autocompleteUrl'); },
        link: function (elem) { return elem.data('editUrl'); },
        label: function (elem, label) { if (label) { elem.prop('placeholder', label); } else { return elem.prop('placeholder') || ''; } },
        select: function(value, label) { console.log(value, label); }
    };

    $.fn.autocomplete.init = function(elem, options) {
        var data = children = {};

        data.label = options.label;
        data.url = typeof options.url === 'function' ? options.url(elem) : options.url;
        data.link = typeof options.link === 'function' ? options.link(elem) : options.link;
        data.select = options.select;

        children.suggestionContainer = $('<div class="autocomplete-suggestions"/>').data('parent', elem);
        children.labelField = $('<input class="autocomplete-label">').data('parent', elem).prop('placeholder', data.label(elem));
        if (!elem.val() || elem.val() == '0') children.labelField.addClass('empty');
        if (data.link) children.editButton = $('<button type="button" class="autocomplete-button"><span class="icon ' + (elem.val() && elem.val() != '0' ? 'icon-edit' : 'icon-create') + '"></span></button>').data('parent', elem);

        if (data.link) elem.after(children.editButton);
        elem.after(children.suggestionContainer);
        elem.after(children.labelField);
        elem.hide();

        if (data.link) children.editButton.on('click', null, data.link, $.fn.autocomplete.button);
        children.labelField.on('keydown', $.fn.autocomplete.keypress);
        children.labelField.on('blur', $.fn.autocomplete.blur);
        
        children.suggestionContainer.on('click', 'div', $.fn.autocomplete.click).hide();

        elem.data('children', children);
        elem.data('data', data);
    }

    $.fn.autocomplete.keypress = function(event) {

        var labelField = $(event.target);
        var parent = labelField.data('parent');
        var siblings = parent.data('children');
        var suggestionContainer = siblings.suggestionContainer;
        var options = $('div', suggestionContainer);
        var option = $('.candidate', suggestionContainer);
        var index;
        var url = parent.data('data').url;

        if (option.length) index = option.prevAll().length + 1;

        switch (event.keyCode) {

            case 13:
                if (option.length) $.fn.autocomplete.select(option);
                event.preventDefault();
                return false;

            case 27:
                labelField.blur();
                event.preventDefault();
                return false;

            case 38:
                if (typeof index === 'undefined') {
                    index = options.length;
                } else if (index > 1) {
                    option.removeClass('candidate');
                    index--;
                }
                option = $('div:nth-of-type(' + index + ')', suggestionContainer);
                option.addClass('candidate');
                event.preventDefault();
                return false;

            case 40:
                if (typeof index === 'undefined') {
                    index = 1;
                } else if (index < options.length) {
                    option.removeClass('candidate');
                    index++;
                }
                option = $('div:nth-of-type(' + index + ')', suggestionContainer);
                option.addClass('candidate');
                event.preventDefault();
                return false;

            default:
                window.setTimeout(function(){
                    $.getJSON(url + encodeURIComponent(labelField.val()), function (data, status) {
                        siblings.suggestionContainer.empty().show();
                        for(var i = 0; i < data.length; i++) {
                            optionvalue = typeof data[i].value === 'undefined' ? '' : ' data-value="' + data[i].value + '"';
                            siblings.suggestionContainer.append('<div' + optionvalue + '>' + data[i].label + '</div>');
                        }
                    });
                }, 250);
        }
    }

    $.fn.autocomplete.click = function(event) {
        console.log('click');
        var newOption = $(event.target);
        var suggestionContainer = newOption.parent();
        var parent = suggestionContainer.data('parent');
        var siblings = parent.data('children');
        var options = $('div', suggestionContainer);
        var oldOption = $('.candidate', suggestionContainer);
        oldOption.removeClass('candidate');
        newOption.addClass('candidate');
        $.fn.autocomplete.select(newOption);
        return false;
    }

    $.fn.autocomplete.blur = function(event) {
        console.log(event);
        var labelField = $(event.target);
        var parent = labelField.data('parent');
        var siblings = parent.data('children');
        window.setTimeout(function(){
            console.log('BOOOM !!');
            labelField.val('');
            siblings.suggestionContainer.empty().hide();
        }, 250);
    }

    $.fn.autocomplete.select = function(option) {
        console.log('select');
        var value = option.data('value')
        var label = option.text();
        var suggestionContainer = option.parent();
        var parent = suggestionContainer.data('parent');
        var siblings = parent.data('children');
        var labelField = siblings.labelField;
        var editButton = siblings.editButton;
        if (typeof value !== 'undefined') {
            parent.data('data').label(parent, label);
            parent.val(value);
            parent.data('data').select(value, label);

            labelField.text('').prop('placeholder', label).blur();
            if (value && value != '0') {
                labelField.removeClass('empty');
                if (editButton) $('span', editButton).removeClass('icon-create').addClass('icon-edit');
            } else {
                labelField.addClass('empty');
                if (editButton) $('span', editButton).removeClass('icon-edit').addClass('icon-create');
            }
        }
    }

    $.fn.autocomplete.button = function(event) {
        var button = $(event.target);
        var parent = button.data('parent');
        var value = parent.val();
        var link = event.data + parent.val()
        document.location.href = link;
        return false;
    }

    $('.autocomplete').autocomplete();

}(jQuery));