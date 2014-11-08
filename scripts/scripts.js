$(function () {


    /**
     * Animations.
     */
    $("#demo-animations").find(".demo-col").on("click", function () {
        var $this = $(this);

        $this.addClass("animated " + $this.text());
    });



    /**
     * Tabs.
     */
    $(".tabs-links a").on("click", function (e) {
        e.preventDefault();

        // Get the target tab.
        var newTab = $(this).attr("href");

        // Show the targeted tab content while hiding the rest.
        $(newTab).show().siblings().hide();

        // Add the active class to the parent list item while removing it from the rest.
        $(this).parent("li").addClass("active").siblings().removeClass("active");
    });


    /**
     * Accordions.
     */
    $(document).ready(function () {
        $("#accordion section h1").click(function (e) {
            $(this).parents().siblings("section").addClass("ac_hidden");
            $(this).parents("section").removeClass("ac_hidden");

            e.preventDefault();
        });

    });
    
    

    /**
     * Navigation
     */

    $(document).ready(function () {

        $.fn.menumaker = function (options) {

            var cssmenu = $(this),
                settings = $.extend({
                    title: "Menu",
                    format: "dropdown",
                    breakpoint: 768,
                    sticky: false
                }, options);

            return this.each(function () {
                cssmenu.find('li ul').parent().addClass('has-sub');
                if (settings.format != 'select') {
                    cssmenu.prepend('<div id="menu-button">' + settings.title + '</div>');
                    $(this).find("#menu-button").on('click', function () {
                        $(this).toggleClass('menu-opened');
                        var mainmenu = $(this).next('ul');
                        if (mainmenu.hasClass('open')) {
                            mainmenu.hide().removeClass('open');
                        } else {
                            mainmenu.show().addClass('open');
                            if (settings.format === "dropdown") {
                                mainmenu.find('ul').show();
                            }
                        }
                    });

                    multiTg = function () {
                        cssmenu.find(".has-sub").prepend('<span class="submenu-button"></span>');
                        cssmenu.find('.submenu-button').on('click', function () {
                            $(this).toggleClass('submenu-opened');
                            if ($(this).siblings('ul').hasClass('open')) {
                                $(this).siblings('ul').removeClass('open').hide();
                            } else {
                                $(this).siblings('ul').addClass('open').show();
                            }
                        });
                    };

                    if (settings.format === 'multitoggle') multiTg();
                    else cssmenu.addClass('dropdown');
                } else if (settings.format === 'select') {
                    cssmenu.append('<select style="width: 100%"/>').addClass('select-list');
                    var selectList = cssmenu.find('select');
                    selectList.append('<option>' + settings.title + '</option>', {
                        "selected": "selected",
                        "value": ""
                    });
                    cssmenu.find('a').each(function () {
                        var element = $(this),
                            indentation = "";
                        for (i = 1; i < element.parents('ul').length; i++) {
                            indentation += '-';
                        }
                        selectList.append('<option value="' + $(this).attr('href') + '">' + indentation + element.text() + '</option');
                    });
                    selectList.on('change', function () {
                        window.location = $(this).find("option:selected").val();
                    });
                }

                if (settings.sticky === true) cssmenu.css('position', 'fixed');

                resizeFix = function () {
                    if ($(window).width() > settings.breakpoint) {
                        cssmenu.find('ul').show();
                        cssmenu.removeClass('small-screen');
                        if (settings.format === 'select') {
                            cssmenu.find('select').hide();
                        } else {
                            cssmenu.find("#menu-button").removeClass("menu-opened");
                        }
                    }

                    if ($(window).width() <= settings.breakpoint && !cssmenu.hasClass("small-screen")) {
                        cssmenu.find('ul').hide().removeClass('open');
                        cssmenu.addClass('small-screen');
                        if (settings.format === 'select') {
                            cssmenu.find('select').show();
                        }
                    }
                };
                resizeFix();
                return $(window).on('resize', resizeFix);

            });
        };
    });

        $(document).ready(function () {

            $(document).ready(function () {
                $("#cssmenu").menumaker({
                    title: "Menu",
                    format: "dropdown"
                });
            });

        });

    

    /*
     * Slider
     */
    $(document).ready(function () {

        var Page = (function () {

            var $navArrows = $('#nav-arrows').hide(),
                $shadow = $('#shadow').hide(),
                slicebox = $('#sb-slider').slicebox({
                    onReady: function () {

                        $navArrows.show();
                        $shadow.show();

                    },
                    orientation: 'r',
                    cuboidsRandom: true,
                    autoplay: true,
                    disperseFactor: 30
                }),

                init = function () {

                    initEvents();

                },
                initEvents = function () {

                    // add navigation events
                    $navArrows.children(':first').on('click', function () {

                        slicebox.next();
                        return false;

                    });

                    $navArrows.children(':last').on('click', function () {

                        slicebox.previous();
                        return false;

                    });

                };

            return {
                init: init
            };

        })();

        Page.init();

    });

});