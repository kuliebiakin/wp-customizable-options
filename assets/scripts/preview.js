(function ($, _, wp) {

    'use strict';

    var api = wp.customize;
    var ATTRIBUTE = 'customize-preview-edit';
    var Field;

    Field = (function () {

        function Field(el) {
            this.el = el;
            this.$el = $(this.el);
            this.setting = this.$el.data(ATTRIBUTE);
            this._send = false;
            this.initialize();
            this.$el.data(ATTRIBUTE, this);
        }

        Field.prototype = {
            initialize: function () {
                api(this.setting, function (value) {
                    this._value = value;
                    this.enqueue()
                        .enable()
                        .on();
                }.bind(this));

                return this;
            },
            enable: function () {
                this.$el
                    .attr('contenteditable', 'true')
                    .attr('tabindex', '-1');

                if (!this.value()) {
                    this.$el.text('...');
                }

                return this;
            },
            disable: function () {
                this.$el
                    .removeAttr('contenteditable')
                    .removeAttr('tabindex')
                    .text(this.value());

                return this;
            },
            on: function () {
                this._value.bind(this._listeners.onCustomizeChange);
                this.$el.on('keyup', this._listeners.onPreviewChange);

                return this;
            },
            off: function () {
                this._value.unbind(this._listeners.onCustomizeChange);
                this.$el.off('keyup', this._listeners.onPreviewChange);

                return this;
            },
            value: function (newValue) {
                var value = this._value.get();

                if (typeof newValue !== 'undefined' && newValue !== null && value !== newValue) {
                    this._send = true;
                    api.preview.send(ATTRIBUTE, {
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
                    onPreviewChange: _.debounce(this.onPreviewChange.bind(this), $.fx.speeds.fast)
                };

                return this;
            },
            onCustomizeChange: function (newValue) {
                this.$el.text(newValue);

                console.log(this._send);
                if (this._send) {
                    this.focus()
                        ._send = false;
                }

                return this;
            },
            onPreviewChange: function () {
                this.value(this.$el.text());

                return this;
            },
            focus: function () {
                var range;
                var selection;

                this.el.focus();

                if (typeof window.getSelection !== 'undefined' && typeof document.createRange !== 'undefined') {
                    range = document.createRange();
                    range.selectNodeContents(this.el);
                    range.collapse(false);
                    selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);
                } else if (typeof document.body.createTextRange != 'undefined') {
                    range = document.body.createTextRange();
                    range.moveToElementText(this.el);
                    range.collapse(false);
                    range.select();
                }

                return this;
            }
        };

        return Field;
    })();

    api.bind('preview-ready', function () {
        $('[data-' + ATTRIBUTE + ']').each(function (index, el) {
            new Field(el);
        });
    });

})(jQuery, _, window.wp);