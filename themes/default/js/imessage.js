+ function($) {
    'use strict';

    class Imessage {
        constructor(element, options) {
            this.$element = $(element);
            this.options = options;

            this.boxOpen = 0;
            this.timerShowBox = 0;
            this.timerCheckScreen = 0;
            this.show_emoji = 0;

            this.init();
        }

        // Các key cấu hình mặc định
        static DEFAULTS = {
            boxtitle: '',
            boxheight: 450,
            groupchat: 0,
            typeshow: 'button',
            autoshow: 1,
            align: 'right',
            offsetx: 15,
            offsety: 15,
            zindex: 2147483600,
            btnWidth: 60,
            btnHeight: 60,
            btnBackground: '#5cb85c',
            btnPadding: 16,
            btnColor: '#ffffff',
            autoShowTimeout: 5000,
            soundBuzz: nv_base_siteurl + 'themes/default/images/imessage/ding.mp3',
            soundChat: nv_base_siteurl + 'themes/default/images/imessage/message.mp3',
        }

        #templateBtn = '\
        <div class="imessage-chat-btn" data-toggle="chatbtn">\
            <a href="">\
                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 448c141.4 0 256-93.1 256-208S397.4 32 256 32S0 125.1 0 240c0 45.1 17.7 86.8 47.7 120.9c-1.9 24.5-11.4 46.3-21.4 62.9c-5.5 9.2-11.1 16.6-15.2 21.6c-2.1 2.5-3.7 4.4-4.9 5.7c-.6 .6-1 1.1-1.3 1.4l-.3 .3 0 0 0 0 0 0 0 0c-4.6 4.6-5.9 11.4-3.4 17.4c2.5 6 8.3 9.9 14.8 9.9c28.7 0 57.6-8.9 81.6-19.3c22.9-10 42.4-21.9 54.3-30.6c31.8 11.5 67 17.9 104.1 17.9zM128 208a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm128 0a32 32 0 1 1 0 64 32 32 0 1 1 0-64zm96 32a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>\
            </a>\
        </div>\
        <div class="imessage-chatbox imessage-chatbox-btn" data-toggle="boxArea"></div>';

        #templateBlock = '<div class="imessage-chatbox imessage-chatbox-block" data-toggle="boxArea"></div>';

        #templateBox = '\
            <div class="imessage-box-heading">\
                <div class="box-title" data-toggle="titlebox"></div>\
                <div class="box-tools">\
                    <a href="#" data-toggle="closebox"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></a>\
                </div>\
            </div>\
            <div class="imessage-box-body">\
                Boydy\
            </div>\
            <div class="imessage-box-footer">\
                <div class="box-funcs">\
                    <a data-toggle="chatsound" class="icon-sound" href="#">\
                        <svg class="sound-off" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M301.1 34.8C312.6 40 320 51.4 320 64V448c0 12.6-7.4 24-18.9 29.2s-25 3.1-34.4-5.3L131.8 352H64c-35.3 0-64-28.7-64-64V224c0-35.3 28.7-64 64-64h67.8L266.7 40.1c9.4-8.4 22.9-10.4 34.4-5.3zM425 167l55 55 55-55c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-55 55 55 55c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-55-55-55 55c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l55-55-55-55c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0z"/></svg>\
                        <svg class="sound-on" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 640 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M533.6 32.5C598.5 85.3 640 165.8 640 256s-41.5 170.8-106.4 223.5c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C557.5 398.2 592 331.2 592 256s-34.5-142.2-88.7-186.3c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zM473.1 107c43.2 35.2 70.9 88.9 70.9 149s-27.7 113.8-70.9 149c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C475.3 341.3 496 301.1 496 256s-20.7-85.3-53.2-111.8c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zm-60.5 74.5C434.1 199.1 448 225.9 448 256s-13.9 56.9-35.4 74.5c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C393.1 284.4 400 271 400 256s-6.9-28.4-17.7-37.3c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zM301.1 34.8C312.6 40 320 51.4 320 64V448c0 12.6-7.4 24-18.9 29.2s-25 3.1-34.4-5.3L131.8 352H64c-35.3 0-64-28.7-64-64V224c0-35.3 28.7-64 64-64h67.8L266.7 40.1c9.4-8.4 22.9-10.4 34.4-5.3z"/></svg>\
                    </a>\
                    <a data-toggle="chatbuzz" class="icon-ding" href="#"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M319.4 372c48.5-31.3 80.6-85.9 80.6-148c0-97.2-78.8-176-176-176S48 126.8 48 224c0 62.1 32.1 116.6 80.6 148c1.2 17.3 4 38 7.2 57.1l.2 1C56 395.8 0 316.5 0 224C0 100.3 100.3 0 224 0S448 100.3 448 224c0 92.5-56 171.9-136 206.1l.2-1.1c3.1-19.2 6-39.8 7.2-57zm-2.3-38.1c-1.6-5.7-3.9-11.1-7-16.2c-5.8-9.7-13.5-17-21.9-22.4c19.5-17.6 31.8-43 31.8-71.3c0-53-43-96-96-96s-96 43-96 96c0 28.3 12.3 53.8 31.8 71.3c-8.4 5.4-16.1 12.7-21.9 22.4c-3.1 5.1-5.4 10.5-7 16.2C99.8 307.5 80 268 80 224c0-79.5 64.5-144 144-144s144 64.5 144 144c0 44-19.8 83.5-50.9 109.9zM224 312c32.9 0 64 8.6 64 43.8c0 33-12.9 104.1-20.6 132.9c-5.1 19-24.5 23.4-43.4 23.4s-38.2-4.4-43.4-23.4c-7.8-28.5-20.6-99.7-20.6-132.8c0-35.1 31.1-43.8 64-43.8zm0-144a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg></a>\
                </div>\
                <div class="box-text">\
                    <textarea placeholder="Aa"></textarea>\
                    <a data-toggle="showemojis" class="icon-emoji" href="#"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM164.1 325.5C182 346.2 212.6 368 256 368s74-21.8 91.9-42.5c5.8-6.7 15.9-7.4 22.6-1.6s7.4 15.9 1.6 22.6C349.8 372.1 311.1 400 256 400s-93.8-27.9-116.1-53.5c-5.8-6.7-5.1-16.8 1.6-22.6s16.8-5.1 22.6 1.6zM144.4 208a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm192-32a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/></svg></a>\
                </div>\
            </div>\
        ';

        #templateEmojis = '<div class="imessage-emojis-panel" data-toggle="panelemojis">\
            <div class="emoji-body"></div>\
            <svg height="12" viewBox="0 0 21 12" width="21" class="emoji-icon"><path d="M20.685.12c-2.229.424-4.278 1.914-6.181 3.403L5.4 10.94c-2.026 2.291-5.434.62-5.4-2.648V.12h20.684z"></path></svg>\
        </div>';

        // Khởi tạo
        init() {
            var self = this;
            var cssMode = 'imessage-light-mode';

            self.chatid = nv_randomPassword(8);
            self.$element.addClass(cssMode);
            if (self.options.typeshow == 'button') {
                self.$element.html(self.#templateBtn);
            } else {
                self.$element.html(self.#templateBlock);
            }
            $('[data-toggle="boxArea"]', self.$element).html(self.#templateBox);
            self.btn = $('[data-toggle="chatbtn"]', self.$element);
            self.box = $('[data-toggle="boxArea"]', self.$element);
            self.textbox = $('textarea', self.$element);
            self.btn_toggle_sound = $('[data-toggle="chatsound"]', self.$element);
            self.btn_buzz = $('[data-toggle="chatbuzz"]', self.$element);
            self.btn_emoji = $('[data-toggle="showemojis"]', self.$element);

            // Emoji panel
            $('body').append('<div id="' + self.chatid + '-emojis" class="' + cssMode + '">' + self.#templateEmojis + '</div>');
            self.panel_emojis = $('[data-toggle="panelemojis"]', '#' + self.chatid + '-emojis');
            var css = [];
            css.push('z-index: ' + (self.options.zindex + 2));
            self.panel_emojis.attr('style', css.join('; '));

            // Âm thanh
            self.sound_buzz = new Audio(self.options.soundBuzz);
            self.sound_chat = new Audio(self.options.soundChat);

            // Css nút chat
            if (self.options.typeshow == 'button') {
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
            }

            var cssBox = [];
            cssBox.push('z-index: ' + (self.options.zindex + 1));
            self.box.attr('style', cssBox.join('; '));
            this.checkScreen();

            // Nút ẩn hiện chat box
            $('a', self.btn).on('click', function(e) {
                e.preventDefault();
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

            // Đóng cửa sổ
            $('[data-toggle="closebox"]', self.$element).on('click', function(e) {
                e.preventDefault();
                self.toggleChatBox();
            });

            // Tiêu đề cửa sổ
            $('[data-toggle="titlebox"]', self.$element).html(self.options.boxtitle);

            // Xử lý khi nhập nội dung chat
            self.textbox.on('keyup paste', function(event) {
                self.checkKeyboard(event);
            });

            // Bật tắt âm thanh chat
            self.updateSoundState();
            self.btn_toggle_sound.on('click', function(e) {
                e.preventDefault();
                self.toggleSound();
            });
            self.showSoundState();

            // Gửi buzz
            self.btn_buzz.on('click', function(e) {
                e.preventDefault();
                self.buzz();
            });

            // Ẩn hiện emoji
            self.btn_emoji.on('click', function(e) {
                e.preventDefault();
                self.toggleEmojis();
            });
        }

        // FIXME
        // Gửi chat
        postChat(message) {
            console.log(message);
        }

        // Ẩn hiện emojis
        toggleEmojis() {
            var self = this;
            console.log('toggleEmojis');
        }

        // Gửi buzz
        buzz() {
            var self = this;
            if (self.enable_sound) {
                self.sound_buzz.play();
            }
            self.postChat('[ding]');
        }

        // Lấy trạng thái bật/tắt âm thanh và hiển thị
        updateSoundState() {
            var self = this;
            self.enable_sound = nv_getCookie('imessage_sound');
            if (self.enable_sound === null || self.enable_sound == 'enable') {
                self.enable_sound = 1;
            } else {
                self.enable_sound = 0;
            }
            self.showSoundState();
        }

        // Bật tắt âm thanh chat
        toggleSound() {
            var self = this;
            self.enable_sound = self.enable_sound ? 0 : 1;
            nv_setCookie('imessage_sound', self.enable_sound ? 'enable' : 'disable', 365);
            self.showSoundState();

            // Gọi lệnh cập nhật trạng thái chat trên tất cả các box chat trên site
            $('[data-toggle="imessage"]').each(function() {
                $(this).data('imessage').updateSoundState();
            });
        }

        // Hiển thị icon sound theo trạng thái
        showSoundState() {
            var self = this;
            if (self.enable_sound) {
                $('.sound-off', self.btn_toggle_sound).hide();
                $('.sound-on', self.btn_toggle_sound).show();
                return;
            }
            $('.sound-on', self.btn_toggle_sound).hide();
            $('.sound-off', self.btn_toggle_sound).show();
        }

        // Ẩn, hiện box chat trường hợp dạng nút
        toggleChatBox() {
            var self = this;
            if (self.options.typeshow != 'button') {
                return;
            }
            if (self.timerShowBox) {
                clearTimeout(self.timerShowBox);
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
                self.box.css({
                    height: self.options.boxheight + 'px'
                });
                return;
            }
            var boxDefaultW = 328, boxDefaultH = 455;
            var boxW = 328, boxH = 455, winW = $(window).width(), winH = $(window).height();
            var boxXOffset = 15, boxYOffset = 15;
            var css = {};

            if (winH > boxH && winW > (self.options.offsetx + self.options.btnWidth + boxXOffset + boxW)) {
                css.bottom = '0px';
                if (self.options.align == 'right') {
                    css.left = 'inherit';
                    css.right = (self.options.offsetx + self.options.btnWidth + boxXOffset) + 'px';
                } else {
                    css.left = (self.options.offsetx + self.options.btnWidth + boxXOffset) + 'px';
                    css.right = 'inherit';
                }
                self.box.removeClass('box-round-bottom');
                boxW = boxDefaultW;
                boxH = boxDefaultH;
            } else if (winW > (boxW + self.options.offsetx) && winH > (self.options.offsety + self.options.btnWidth + boxYOffset + boxH)) {
                css.bottom = (self.options.offsety + self.options.btnWidth + boxYOffset) + 'px';
                if (self.options.align == 'right') {
                    css.left = 'inherit';
                    css.right = (self.options.offsetx) + 'px';
                } else {
                    css.left = (self.options.offsetx) + 'px';
                    css.right = 'inherit';
                }
                self.box.addClass('box-round-bottom');
                boxW = boxDefaultW;
                boxH = boxDefaultH;
            } else {
                if (boxH > winH) {
                    boxH = winH - 5;
                }
                boxW = winW;
                css.bottom = '0px';
                css.left = '0px';
                css.right = '0px';
                self.box.removeClass('box-round-bottom');
            }
            css.width = boxW + 'px';
            css.height = boxH + 'px';
            self.box.css(css);
        }

        // Xử lý khi nhập nội dung chat
        checkKeyboard(event) {
            var self = this;
            var text = trim(self.textbox.val());
            if (text.length > 0) {
                self.box.addClass('imessage-is-typing');
            } else {
                self.box.removeClass('imessage-is-typing');
            }
            var maxHeight = 96, basicHeight = 16;
            var textarea = self.textbox[0];

            $(textarea).height(basicHeight);
            while (textarea.scrollHeight - textarea.clientHeight > 6 && $(textarea).height() < maxHeight) {
                $(textarea).height($(textarea).height() + 16);
            }
            if (textarea.scrollHeight - textarea.clientHeight > 6) {
                $(textarea).css({'overflow' : 'auto'});
            } else {
                $(textarea).css({'overflow' : 'hidden'});
            }

            // Ấn enter => gửi nội dung chat
            if (event.keyCode == 13 && !event.shiftKey) {
                // FIXME submit
            }
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
