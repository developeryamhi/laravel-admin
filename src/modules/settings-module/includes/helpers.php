<?php

/**
 * Add/Update Setting Group
 * 
 * @param type $key
 * @param type $name_lang
 * @param type $desc_lang
 * @return type
 */
function setSettingGroup($key, $name_lang, $desc_lang = '') {
    return \Developeryamhi\SettingsModule\SettingGroupItem::setSettingGroup($key, $name_lang, $desc_lang);
}

/**
 * Add Setting Item
 * 
 * @param type $group
 * @param type $key
 * @param type $value
 * @param type $lang_key
 * @param type $autoload
 * @param type $desc_key
 * @param type $type
 * @param type $options
 * @param type $mchoice
 * @param type $has_interface
 */
function setSettingItem($group, $key, $value, $lang_key, $autoload = 1, $required = 1, $desc_key = '', $type = 'text', $options = '', $mchoice = 0, $has_interface = true) {
    return \Developeryamhi\SettingsModule\SettingItem::setSettingItem($group, $key, $value, $lang_key, $autoload, $required, $desc_key, $type, $options, $mchoice, $has_interface);
}

/**
 * Get Setting
 * 
 * @param type $key
 * @param type $def
 * @return type
 */
function getSetting($key, $def = null) {
    return \Developeryamhi\SettingsModule\SettingItem::getSetting($key, $def);
}

function sample_func() {
    return array(
        'm' => 'Marshal',
        'y' => 'Yamuna'
    );
}