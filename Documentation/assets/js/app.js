(function($) {
    "use strict";

    var fn = {

        // Launch Functions
        Launch: function() {
            fn.Masonry();
            fn.Overlay();
            fn.Filetree();
            fn.Clipboard();
            fn.Apps();
        },

        Masonry: function() {
            var $grid = $('.masonry').masonry({
                itemSelector: '.masonry > *',
            });
        },

        // Overlay Menu
        Overlay: function() {
            $(document).ready(function() {
                $('.overlay-menu-open').click(function() {
                    $(this).toggleClass('active');
                    $('html').toggleClass('active');
                    $('.overlay-menu').toggleClass('active');
                });
            });
        },


        // File Tree
        Filetree: function() {
            var folder = $('.file-tree li.file-tree-folder'),
                file = $('.file-tree li');

            folder.on("click", function(a) {
                $(this).children('ul').slideToggle(400, function() {
                    $(this).parent("li").toggleClass("open")
                }), a.stopPropagation()
            })

            file.on('click', function(b) {
                b.stopPropagation();
            })
        },


        // Clipboard
        Clipboard: function() {
            var a = new ClipboardJS('.anchor', {
                text: function(b) {
                    return window.location.host + window.location.pathname + $(b).attr("href")
                }
            });

            a.on('success', function(e) {
                e.clearSelection(), $(e.trigger).addClass("copied"), setTimeout(function() {
                    $(e.trigger).removeClass("copied")
                }, 2000)
            });
        },


        // Apps
        Apps: function() {

            // accordion
            $(document).ready(function() {

                $('.collapse').on('show.bs.collapse', function() {
                    $(this).parent().addClass('active');
                });

                $('.collapse').on('hide.bs.collapse', function() {
                    $(this).parent().removeClass('active');
                });

            });

            // skrollr
            skrollr.init({
                forceHeight: false,
                mobileCheck: function() {
                    //hack - forces mobile version to be off
                    return false;
                }
            });


            // Smooth Scroll
            $(function() {
                var scroll = new SmoothScroll('[data-scroll]');
            });


            $(document).ready(function() {
                var window_width = jQuery(window).width();

                if (window_width < 768) {
                    $(".sticky").trigger("sticky_kit:detach");
                } else {
                    make_sticky();
                }


                $(window).resize(function() {

                    window_width = jQuery(window).width();

                    if (window_width < 768) {
                        $(".sticky").trigger("sticky_kit:detach");
                    } else {
                        make_sticky();
                    }

                });


                // recalc on collapse
                $('.nav-item .collapse').on('shown.bs.collapse hidden.bs.collapse', function() {
                    $(".sticky").trigger("sticky_kit:recalc");
                });

                function make_sticky() {
                    $(".sticky").stick_in_parent();
                }

            });


            // prism
            (function() {
                if (typeof self === 'undefined' || !self.Prism || !self.document) {
                    return;
                }

                if (!Prism.plugins.toolbar) {
                    console.warn('Copy to Clipboard plugin loaded before Toolbar plugin.');

                    return;
                }

                var ClipboardJS = window.ClipboardJS || undefined;

                if (!ClipboardJS && typeof require === 'function') {
                    ClipboardJS = require('clipboard');
                }

                var callbacks = [];

                if (!ClipboardJS) {
                    var script = document.createElement('script');
                    var head = document.querySelector('head');

                    script.onload = function() {
                        ClipboardJS = window.ClipboardJS;

                        if (ClipboardJS) {
                            while (callbacks.length) {
                                callbacks.pop()();
                            }
                        }
                    };

                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js';
                    head.appendChild(script);
                }

                Prism.plugins.toolbar.registerButton('copy-to-clipboard', function(env) {
                    var linkCopy = document.createElement('a');
                    linkCopy.textContent = 'Copy';

                    if (!ClipboardJS) {
                        callbacks.push(registerClipboard);
                    } else {
                        registerClipboard();
                    }

                    return linkCopy;

                    function registerClipboard() {
                        var clip = new ClipboardJS(linkCopy, {
                            'text': function() {
                                return env.code;
                            }
                        });

                        clip.on('success', function() {
                            linkCopy.textContent = 'Copied!';

                            resetText();
                        });
                        clip.on('error', function() {
                            linkCopy.textContent = 'Press Ctrl+C to copy';

                            resetText();
                        });
                    }

                    function resetText() {
                        setTimeout(function() {
                            linkCopy.textContent = 'Copy';
                        }, 5000);
                    }
                });
            })();
        }
    };

    $(document).ready(function() {
        fn.Launch();
    });

})(jQuery);