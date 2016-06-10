(function ($, settings) {
    
    'use strict';
    
    var api = wp.customize;
    var ATTRIBUTE = 'customize-preview-edit';
    
    $(function () {
        console.log(api.settings);
        api.previewer.bind(ATTRIBUTE, function (data) {
            var section = api.section(api.control(data.setting).section());
            
            if (!section.expanded()) {
                section.expand();
            }
            api(data.setting).set(data.value);
        });
    });

})(jQuery, window.CustomizableOptions);