/**
 * translator.js
 * Requires: jQuery
 * Author: Bipin Singh
 * Date: May 10 2014
 * Version: 1.0
 * Licence: GNU General Public License
 */

jQuery(function () {


    if (typeof Microsoft != "undefined") {
        var d = bt_global;
        var e = d.defaults;

        function hideWidget() {
            jQuery("#WidgetFloaterPanels").hide()
        }
        function onComplete() {
            Microsoft.Translator.Widget.domTranslator.showHighlight = false;
            Microsoft.Translator.Widget.domTranslator.showTooltips = false;
            hideWidget()
        }
        function onRestoreOriginal() {
            Microsoft.Translator.Widget.domTranslator.showHighlight = false;
            Microsoft.Translator.Widget.domTranslator.showTooltips = false;
            hideWidget()
        }
        function getWidget(a) {
            Microsoft.Translator.Widget.Translate('', a, onComplete, onRestoreOriginal);
            hideWidget()
        }
        function widgetRestore() {
            Microsoft.Translator.Widget.RestoreOriginal();
            hideWidget()
        }
        function getDefaultLangWidget(b) {
		  if (jQuery.browser.msie == true) {Microsoft.Translator.Widget.Translate('', b, onComplete, onRestoreOriginal);
                    hideWidget()}
					else{
					var c = function (a) {
                  
                  Microsoft.Translator.Widget.Translate('', a, onComplete, onRestoreOriginal);
                    hideWidget();  
                };
            setTimeout(function () {
                c(b)
            }, 2000)	
					}
            
        }
        var f = d.imgpath;
        var g = d.flagpos;
        var h = bt_global.enable;
        switch (h) {
        case "simplecombo":

            var i = jQuery(".language-dropdown-simple");
            jQuery(".bt_translator_content").on('change', 'select', function () {
                lnCode = jQuery(this).val();
                if (lnCode != "default") {
                    getWidget(lnCode)
                }
                if (lnCode == "default") {
                    widgetRestore()
                }
            });
            if (e != "default") {
                getDefaultLangWidget(e)
            }
            if (g != '') {
                if (g == 'left') {
                    flagElem = jQuery('.bt_flagleft')
                }
                if (g == 'right') {
                    flagElem = jQuery('.bt_flagright')
                }
                flagElem.append("<img src='" + f + "/" + i.val() + ".png'/>");
                jQuery(".bt_translator_content").on('change', 'select', function () {
                    flagElem.children().replaceWith("<img src='" + f + "/" + jQuery(this).val() + ".png'/>")
                })
            }
            break;
        case "advanced":
            var j = bt_global.width;
            hideWidget();
            jQuery(".language-dropdown-advanced").each(function () {
                jQuery(this).msDropDown({
                    width: j,
                    on: {
                        change: function (a, b) {
                            var c = a.value;
                            if (c != "default") {
                                getWidget(c)
                            }
                            if (c == "default") {
                                widgetRestore()
                            }
                        }
                    }
                })
            });
            if (e != "default") {
                getDefaultLangWidget(e)
            }
            break;
        case "flags":
            jQuery(".ln_flag").on('click', 'img', function () {
                var a = jQuery(this).attr("id");
                getWidget(a)
            });
            if (e != "default") {
                getDefaultLangWidget(e)
            }
        }
        var k = setInterval(function () {
            var a = jQuery("#WidgetFloaterPanels");
            var b = a.css("display");
            if (a.length > 0 && b != "none") {
                a.hide();
                clearInterval(k)
            }
        }, 1)
    }
})
