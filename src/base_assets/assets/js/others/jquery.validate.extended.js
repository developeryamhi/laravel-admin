(function($) {

    //  Check if Validator Loaded
    if(!$.validator)    return;

    //  Extend Validation Messages
    $.extend($.validator.messages, {
        remote: $.validator.format("'{0}' already exists in system")
    });

    //  Add Alpha Rule
    $.validator.addMethod("alpha", function(value, element) {

        //  Validate
        return this.optional(element) || /^[a-z]?([a-z\s\.]+)$/i.test(value);
    }, 'Please enter alphabatic characters only');

    //  Add Alpha-Dash Rule
    $.validator.addMethod("alphaDash", function(value, element) {

        //  Validate
        return this.optional(element) || /^[a-z]?([a-z\-\_\s\.]+)$/i.test(value);
    }, 'Please enter alphabatic and/or -_ characters only');

    //  Add AlphaNumeric Rule
    $.validator.addMethod("alphanumeric", function(value, element) {

        //  Validate
        return this.optional(element) || /^[a-z0-9]?([a-z0-9\s]+)$/i.test(value);
    }, 'Please enter alpha-numeric characters only');

    //  Add AlphaNumeric-Dash Rule
    $.validator.addMethod("alphanumericDash", function(value, element) {

        //  Validate
        return this.optional(element) || /^[a-z0-9]?([a-z0-9\s\-\_\.]+)$/i.test(value);
    }, 'Please enter alpha-numeric and/or -_ characters only');

    //  Add Class Rules
    $.validator.addClassRules({
        alpha: {
            alpha: true
        },
        alphaDash: {
            alphaDash: true
        },
        alphanumeric: {
            alphanumeric: true
        },
        alphanumericDash: {
            alphanumericDash: true
        }
    });

})(jQuery);