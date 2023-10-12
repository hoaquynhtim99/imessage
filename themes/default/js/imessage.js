+ function($) {
    'use strict';

    class Imessage {
        constructor(element, options) {
            this.$element = $(element);
            this.options = options;

            this.boxOpen = 0;
            this.timerShowBox = 0;
            this.timerCheckScreen = 0;

            this.init();
        }

        // Các key cấu hình mặc định
        static DEFAULTS = {
            groupchat: 0,
            typeshow: 'button',
            autoshow: 1,
            align: 'right',
            offsetx: 15,
            offsety: 15,
            zindex: 9999999999,
            btnWidth: 60,
            btnHeight: 60,
            btnBackground: '#5cb85c',
            btnPadding: 16,
            btnColor: '#ffffff',
            autoShowTimeout: 5000,
        }

        #templateBtn = '\
        <div class="imessage-chat-btn">\
            <a href="">\
                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3 0 0 0 0 0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9zM128 208a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm128 0a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm96 32a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>\
            </a>\
        </div>\
        <div class="imessage-chatbox imessage-chatbox-btn">HIhiii</div>';

        // Khởi tạo
        init() {
            var self = this;
            self.$element.html(self.#templateBtn);
            self.btn = self.$element.find('div:first');
            self.box = self.$element.find('div:nth-child(2)');

            // Css nút chat
            var cssBtn = [];
            cssBtn.push('background-color: ' + self.options.btnBackground);
            cssBtn.push('padding: ' + self.options.btnPadding + 'px');
            cssBtn.push('width: ' + self.options.btnWidth + 'px');
            cssBtn.push('height: ' + self.options.btnHeight + 'px');
            cssBtn.push('bottom: ' + self.options.offsety + 'px');
            cssBtn.push('z-index: ' + self.options.zindex);
            if (self.options.align == 'right') {
                cssBtn.push('right: ' + self.options.offsetx + 'px');
            } else {
                cssBtn.push('left: ' + self.options.offsetx + 'px');
            }
            self.btn.attr('style', cssBtn.join('; '));
            self.btn.find('svg').css({
                fill: self.options.btnColor
            });

            var cssBox = [];
            cssBox.push('z-index: ' + (self.options.zindex + 1));
            self.box.attr('style', cssBox.join('; '));

            this.checkScreen();

            // Nút ẩn hiện chat box
            $('a', self.btn).on('click', function(e) {
                e.preventDefault();
                if (self.timerShowBox) {
                    clearTimeout(self.timerShowBox);
                }
                self.toggleChatBox();
            });

            // Tự động show nút chat lên sau load trang
            if (self.options.typeshow == 'button' && self.options.autoshow > 0) {
                self.timerShowBox = setTimeout(function() {
                    $('a', self.btn).trigger('click');
                }, self.options.autoShowTimeout);
            }

            // Thiết lập tự động chỉnh kích thước khung chat
            $(window).on('resize', function() {
                if (self.timerCheckScreen) {
                    clearTimeout(self.timerCheckScreen);
                }
                self.timerCheckScreen = setTimeout(function() {
                    self.checkScreen();
                }, 50);
            });
        }

        // Ẩn, hiện box chat trường hợp dạng nút
        toggleChatBox() {
            var self = this;
            if (self.options.typeshow != 'button') {
                return;
            }
            if (self.boxOpen) {
                self.box.removeClass('box-open');
                self.boxOpen = 0;
            } else {
                self.box.addClass('box-open');
                self.boxOpen = 1;
            }
        }

        // Kiểm tra và chỉnh kích thước khung chat dựa vào chiều rộng màn hình
        checkScreen() {
            var self = this;
            if (self.options.typeshow != 'button') {
                return;
            }

            var boxW = 328, boxH = 455;
            self.box.css({
                'width': boxW + 'px',
                'height': boxH + 'px',
                'right': (self.options.offsetx + self.options.btnWidth + 15) + 'px',
                'bottom': '0px',
            });

            console.log($(window).width());
            console.log($(window).height());
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
