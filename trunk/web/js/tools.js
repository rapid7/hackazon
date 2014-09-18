/**
 * Created by Nikolay Chervyakov on 18.09.2014.
 */

(function ($) {
    window.createCustomBootstrapValidator = function (options, parentName) {
        var defaultOptions = options;
        parentName = parentName || 'bootstrapValidator';

        return function (option) {
            var params = Array.prototype.slice.call(arguments, 0),
                options = 'object' === typeof option && option;

            if ('undefined' === typeof option) {
                options = {};
            }

            if (options) {
                options = $.extend({}, defaultOptions, options);
                params[0] = options;
            }
            return $.fn[parentName].apply(this, params);
        };
    };

    $.fn.hzBootstrapValidator = createCustomBootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        container: 'tooltip'
    });
})(jQuery);