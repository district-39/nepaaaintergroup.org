(function( $ ) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    const default_text_color = document.querySelector( '#apvc-default-counter-text-color' );
    // classic
    if (default_text_color) {
        pickr.create({
            el: default_text_color,
            theme: 'nano',
            default: $("input[name='apvc-default-counter-text-color']").val(),
            defaultRepresentation: 'HEXA',
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: false,
                    rgba: false,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        }).on('save', (color, instance) => {
            $("input[name='apvc-default-counter-text-color']").val(color.toHEXA().toString());
            instance.hide();
        });


    }

    const default_border_color = document.querySelector( '#apvc-default-counter-border-color' );
    // classic
    if (default_border_color) {
        pickr.create({
            el: default_border_color,
            theme: 'nano',
            default: $("input[name='apvc-default-counter-border-color']").val(),
            defaultRepresentation: 'HEXA',
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: false,
                    rgba: false,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        }).on('save', (color, instance) => {
            $("input[name='apvc-default-counter-border-color']").val(color.toHEXA().toString());
            instance.hide();
        });
    }

    const default_background_color = document.querySelector( '#apvc-default-background-color' );
    // classic
    if (default_background_color) {
        pickr.create({
            el: default_background_color,
            theme: 'nano',
            default: $("input[name='apvc-default-background-color']").val(),
            defaultRepresentation: 'HEXA',
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: false,
                    rgba: false,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        }).on('save', (color, instance) => {
            $("input[name='apvc-default-background-color']").val(color.toHEXA().toString());
            instance.hide();
        });
    }


    const border_color = document.querySelector( '#border_color' );
    if (border_color) {
        pickr.create({
            el: border_color,
            theme: 'nano',
            default: $("input[name='border_color']").val(),
            defaultRepresentation: 'HEXA',
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: false,
                    rgba: false,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        }).on('save', (color, instance) => {
            $("input[name='border_color']").val(color.toHEXA().toString());
            instance.hide();
        });
    }

    const font_color = document.querySelector( '#font_color' );
    if (font_color) {
        pickr.create({
            el: font_color,
            theme: 'nano',
            default: $("input[name='font_color']").val(),
            defaultRepresentation: 'HEXA',
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: false,
                    rgba: false,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        }).on('save', (color, instance) => {
            $("input[name='font_color']").val(color.toHEXA().toString());
            instance.hide();
        });
    }

    const background_color = document.querySelector( '#background_color' );
    if (background_color) {
        pickr.create({
            el: background_color,
            theme: 'nano',
            default: $("input[name='background_color']").val(),
            defaultRepresentation: 'HEXA',
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: false,
                    rgba: false,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        }).on('save', (color, instance) => {
            $("input[name='background_color']").val(color.toHEXA().toString());
            instance.hide();
        });
    }

})( jQuery );
