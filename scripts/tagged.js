/**
 * tagged.js
 * Requires: jQuery
 * Author: Bipin Singh
 * Date: May 10 2014
 * Version: 1.0
 * Licence: GNU General Public License
 */
jQuery(function () {
    var e = jQuery.parseJSON(json.json);
    var t = jQuery.parseJSON(json.value);
    var n = jQuery.parseJSON(json.defaults);
    var r = json.plugPath;
    var i = jQuery("#tagBx").magicSuggest({
        width: 300,
        emptyText: "Please select Languages",
        hideTrigger: true,
        toggleOnClick: true,
        maxSelection: null,
        displayField: "name",
        value: t,
        data: e
    });
    var s = jQuery("#defTgBx").magicSuggest({
        width: 300,
        emptyText: "Default Language",
        hideTrigger: true,
        toggleOnClick: true,
        maxSelection: 1,
        displayField: "name",
        value: n,
        data: e
    });
    jQuery("#tagBx,#defTgBx").css({
        height: "auto"
    });
    var o = jQuery(".bt_adv_combo");
    var u = jQuery("#bt_choice").val();
    u == "advanced_combobox" ? o.show() : o.hide();
    jQuery("#bt_choice").on("change", function () {
        var e = jQuery(this).val();
        if (e == "advanced_combobox") {
            o.slideDown()
        }
        if (e != "advanced_combobox") {
            o.slideUp()
        }
    });
    var a = jQuery(".bt_shflg");
    var f = jQuery(".bt_flg_rl");
    var l = jQuery("#shflg").attr("checked");
    var c = jQuery("#bt_choice");
    if (c.val() == "combobox") {
        a.show();
        if (l == "checked") {
            f.show()
        } else {
            f.hide()
        }
    } else {
        a.hide();
        f.hide()
    }
    c.on("change", function () {
        currVal = jQuery(this).val();
        if (currVal == "combobox") {
            a.slideDown();
            if (jQuery("#shflg").attr("checked") == "checked") {
                f.slideDown()
            }
        }
        if (currVal != "combobox") {
            a.slideUp();
            f.slideUp()
        }
    });
    jQuery("#shflg").on("click", function () {
        if (jQuery(this).attr("checked") == "checked") {
            f.slideDown()
        }
        if (jQuery(this).attr("checked") != "checked") {
            f.slideUp()
        }
    });
    jQuery("#all_lang").on("click", function () {
        if (jQuery(this).attr("checked") == "checked") {
            i.addToSelection(e);
            i.setValue(t);
            n
        } else {
            i.removeFromSelection(e);
            i.setValue(t)
        }
    });
    jQuery("#clear_all_lang").on("click", function () {
        if (jQuery(this).attr("checked") == "checked") {
            i.removeFromSelection(e);
            i.setValue(n)
        } else {
            i.setValue(t)
        }
    });
    if (jQuery("#ms-sel-ctn-0 .ms-sel-item").children().length == 42) {
        jQuery("#all_lang").attr("checked", true)
    }
    var h = "<img src='" + r + "/bt-translator/styles/images/previews/" + jQuery("#bt_choice").val() + ".png' />";
    var p = jQuery("#bt_preview");
    p.append(h);
    jQuery("#bt_choice").on("change", function () {
        jQuery("#bt_preview img").replaceWith("<img style='opacity:0;'img src='" + r + "/bt-translator/styles/images/previews/" + jQuery(this).val() + ".png' />");
        jQuery("#bt_preview img").animate({
            opacity: 1
        }, {
            duration: 3e3
        })
    })
})