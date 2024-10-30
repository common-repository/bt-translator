<?php
/*
Plugin Name: BT translator
Description: Standart Language Translator. Simply insert [bt-translator] within any post or page.
Author: Bipin Singh
Version: 1.1.1
License: GPLv2 or later
*/
$BtTranslator = new BtTranslator();
class BtTranslator
{
    function BtTranslator()
    {
        register_activation_hook(__FILE__, array(
            $this,
            'bt_activate'
        ));
        register_deactivation_hook(__FILE__, array(
            $this,
            'bt_deactivate'
        ));
        add_action('admin_init', array(
            &$this,
            'BT_settings'
        ));
        add_action('admin_menu', array(
            &$this,
            'addoptionsPage'
        ));
        add_filter('plugin_action_links', array(
            &$this,
            'pluginActions'
        ), 10, 2);
        add_action('admin_enqueue_scripts', array(
            &$this,
            'bt_scripts'
        ));
        add_action('widgets_init', array(
            &$this,
            'register_widget'
        ));
        add_shortcode('bt-translator', array(
            $this,
            'get_bt_shortcode'
        ));
    }
	
	//default bt settings during activation
    function bt_activate()
    {
        add_option('bt_title', 'Language:');
        add_option('bt_lang_type', 'flags');
        add_option(' bt_combo_flags', 'no');
        add_option('bt_combo_flags_leftright', 'right');
        add_option('advComboWidth', '200');
        add_option('languages', '["English"]');
    }
	
	//delete settings on deactivation
    function bt_deactivate()
    {
        delete_option('bt_title');
        delete_option('bt_lang_type');
        delete_option(' bt_combo_flags');
        delete_option('bt_combo_flags_leftright');
        delete_option('advComboWidth');
        delete_option('default_lang');
        delete_option('languages');
    }
	
	
	//register all the settings
    function BT_settings()
    {
        register_setting('bt-settings-group', 'bt_title');
        register_setting('bt-settings-group', 'bt_lang_type');
        register_setting('bt-settings-group', 'bt_combo_flags');
        register_setting('bt-settings-group', 'bt_combo_flags_leftright');
        register_setting('bt-settings-group', 'advComboWidth', array(
            $this,
            'check_combo_width'
        ));
        register_setting('bt-settings-group', 'default_lang');
        register_setting('bt-settings-group', 'languages', array(
            $this,
            'check_languages_fields'
        ));
        add_settings_section('bt-section', '', array(
            &$this,
            'bt_section_callback'
        ), 'bt-translator');
        add_settings_field('bt_title', 'Title:', array(
            &$this,
            'bt_title_callback'
        ), 'bt-translator', 'bt-section');
        add_settings_field('bt_lang_type', 'Translator Display Type:', array(
            &$this,
            'bt_choice_callback'
        ), 'bt-translator', 'bt-section');
        add_settings_field(' bt_combo_flags', 'Show flag along with combobox:', array(
            &$this,
            'bt_comboflag'
        ), 'bt-translator', 'bt-section');
        add_settings_field(' bt_combo_flags_leftright', 'Select Flag Position:', array(
            &$this,
            'bt_comboflagrl'
        ), 'bt-translator', 'bt-section');
        add_settings_field('advComboWidth', 'Width Of Advanced Combobox:', array(
            &$this,
            'bt_combo_widthcallback'
        ), 'bt-translator', 'bt-section');
        add_settings_field('default_lang', 'Default Language:', array(
            &$this,
            'bt_default_lang_callback'
        ), 'bt-translator', 'bt-section');
        add_settings_field('languages', 'Languages:', array(
            &$this,
            'languages_callback'
        ), 'bt-translator', 'bt-section');
        wp_register_style('bt-style', plugins_url() . "/bt-translator/styles/bt_style.css");
    }
    function bt_section_callback()
    {
        //nothing to do
    }
    function bt_title_callback()
    {
        $setting = esc_attr(get_option('bt_title'));
        echo "<input type='text' name='bt_title' value='$setting' /><i>('Type &lt;none&gt; for no title') </i>";
    }
    function bt_choice_callback()
    {
        $setting = esc_attr(get_option('bt_lang_type'));
        $html    = "<select id='bt_choice' name='bt_lang_type'>
        <option " . selected(get_option("bt_lang_type"), 'flags', false) . "   value='flags' >Flags</option>
        <option" . selected(get_option("bt_lang_type"), 'combobox', false) . "    value='combobox'>combobox</option>
        <option " . selected(get_option("bt_lang_type"), 'advanced_combobox', false) . "   value='advanced_combobox'>Advanced combobox</option>
        </select>";
        echo $html;
    }
    function bt_comboflag()
    {
        $setting = esc_attr(get_option('bt_combo_flags'));
        $html    = "<input id='shflg' name='bt_combo_flags' " . checked($setting, 'yes', false) . " type='checkbox' value='yes'/>";
        echo $html;
    }
    function bt_comboflagrl()
    {
        $setting = esc_attr(get_option('bt_combo_flags_leftright'));
        $html    = "Left: <input name='bt_combo_flags_leftright'" . checked($setting, 'left', false) . "type='radio' value='left' />";
        $html .= "Right: <input name='bt_combo_flags_leftright'" . checked($setting, 'right', false) . "type='radio' value='right' />";
        echo $html;
    }
    function bt_combo_widthcallback()
    {
        $setting = esc_attr(get_option('advComboWidth'));
        echo "<input id='adv_combo' name='advComboWidth' type='text' value='$setting'/><i style='margin-left:5px;'>Px</i>";
    }
    function bt_default_lang_callback()
    {
        echo "<div name='default_lang' id='defTgBx'></div>";
    }
    function languages_callback()
    {
        echo "<div  name='languages' id='tagBx'></div>";
    }
    function check_combo_width()
    {
        if (!is_numeric($_POST['advComboWidth'])) {
            add_settings_error('advComboWidth', 'advComboWidth', 'Please enter a valid numerical value in the "Width Of Advanced Combobox:" field resetting to default', 'error');
            $valid = 200;
        } else {
            $valid = $_POST['advComboWidth'];
        }
        return $valid;
    }
    function check_languages_fields()
    {
        $data = esc_attr($_POST['languages']);
        if ($data == "[]") {
            add_settings_error('languages', 'languages', '"Languages:" field cannot be left empty resetting to default', 'error');
            $data = '["English"]';
        }
        return htmlspecialchars_decode(stripslashes($data));
    }
    function optionsPage()
    {
        $me = get_plugin_data(__FILE__);
    ?>
    <div class="wrap">
    <div class=bt_container>
    <div class='bt_opt_title'><h2>BT Translator Options</h2></div>
    <div class="bt_form">
    <form action="<?php
        echo admin_url('options.php');
    ?>" method="POST">
    <?php
        settings_fields('bt-settings-group');
    ?>
    <div class="bt_label"><label>Title:</label></div><div class="bt_field"><?php
        $this->bt_title_callback();
    ?></div>
    <div class="bt_label"><label>Translator Display Type:</label></div><div class="bt_field"><?php
        $this->bt_choice_callback();
    ?> </div>
    <div class="bt_label bt_shflg"><label>show flag image with combobox:</label></div><div class="bt_field bt_shflg"><?php
        $this->bt_comboflag();
    ?> </div>
    <div class="bt_label bt_flg_rl"><label>Flag Position:</label></div><div class="bt_field bt_flg_rl"><?php
        $this->bt_comboflagrl();
    ?></div>
    <div class="bt_label bt_adv_combo"><label>Width Of Advanced Combobox:</label></div><div class="bt_field bt_adv_combo"><?php
        $this->bt_combo_widthcallback();
    ?></div>
    <div class="bt_label"><label>Default Language:</label></div><div class="bt_field"><?php
     $this->bt_default_lang_callback();
    ?></div>
    <div class="bt_label"><label>Languages:</label></div><div class="bt_field"><?php
    $this->languages_callback();
    ?></div>
    <div class="bt_field bt_selall"><input style=" margin-left: 2px;" type="checkbox" id="all_lang"><i>Select all Languages</i></div>
    <div class="bt_field bt_selall"><input style=" margin-left: 2px;" type="checkbox" id="clear_all_lang"><i>Clear all Languages</i></div>
    <div class="bt_field bt_submit"><?php
        submit_button();
    ?></div>
    </form>
    </div>
    <div class="preview_meta">
    <p>Preview:</p>
    <div id="bt_preview"></div>
    <i>Plugin By:<?php
    echo $me['AuthorName'];
    ?></i>
    </div>
    <div class="short_code_examle">
    <p>use shortcode (<span style="color:#F6358A;">[bt-translator] </span>) anywhere in the posts or pages to show the translator.You can also use Shortcode     simmillar to:  (<span style="color:#F6358A;">[bt-translator title=language display=combobox  showflags=yes flagposition=right advanced_combo_width=200  ]    </span>)</p>
    </div>
    </div></div>
       
  
    <?php
    }
	
	//options page for BT translator
    function addoptionsPage()
    {
        global $wp_version;
        $menutitle = '';
        if (version_compare($wp_version, '2.6.999', '>'))
            $menutitle .= 'BT Translator';
        $page = add_options_page('BT Translator Options', $menutitle, 9, 'bt-translator', array(
            &$this,
            'optionsPage'
        ));
        add_action('admin_print_styles-' . $page, array(
            $this,
            'bt_admin_styles'
        ));
    }
	
    function pluginActions($links, $file)
    {
        if ($file == plugin_basename(__FILE__) && strpos($_SERVER['SCRIPT_NAME'], '/network/') === false) {
            $link = '<a href="options-general.php?page=bt-translator">' . __('Settings') . '</a>';
            array_unshift($links, $link);
        }
        return $links;
    }
	
    function bt_admin_styles()
    {
        wp_enqueue_style('bt-style');
    }
	
    function bt_scripts()
    {
        wp_enqueue_style('tagbox-style', plugins_url() . '/bt-translator/styles/tagbox.css');
        wp_register_script('tagged-api', plugins_url() . '/bt-translator/scripts/tagged.js');
        $json    = $this->getJsonData();
        $value   = get_option('languages');
        $default = get_option('default_lang');
        $path    = plugins_url();
        wp_localize_script('tagged-api', 'json', array(
            'json' => $json,
            'value' => $value,
            'defaults' => $default,
            'plugPath' => $path
        ));
        wp_enqueue_script('tagbox-api', plugins_url() . '/bt-translator/scripts/tagbox.js', array(
            'jquery'
        ));
        wp_enqueue_script('tagged-api', array(
            'jquery'
        ));
    }
    function getJsonData()
    {
        $Countrydata = array(
            "ar" => "Arabic",
            "bg" => "Bulgarian",
            "ca" => "Catalan",
            "zh-CHS" => "Chinese Simplified",
            "zh-CHT" => "Chinese Traditional",
            "cs" => "Czech",
            "da" => "Danish",
            "nl" => "Dutch",
            "en" => "English",
            "et" => "Estonian",
            "fi" => "Finnish",
            "fr" => "French",
            "de" => "German",
            "el" => "Greek",
            "ht" => "Haitian Creole",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "hu" => "Hungarian",
            "id" => "Indonesian",
            "it" => "Italian",
            "ja" => "Japanese",
            "ko" => "Korean",
            "lv" => "Latvian",
            "lt" => "Lithuanian",
            "ms" => "Malay",
            "mt" => "Maltese",
            "no" => "Norwegian",
            "fa" => "Persian",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "ro" => "Romanian ",
            "ru" => "Russian",
            "sk" => "Slovak",
            "sl" => "Slovenian",
            "es" => "Spanish",
            "sv" => "Swedish",
            "th" => "Thai",
            "tr" => "Turkish",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "vi" => "Vietnamese",
            "cy" => "Welsh"
        );
        foreach ($Countrydata as $key => $value) {
            $data[] = array(
                'id' => $value,
                'name' => $value
            );
        }
        $json = json_encode($data);
        return $json;
    }
	
	//register the widget
    function register_widget()
    {
        register_widget('bt_translator_Widget');
    }
	
	//short code for bt translator
    function get_bt_shortcode($atts)
    {
        $type = 'bt_translator_Widget';
        ob_start();
        $params = http_build_query(array(
            "title" => "{$atts['title']}",
            "display" => "{$atts['display']}",
            "languages" => "{$atts['languages']}",
            "showflags" => "{$atts['showflags']}",
            "flagposition" => "{$atts['flagposition']}",
            "advanced_combo_width" => "{$atts['advanced_combo_width']}",
            "defaultlanguage" => "{$atts['defaultlanguage']}"
        ), '', '&');
        the_widget($type, $params);
        $output = ob_get_clean();
        return $output;
    }
}


################################################################widget starts #######################################
class bt_translator_Widget extends WP_Widget
{
    // Create Widget
    function bt_translator_Widget()
    {
        parent::WP_Widget(false, $name = 'BT Translator', array(
            'description' => 'BT Translator for translating site language'
        ));
    }
    // Widget Content
    function widget($args, $instance)
    {
        wp_register_script('microsoft-api', 'http://www.microsoftTranslator.com/ajax/v3/WidgetV3.ashx?siteData=ueOIGRSKkd965FeEGM5JtQ**');
        wp_enqueue_script('microsoft-api',array('jQuery'));
		wp_register_script('bt_api', plugins_url() . '/bt-translator/scripts/translator.js');
        echo $args['before_widget'];
        $title           = isset($instance['title']) && !empty($instance['title']) ? $instance['title'] : get_option('bt_title', 'Language:');
        $disp_type       = isset($instance['display']) && !empty($instance['display']) ? $instance['display'] : get_option('bt_lang_type');
        $allLanguages    = isset($instance['languages']) && !empty($instance['languages']) ? json_encode(explode(',', $instance['languages'])) : get_option('languages');
        $show_flags      = isset($instance['showflags']) && !empty($instance['showflags']) ? $instance['showflags'] : get_option('bt_combo_flags');
        $flag_position   = isset($instance['flagposition']) && !empty($instance['flagposition']) ? $instance['flagposition'] : get_option('bt_combo_flags_leftright');
        $adv_combo_width = isset($instance['advanced_combo_width']) && !empty($instance['advanced_combo_width']) ? $instance['advanced_combo_width'] : get_option('advComboWidth', '200');
        $def_lang        = isset($instance['defaultlanguage']) && !empty($instance['defaultlanguage']) ? '["' . $instance['defaultlanguage'] . '"]' : get_option('default_lang');
        $img_path        = plugins_url() . "/bt-translator/flags";
        if (empty($title)) {
            echo "Select Languages:";
        }
        if (!empty($title) && $title != "<none>") {
            echo $title;
        }
        echo $args['after_title'];
        //bt content here
        if ($disp_type == "advanced_combobox") {
            echo $this->getAdvancedCombobox($allLanguages, $def_lang, $adv_combo_width);
        }
        if ($disp_type == "combobox") {
            echo $this->getCombobox($allLanguages, $def_lang, $show_flags, $flag_position, $img_path);
        }
        if ($disp_type == "flags") {
            echo $this->getFlags($allLanguages, $def_lang);
        }
        echo $args['after_widget'];
    }
    // Update and save the widget
    function update($new_instance, $old_instance)
    {
        //we need to do nothing here just return the new instance
        return $new_instance;
    }
    // If widget content needs a form
    function form($instance)
    {
        echo '<div class="bt_settings" style="margin: 15px 0px;"> Click <a  href="options-general.php?page=bt-translator">' . __('Here') . '</a> to access the settings of this widget.</div>';
    }
	
	//create language codes
    function bt_get_Lngcode($arr)
    {
        $langArray  = array(
            "ar" => "Arabic",
            "bg" => "Bulgarian",
            "ca" => "Catalan",
            "zh-CHS" => "Chinese Simplified",
            "zh-CHT" => "Chinese Traditional",
            "cs" => "Czech",
            "da" => "Danish",
            "nl" => "Dutch",
            "en" => "English",
            "et" => "Estonian",
            "fi" => "Finnish",
            "fr" => "French",
            "de" => "German",
            "el" => "Greek",
            "ht" => "Haitian Creole",
            "he" => "Hebrew",
            "hi" => "Hindi",
            "hu" => "Hungarian",
            "id" => "Indonesian",
            "it" => "Italian",
            "ja" => "Japanese",
            "ko" => "Korean",
            "lv" => "Latvian",
            "lt" => "Lithuanian",
            "ms" => "Malay",
            "mt" => "Maltese",
            "no" => "Norwegian",
            "fa" => "Persian",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "ro" => "Romanian ",
            "ru" => "Russian",
            "sk" => "Slovak",
            "sl" => "Slovenian",
            "es" => "Spanish",
            "sv" => "Swedish",
            "th" => "Thai",
            "tr" => "Turkish",
            "uk" => "Ukrainian",
            "ur" => "Urdu",
            "vi" => "Vietnamese",
            "cy" => "Welsh"
        );
        $lang_array = '';
        if ($arr != "[]") {
            $sanitizedArray = explode(',', str_replace(array(
                "[",
                "]"
            ), '', $arr));
            foreach ($sanitizedArray as $ck => $cv) {
                $code = array_search(trim($cv, '"'), $langArray);
                if ($code != FALSE) {
                    $lang_array[$code] = trim($cv, '"');
                }
            }
        }
        return $lang_array;
    }
    function getAdvancedCombobox($allLanguages, $def_lang, $adv_combo_width)
    {
	    wp_enqueue_style('dropdown-style', plugins_url() . '/bt-translator/styles/dd.css');
	    wp_enqueue_script('dropdown-api',plugins_url() . '/bt-translator/scripts/jquery.dd.js',true);
        $language_codes   = $this->bt_get_Lngcode($allLanguages);
        $default_language = $this->bt_get_Lngcode($def_lang);
        if (!is_array($default_language)) {
            $default_language = array(
                "default" => "Default"
            );
        }
        list($lndc, $lndv) = each($default_language);
        $html = "<div class='bt_translator_content'><select class='language-dropdown-advanced'>
<option data-image='" . plugins_url() . "/bt-translator/flags/" . $lndc . ".png'   class='restoreOriginal'  value='" . $lndc . "'>" . $lndv . "</option>";
        foreach ($language_codes as $lnc => $lnv) {
            $html .= "<option data-image='" . plugins_url() . "/bt-translator/flags/" . $lnc . ".png' value='" . $lnc . "'>" . $lnv . "</option>";
        }
        $html .= '</select></div>';
        wp_localize_script('bt_api', 'bt_global', array(
            'enable' => 'advanced',
            'width' => $adv_combo_width,
            'defaults' => $lndc
        ));
        wp_enqueue_script('bt_api');
        return $html;
    }
    function getCombobox($allLanguages, $def_lang, $show_flags, $flag_position, $img_path)
    {
        $language_codes   = $this->bt_get_Lngcode($allLanguages);
        $default_language = $this->bt_get_Lngcode($def_lang);
        if (!is_array($default_language)) {
            $default_language = array(
                "default" => "Default"
            );
        }
        list($lndc, $lndv) = each($default_language);
        $leftFlag  = !empty($show_flags) && $show_flags == "yes" && $flag_position == "left" ? "<div style='width:24px;float: left;'  class='bt_flagleft'></div>" : "";
        $rightFlag = !empty($show_flags) && $show_flags == "yes" && $flag_position == "right" ? "<div  style='width:24px;float: right;'  class='bt_flagright'></div>" : '';
        $leftPos   = !empty($show_flags) && $show_flags == "yes" && $flag_position == "left" ? "left" : "";
        $rightPos  = !empty($show_flags) && $show_flags == "yes" && $flag_position == "right" ? "right" : "";
        $flagPos   = '';
        if ($leftPos == 'left') {
            $flagPos = 'left';
        }
        if ($rightPos == 'right') {
            $flagPos = 'right';
        }
        $html = "<div class='bt_translator_content'>" . $leftFlag . "<select class='language-dropdown-simple'>";
        $html .= "<option   class='restoreOld'  value='" . $lndc . "'>" . $lndv . "</option>";
        foreach ($language_codes as $lnc => $lnv) {
            $html .= "<option value='" . $lnc . "'>" . $lnv . "</option>";
        }
        $html .= '</select>' . $rightFlag . '</div>';
        wp_localize_script('bt_api', 'bt_global', array(
            'enable' => 'simplecombo',
            'defaults' => $lndc,
            'imgpath' => $img_path,
            'flagpos' => $flagPos
        ));
        wp_enqueue_script('bt_api');
        return $html;
    }
    function getFlags($allLanguages, $def_lang)
    {
        $language_codes   = $this->bt_get_Lngcode($allLanguages);
        $default_language = $this->bt_get_Lngcode($def_lang);
        if (!is_array($default_language)) {
            $default_language = array(
                "default" => "Default"
            );
        }
        list($lndc, $lndv) = each($default_language);
        $html = "<div class='bt_translator_content'>";
        foreach ($language_codes as $lnc => $lnv) {
            $html .= "<span  class='ln_flag' style='cursor: pointer;'><img id='" . $lnc . "' src='" . plugins_url() . "/bt-translator/flags/" . $lnc . ".png' alt='" . $lnv . "'/></span>";
        }
        $html .= '</div>';
        wp_localize_script('bt_api', 'bt_global', array(
            'enable' => 'flags',
            'defaults' => $lndc
        ));
        wp_enqueue_script('bt_api');
        return $html;
    }
}
?>