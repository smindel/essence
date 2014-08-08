window.onload = function() {

    var onafterselect = function(event) {
        var target = event.target;
        var value = target.value;
        var listid = target.getAttribute('list');
        var list = document.getElementById(listid);
        var options = list.children;
        var label = null;
        var datafieldname = target.getAttribute('data-data-field-name');
        var datafield = document.getElementsByName(datafieldname)[0];
        var editlinkid = target.getAttribute('data-edit-link-id');
        var editlink = document.getElementById(editlinkid);
        var editurl = null;
        for (var i = 0; i < options.length; i++) if (options[i].value == value) {
            label = options[i].textContent;
            editurl = options[i].getAttribute('data-edit-url');
        }

        datafield.setAttribute('value', value);
        datafield.value = value;
        target.setAttribute('placeholder', label);
        target.value = '';
        editlink.setAttribute('href', editurl);
        editlink.textContent = editurl ? 'edit' : '';
    }

    var fields = document.getElementsByClassName('HasOneFormFieldOptions');

    for(var i = 0; i < fields.length; i++) {
        fields[i].addEventListener('select', onafterselect, false);
    }
}