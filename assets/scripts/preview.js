(function ($, _, settings) {

    'use strict';

    var api = wp.customize;
    var Field;
    var $doc = $('html, body');
    var $window = $(window);

    Field = (function () {

        function Field(el) {
            this.el = el;
            this.$el = $(this.el);
            this.setting = this.$el.data(settings.key);
            this._value = null;
            this.ready();
            this.$el.data(settings.key, this);
        }

        Field.prototype = {
            ready: function () {
                api.bind('preview-ready', this.initialize.bind(this));

                return this;
            },
            initialize: function () {
                api(this.setting, function (value) {
                    if (this._value === null) {
                        this._value = value;
                        this.enqueue()
                            .enable()
                            .on();
                    }
                }.bind(this));
                
                if (this._value === null) {
                    this.add();
                }

                return this;
            },
            add: function () {
                api.preview
                    .bind('sync', function () {
                        var value = {};

                        value[this.setting] = this.$el.text();
                        api.preview.send(settings.key + ':add', {
                            setting: this.setting,
                            value: value[this.setting]
                        });
                        api.preview.trigger('settings', $.extend(api.settings.values, value));
                        this.initialize();
                        this.$el.text(this._value.get());
                    }.bind(this));

                return this;
            },
            enable: function () {
                this.$edit = $('<span class="dashicons dashicons-edit" style="cursor: pointer; font-size: inherit;" />');
                this.$el
                    .attr('contenteditable', 'true')
                    .attr('tabindex', '-1')
                    .after(this.$edit);
                this.reset();

                return this;
            },
            disable: function () {
                this.$el
                    .removeAttr('contenteditable')
                    .removeAttr('tabindex')
                    .text(this.value());
                this.$edit.remove();
                this.$edit = null;

                return this;
            },
            on: function () {
                this._value
                    .bind(this._listeners.onCustomizeChange);
                this.$el
                    .on('keyup cut copy paste input', this._listeners.onPreviewChange)
                    .on('blur', this._listeners.onBlur);
                this.$edit
                    .on('click', this._listeners.onEditClick);

                return this;
            },
            off: function () {
                this._value
                    .unbind(this._listeners.onCustomizeChange);
                this.$el
                    .off('keyup cut copy paste input', this._listeners.onPreviewChange)
                    .off('blur', this._listeners.onBlur);
                this.$edit
                    .off('click', this._listeners.onEditClick);

                return this;
            },
            value: function (newValue) {
                var value = this._value.get();

                if (typeof newValue !== 'undefined' && newValue !== null && value !== newValue) {
                    this._caret = this.$el.caret('pos');
                    this.$el.off('blur', this._listeners.onBlur);
                    api.preview.send(settings.key + ':edit', {
                        setting: this.setting,
                        value: newValue
                    });
                }

                return value;
            },
            toString: function () {
                return this.setting;
            },
            enqueue: function () {
                this._listeners = {
                    onCustomizeChange: this.onCustomizeChange.bind(this),
                    onPreviewChange: _.debounce(this.onPreviewChange.bind(this), $.fx.speeds.fast),
                    onBlur: this.onBlur.bind(this),
                    onEditClick: this.onEditClick.bind(this)
                };

                return this;
            },
            onCustomizeChange: function (newValue) {
                this.$el.text(newValue);
                
                if (this._caret !== null) {
                    this.focus();
                } else {
                    this.reset();
                }

                return this;
            },
            onPreviewChange: function () {
                this.save();

                return this;
            },
            onBlur: function () {
                this.save()
                    .reset();

                return this;
            },
            onEditClick: function () {
                this.focus();

                return this;
            },
            save: function () {
                this.value(this.$el.text());

                return this;
            },
            focus: function () {
                this.el.focus();
                this.$el.caret('pos', this._caret);
                this._caret = null;
                this.$el.on('blur', this._listeners.onBlur);

                return this;
            },
            scroll: function () {
                /**
                 * @TODO: refactor
                 */
                var height = this.$el.outerHeight();
                var windowHeight = $window.height();
                var margin = height > windowHeight ? 0 : (windowHeight - height) / 2;

                $doc.animate({
                    scrollTop: this.$el.offset().top - margin
                }, 'fast', function () {
                    $doc.off('scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove');
                });

                return this;
            },
            reset: function () {
                this._caret = null;

                if (!this.value()) {
                    this.$el.text(this.placeholder());
                }

                return this;
            },
            placeholder: function () {
                return '...';
            }
        };

        return Field;
    })();

    $('[data-' + settings.key + ']').each(function (index, el) {
        new Field(el);
    });
    
})(jQuery, _, window.CustomizableOptions);