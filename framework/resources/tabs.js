(function($) {

    $.fn.tabs = function(options) {

        var opts = $.extend({}, $.fn.tabs.defaults, options);

        return this.each(function() {
            $.fn.tabs.init($(this), opts);
        });
    };

    $.fn.tabs.defaults = {
    };

    $.fn.tabs.init = function(base, options) {
        var data = children = {};

        children.form = base.closest('form');
        children.tabsets = $('fieldset', children.form);

        if (children.tabsets.length < 2) return;

        children.tabsets.each(function(){
            var tab = $('<a class="tab" href="' + document.location.href + '#' + $(this).data('name') + '" data-tabset="' + $(this).data('name') + '">' + $(this).data('name') + '</a>');
            $(this).data('tab', tab);
            tab.data('tabset', $(this));
            base.append(tab);
            tab.on('click', $.fn.tabs.click);
            $(this).hide();
        });

        children.tabsets.first().data('tab').click();

        base.data('children', children);
        base.data('data', data);
    };

    $.fn.tabs.click = function(event) {
        var tab = $(event.target);
        var form = tab.closest('form');
        $('fieldset', form).hide();
        $('a.tab', form).removeClass('selected');
        tab.addClass('selected').data('tabset').show();
    };

    $('.tab-base').tabs();

}(jQuery));