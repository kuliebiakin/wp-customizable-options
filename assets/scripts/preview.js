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
            this._caret = null;
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
                this.setDefault();

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
                this._value
                    .bind(this._listeners.onCustomizeChange);
                this.$el
                    .on('keyup cut copy paste input', this._listeners.onPreviewChange)
                    .on('blur', this._listeners.onBlur);

                return this;
            },
            off: function () {
                this._value
                    .unbind(this._listeners.onCustomizeChange);
                this.$el
                    .off('keyup cut copy paste input', this._listeners.onPreviewChange)
                    .off('blur', this._listeners.onBlur);

                return this;
            },
            value: function (newValue) {
                var value = this._value.get();

                if (typeof newValue !== 'undefined' && newValue !== null && value !== newValue) {
                    this._send = true;
                    this._caret = this.$el.caret('pos');
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
                    onPreviewChange: _.debounce(this.onPreviewChange.bind(this), $.fx.speeds.fast),
                    onBlur: this.onBlur.bind(this)
                };

                return this;
            },
            onCustomizeChange: function (newValue) {
                this.$el.text(newValue);

                if (this._send) {
                    this.focus()
                        ._send = false;
                }

                return this;
            },
            onPreviewChange: function () {
                this.save();

                return this;
            },
            onBlur: function () {
                this.save();
                this._send = false;
                this.setDefault();

                return this;
            },
            save: function () {
                this.value(this.$el.text());

                return this;
            },
            focus: function () {
                this.el.focus();

                if (this._caret !== null) {
                    this.$el.caret('pos', this._caret);
                }

                return this;
            },
            setDefault: function () {

                if (!this.value()) {
                    this.$el.text('...');
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