jQuery(function($) {

    //  Init Tabdrop
    $('.nav-pills, .nav-tabs').tabdrop();

    //  Init Bootstrap Select
    $('.bselect').selectpicker();

    //  Init Tooltip
    $("*:not(.ignore-tooltip)[title]").each(function() {
        var pos = ($(this).data("placement") ? $(this).data("placement") : 'top');
        $(this).tooltip({
            placement: pos
        });
    });

    //  Init Editor
    $("textarea.wysihtml5").wysihtml5({
        stylesheets: [BASE_ROOT + "assets/css/bootstrap-wysihtml5/wysiwyg-color.css"]
    });

    //  Init Fancybox
    $("a[rel=popup]").fancybox();
    $("#page a>img").fancybox();

    //  Make Dashboard Widgets Sortable
    $("#dashboard-widgets .dashboard-widget-holder").sortable();

    //  Listen Hash Change
    $(window).hashchange( function() {
        var hashLoc = location.hash;
        if(hashLoc != '') {
            if($("a[href=" + hashLoc + "]").length > 0)
                $("a[href=" + hashLoc + "]").click();
        }
    }).hashchange();

    //  Listen Window Resize
    $(window).resize(function() {
        if($("#main-header .navbar").height() > 55) {
            var diffHeight = $("#main-header .navbar").height() - 55;
            diffHeight += ($("#body-wrapper .alert").length * 52);
            $("#body-wrapper").css("padding-top", (diffHeight + 75) + "px");
        } else {
            $("#body-wrapper").css("padding-top", "75px");
        }
    }).resize();

    //  Fix for Dropdown Menu
    $(".dropdown").click(function() {
        $(".dropdown-submenu").find(">.dropdown-menu").not($(this)).hide(0);
    });
    $(".dropdown-submenu>a").click(function(e) {
        var $mItem = $(this).parent();
        $(".dropdown-submenu").find(">.dropdown-menu").not($mItem).hide(0);
        $mItem.find(">.dropdown-menu").slideToggle();
        e.preventDefault();
        return false;
    });

    if($(".navbar-collapse").height() + 50 >= $(window).height())
        $(".navbar-collapse").css({ maxHeight: $(window).height() - $(".navbar-header").height() + "px" });

    $("li.dropdown-submenu").each(function() {
        if($(this).hasClass("active")) {
            $(this).parent().parent().addClass("active");
        }
    });

    $(".tmpl_items").each(function() {

        //  This Tmpl Loader
        var $thisTmplLoader = $(this);

        //  Template ID
        var tmplID = $thisTmplLoader.data("template");

        //  Append Add Button
        $thisTmplLoader.after('<div class="clearfix"></div><a class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a>');

        //  Listen Add Button
        $thisTmplLoader.next().next().click(function(e) {

            //  Add Template Data
            $thisTmplLoader.append(tmpl(tmplID, {data: [{title: ""}]}));
            $thisTmplLoader.trigger('tmpl_item:added', $thisTmplLoader.find(".tmpl-item:last"));

            e.preventDefault();
            return false;
        });

        //  Check data available for Loading
        if($thisTmplLoader.data("load")) {

            //  Value
            var value = eval($thisTmplLoader.data("load"));

            //  Check Value
            if(value && value != undefined) {

                //  Add Template Data
                $thisTmplLoader.append(tmpl(tmplID, value));
            }
        }

        //  Check callback available
        if($thisTmplLoader.data("onready")) {

            //  Value
            var onready = $thisTmplLoader.data("onready");

            //  Run Callback
            eval(onready + '($thisTmplLoader)');
        }
    });

    $(document).on('click', '.tmpl-item-remove', function(e) {

        var $row = $(this);
        while(!$row.hasClass("tmpl-item")) {
            $row = $row.parent();
        }

        //  Confirm
        if(confirm("Are you sure to delete this item?")) {
            $row.fadeOut('slow', function() {
                $row.remove();
                $row.parent().trigger('tmpl_item:deleted');
            });
        }
        e.preventDefault();
        return false;
    });

    var validateOptions = {
        ignore: ".ignore",
        errorPlacement: function($label, $input) {
            if($input.parent().hasClass("btn-file")) {
                $input.parent().parent().parent().after($label);
                $input.parent().parent().parent().find("input.selected-file").addClass("error");
            }
            else if($input.parent().hasClass("input-group")) {
                $input.parent().after($label);
            }
            else if($input.parent().is("span"))
                $input.parent().parent().append($label);
            else if($input.is(":hidden")) {
                $input.parent().append($label);
                $input.next().addClass("error");
            }
            else
                $input.after($label);
            tabbed_errors_count_display($(this));
        },
        success: function($label, input) {
            var $input = $(input);
            if($input.parent().hasClass("btn-file")) {
                $input.parent().parent().parent().find("input.selected-file").removeClass("error");
            }
            else if($input.is(":hidden"))
                $input.next().removeClass("error");
            $label.remove();
            tabbed_errors_count_display($(this));
        },
        submitHandler: function() {
            var fine = true;
            var custom_validate = $(this.currentForm).data("customvalidate");
            if(custom_validate) {
                fine = eval(custom_validate + "($(this.currentForm), this)");
            }
            if(fine)
                this.currentForm.submit();
        },
        invalidHandler: function() {
            tabbed_errors_count_display($(this));
        }
    };

    $("form").each(function() {
        $(this).validate(validateOptions);
    });

    $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
        var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;

        if (input.length) {
            input.val(log);
        }
    });
    $("input.selected-file").click(function() {
        $(this).parent().find(":file").click();
    });
    $('.clear-file').click(function() {
        var $fileInput = $(this).parent().parent().find("input:file");
        $fileInput.val("");
        $fileInput.parent().parent().parent().find("input.selected-file").val("no file selected");
        $(this).parent().parent().find("input:file").trigger('fileclear');
    }).click();
});

$(document).on('change', '.btn-file :file', function() {
    var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});

function tabbed_errors_count_display($form) {
    setTimeout(function() {
        $form.find(".nav-tabs>li:not(.dropdown)").each(function() {
            var $tab_pane = $form.find($(this).find(">a").attr("href") + ".tab-pane");
            var errors_count = $tab_pane.find("label.error").length;
            if(errors_count > 0)
                $(this).find(".badge").html(errors_count);
            else
                $(this).find(".badge").html("");
        });

        $form.find(".nav-tabs>li:not(.dropdown)").each(function() {
            var $tab_pane = $form.find($(this).find(">a").attr("href") + ".tab-pane");
            var errors_count = $tab_pane.find("label.error").length;
            if(errors_count > 0) {
                $(this).find(">a").click();
                return false;
            }
        });
    }, 100);
}