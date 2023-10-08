+ function($) {
    'use strict';

    class Imessage {
        constructor(element, options) {
            this.$element = $(element);
            this.options = options;

            console.log(this);
        }

        // Các key cấu hình mặc định
        static DEFAULTS = {
            groupchat: 0,
            typeshow: 'button',
            autoshow: 1,
            align: 'right',
            offsetx: 15,
            offsety: 15
        }
    }

    function Plugin(option) {
        return this.each(function() {
            var $this = $(this);
            var options = $.extend({}, Imessage.DEFAULTS, $this.data(), typeof option == 'object' && option);
            var data = $this.data('imessage');
            if (!data) {
                $this.data('imessage', (data = new Imessage(this, options)));
            }
            if (typeof option == 'string') {
                data[option]();
            }
        });
    }

    var old = $.fn.imessage;

    $.fn.imessage = Plugin;
    $.fn.imessage.Constructor = Imessage;

    $.fn.imessage.noConflict = function() {
        $.fn.validate = old;
        return this;
    }

    // Tự động init trên các thẻ có data-imessage
    $(document).ready(function() {
        $('[data-toggle="imessage"]').each(function() {
            var $element = $(this);
            Plugin.call($element, $element.data());
        });
    });
}(jQuery);
