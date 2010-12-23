/*
 * jquery-ujs
 *
 * copied and modified from http://github.com/rails/jquery-ujs/blob/master/src/rails.js
 *
 * This file supports jQuery 1.4.3 and 1.4.4 .
 *
 */

jQuery(function ($) {
    var csrf_token = $('meta[name=csrf-token]').attr('content');

    $.fn.extend({
        triggerAndReturn: function (name, data) {
            var event = new $.Event(name);
            this.trigger(event, data);

            return event.result !== false;
        },

        
    });

    /**
     *  confirmation handler
     */
    $('body').delegate('a[data-confirm], button[data-confirm], input[data-confirm]', 'click.rails', function () {
        var el = $(this);
        if (el.triggerAndReturn('confirm')) {
            if (!confirm(el.attr('data-confirm'))) {
                return false;
            }
        }
    });


    $('a[data-method]:not([data-remote])').live('click.rails', function (e){
        var link = $(this),
            href = link.attr('href'),
            method = link.attr('data-method'),
            form = $('<form method="post" action="'+href+'"></form>'),
            metadata_input = '<input name="_method" value="'+method+'" type="hidden" />';
        if (csrf_token !== undefined) {
            metadata_input += '<input name="_token" value="'+csrf_token+'" type="hidden" />';
        }

        form.hide()
            .append(metadata_input)
            .appendTo('body');

        e.preventDefault();
        form.submit();
    });


    var jqueryVersion = $().jquery;

	if (!( (jqueryVersion === '1.4.3') || (jqueryVersion === '1.4.4'))){
		alert('This adapter does not support the jQuery version you are using.');
	}


});