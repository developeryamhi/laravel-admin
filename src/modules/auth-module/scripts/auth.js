jQuery(function() {

    if($('input#group_name.unique').length > 0) {
        $('input#group_name.unique').rules('add', {
            remote: {
                async: true,
                type: "POST",
                url: '<?php echo urlRoute("group_exists"); ?>',
                data: {
                    search_id: function () {
                        return $("#group_name").next().val();
                    },
                    search_value: function() {
                        return $("#group_name").val();
                    }
                },
                dataFilter: function(data_str) {
                    var data = $.parseJSON(data_str);
                    if(data.valid) {
                        return "\"true\"";
                    } else {
                        return "\"Group " + jQuery.validator.messages.remote($("#group_name").val()) + "\"";
                    }
                }
            }
        });
    }

    if($('input#permission_key.unique').length > 0) {
        $('input#permission_key.unique').rules('add', {
            remote: {
                async: true,
                type: "POST",
                url: '<?php echo urlRoute("permission_exists"); ?>',
                data: {
                    search_id: function () {
                        return $("#permission_key").next().val();
                    },
                    search_value: function() {
                        return $("#permission_key").val();
                    }
                },
                dataFilter: function(data_str) {
                    var data = $.parseJSON(data_str);
                    if(data.valid) {
                        return "\"true\"";
                    } else {
                        return "\"Permission " + jQuery.validator.messages.remote($("#permission_key").val()) + "\"";
                    }
                }
            }
        });
    }

    if($('input#username.unique').length > 0) {
        $('input#username.unique').rules('add', {
            remote: {
                async: true,
                type: "POST",
                url: '<?php echo urlRoute("user_exists"); ?>',
                data: {
                    search_id: function () {
                        return $("#username").next().val();
                    },
                    search_key: "username",
                    search_value: function() {
                        return $("#username").val();
                    }
                },
                dataFilter: function(data_str) {
                    var data = $.parseJSON(data_str);
                    if(data.valid) {
                        return "\"true\"";
                    } else {
                        return "\"Username " + jQuery.validator.messages.remote($("#username").val()) + "\"";
                    }
                }
            }
        });
    }

    if($('input#email.unique').length > 0) {
        $('input#email.unique').rules('add', {
            remote: {
                async: true,
                type: "POST",
                url: '<?php echo urlRoute("user_exists"); ?>',
                data: {
                    search_id: function () {
                        return $("#email").next().val();
                    },
                    search_key: "email",
                    search_value: function() {
                        return $("#email").val();
                    }
                },
                dataFilter: function(data_str) {
                    var data = $.parseJSON(data_str);
                    if(data.valid) {
                        return "\"true\"";
                    } else {
                        return "\"Email Address " + jQuery.validator.messages.remote($("#email").val()) + "\"";
                    }
                }
            }
        });
    }

    $(".user-search-auto").each(function() {
        bindUserSearch($(this));
    });

    $(".users-search-auto").each(function() {
        bindUsersSearch($(this));
    });
});

function bindUserSearch($input) {
    var usersHound = new Bloodhound({
        datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.value); },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '<?php echo urlRoute("users_autocomplete"); ?>?_term=%QUERY',
            ajax: {
                type: 'POST',
                data: {user_type: $input.data("usertype")}
            }
        }
    });

    usersHound.initialize();

    $input.typeahead(null, {
        withHint: false,
        displayKey: 'full_name',
        source: usersHound.ttAdapter(),
        templates: {
            suggestion: Handlebars.compile([
                '<p class="suggest-name">{{full_name}}</p>',
                '<p class="suggest-group pull-right"><span class="badge btn-primary">{{group}}</span></p>',
                '<p class="suggest-username">Username: {{username}}</p>',
                '<div class="clearfix"></div>'
            ].join(''))
        }
    });

    $input.bind("typeahead:selected", function(e, obj) {
        $input.data("ta-selected", true);
        $input.parent().next().val(obj.id);
    });
    $input.prev().css("color", "transparent");
}

function bindUserSearch2($input) {
    $input.tagsinput({
        itemText: 'full_name',
        itemValue: 'id',
        maxTags: 1,
        typeahead: {
            source: function(query) {
                return $.ajax({
                    url: '<?php echo urlRoute("users_autocomplete"); ?>?_term=' + query,
                    type: 'POST',
                    data: {user_type: $input.data("usertype")}
                });
            }
        }
    });
    $input.bind("itemAdded", function() {
        $input.next().next().val($input.val());
    });
    if($input.data("existings")) {
        var ids = [];
        var existings = $input.data("existings");
        if(typeof existings == "string")
            existings = $.parseJSON(existings);
        for(var i in existings) {
            $input.tagsinput('add', existings[i]);
            ids.push(existings[i].id);
        }
        $input.val(ids.join(","));
    }
}

function bindUsersSearch($input) {
    var countMax = $input.data("countmax");
    $input.tagsinput({
        itemText: 'full_name',
        itemValue: 'id',
        maxTags: countMax,
        typeahead: {
            source: function(query) {
                return $.ajax({
                    url: '<?php echo urlRoute("users_autocomplete"); ?>?_term=' + query,
                    type: 'POST',
                    data: {user_type: $input.data("usertype")}
                });
            }
        }
    });
    if($input.data("existings")) {
        var ids = [];
        var existings = $input.data("existings");
        if(typeof existings == "string")
            existings = $.parseJSON(existings);
        for(var i in existings) {
            $input.tagsinput('add', existings[i]);
            ids.push(existings[i].id);
        }
        $input.val(ids.join(","));
    }
}