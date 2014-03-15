(function($) {

    $.fn.e_autocomplete = function($hidden, options) {

        options = $.extend({
            discard: true,
            depends: undefined,
            item_selected: function() {},
            dependency_unmet: function() {
                options.depends.valid();
            },
            select: function(event, ui) {
                $hidden.val(ui.item.id);
                $hidden.data("label", ui.item.label);
                $(this).data("autocomplete.item", ui.item);
                options.item_selected.call($(this), event, ui);
            }
        }, options);

        return $(this).each(function() {
            var $this = $(this);
            $this.blur(function() {
                if(options.discard && $this.val() != $hidden.data("label")) {
                    $this.val("");
                    $this.data("autocomplete.item", null);
                    $hidden.val(undefined);
                    $hidden.data("label", null);
                }
            });
            $this.autocomplete(options);
            $this.autocomplete('option', 'search', function(event, ui) {
                if(options.depends && options.depends.val() == "") {
                    options.dependency_unmet.call($this, event, ui);
                    return false;
                }
            });
            $this.bind('clear_autocomplete', function() {
                setTimeout(function() {
                    $this.val("");
                    $this.data("autocomplete.item", null);
                    $hidden.val(undefined);
                    $hidden.data("label", null);
                }, 100);
            });
        });
    };

})(jQuery);