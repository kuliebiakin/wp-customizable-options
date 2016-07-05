(function ($, settings) {
    
    'use strict';
    
    var api = wp.customize;
    
    $(function () {
        var templates = {
            text: wp.template('customize-control-' + settings.key + '-text')
        };
        var dynamicSettingsKey = '_' + settings.key;
        var dynamicSettings = api.create(dynamicSettingsKey, dynamicSettingsKey, [], {
            transport: settings.setting.default.transport,
            previewer: api.previewer,
            dirty: true
        });

        api.previewer
            .bind(settings.key + ':add', function (data) {
                var control = api.control(data.setting);

                if (typeof control === 'undefined') {
                    if (!data.label) {
                        data.label = data.setting[0].toUpperCase() + data.setting.slice(1);
                        data.label = data.label.replace(/_+/g, ' ');
                    }
                    api.create(data.setting, data.setting, data.value, {
                        transport: settings.setting.default.transport,
                        previewer: api.previewer,
                        dirty: !!data.dirty
                    });
                    control = new api.Control(data.setting, {
                        params: {
                            content: templates.text(data),
                            section: settings.key,
                            label: data.label,
                            settings: {
                                'default': data.setting
                            }
                        },
                        previewer: api.previewer
                    });
                    api.control.add(data.setting, control);
                    dynamicSettings.get().push(data.setting);
                    /**
                     * @TODO: section should be visible w/o this hook
                     */
                    api.bind('pane-contents-reflowed', function () {
                        api.section(control.section()).container.show();
                    })
                }
            })
            .bind(settings.key + ':edit', function (data) {
                var section = api.section(api.control(data.setting).section());

                if (!section.expanded()) {
                    section.expand();
                }
                api(data.setting).set(data.value);
            });
    });

})(jQuery, window.CustomizableOptions);