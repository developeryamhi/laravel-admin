<div class="page-head">
    <h1 class="col-md-6"><?php echo trans("settings-module::label.manage_settings"); ?></h1>
    <div class="clearfix"></div>
</div>

<?php if(\Developeryamhi\SettingsModule\SettingItem::count() < 1) {
    echo "<p>" . trans("settings-module::message.no_settings_available") . "</p>";
    return;
} ?>

{{ Form::open(array('route' => 'save_settings', 'files' => true)) }}
<div class="panel">
    <div class="panel-body">
        <ul class="nav nav-tabs">
        <?php foreach($groups as $i => $group) { ?>
            <?php if(!$group->hasSettings()) continue; ?>
            <li<?php echo ($i == 0 ? ' class="active"' : ''); ?> title="<?php echo ($group->setting_group_desc_lang != '' ? trans($group->setting_group_desc_lang) : ''); ?>"><a href="#tab-<?php echo $group->setting_group_key; ?>" data-toggle="tab"><?php echo trans($group->setting_group_name_lang); ?>&nbsp;&nbsp;<span class="badge btn-danger"></span></a></li>
        <?php } ?>
        </ul>

        <div class="tab-content">
            <?php foreach($groups as $i => $group) { ?>
            <?php if(!$group->hasSettingsWithInterface()) continue; ?>
            <div class="tab-pane fade<?php echo ($i == 0 ? ' in active' : ''); ?>" id="tab-<?php echo $group->setting_group_key; ?>">
                <div class="row">
                    <div class="col-md-<?php echo ($group->isFullRowSetting() ? 12 : 5); ?>">
                        <?php
                            $settings = $group->settingsWithInterface()->get();
                            $split_cols = (sizeof($settings) > 5) ? true : false;
                            $split_at = ceil(sizeof($settings) / 2);
                            if($group->isFullRowSetting())  $split_at = 0;

                            foreach($settings as $i => $setting) {
                                $settingType = $setting->setting_type;
                                $settingValue = $setting->getValue();
                                $settingMChoice = (bool)$setting->setting_mchoice;
                                if($settingType == "checkbox")
                                    $settingMChoice = true;

                                $pHolder = trans($setting->setting_name_lang);
                                $settingLabel = trans($setting->setting_name_lang);
                                if(!empty($setting->setting_desc_lang))
                                    $pHolder = trans($setting->setting_desc_lang);

                                $settingOptions = $setting->getChoices(array($settingValue => $settingValue));
                                $settingVals = $setting->getValues();
                        ?>
                        <?php if($split_cols > 0 && $i == $split_at) { ?>
                    </div>
                    <div class="col-md-5 pull-right">
                        <?php } ?>
                        <div class="form-group">
                            {{ Form::label("setting-" . $setting->setting_key, $settingLabel) }}
                            <?php
                                switch($settingType) {
                                    case 'choices':
                            ?>
                                <div class="clearfix"></div>
                                <?php foreach($settingOptions as $settingOptionKey => $settingOptionLabel) { ?>
                                <label class="inline-choice">
                                    {{ Form::checkbox('setting[' . $setting->setting_key . ']' . ($settingMChoice ? '[]' : ''), $settingOptionKey, in_array($settingOptionKey, $settingVals)) }}
                                    <?php echo $settingOptionLabel; ?>
                                </label>
                                <?php } ?>
                            <?php
                                        break;
                                    case 'radio':
                            ?>
                                <div class="clearfix"></div>
                                <?php foreach($settingOptions as $settingOptionKey => $settingOptionLabel) { ?>
                                <label class="inline-choice">
                                    {{ Form::radio('setting[' . $setting->setting_key . ']', $settingOptionKey, in_array($settingOptionKey, $settingVals)) }}
                                    <?php echo $settingOptionLabel; ?>
                                </label>
                                <?php } ?>
                            <?php
                                        break;
                                    case 'boolean':
                            ?>
                                {{ Form::hidden('setting[' . $setting->setting_key . ']', 0) }}
                                {{ Form::checkbox('setting[' . $setting->setting_key . ']', 1, in_array(1, $settingVals), array('id' => 'setting-' . $setting->setting_key)) }}
                            <?php
                                        break;
                                    case 'select':
                            ?>
                                {{ Form::select('setting[' . $setting->setting_key . ']' . ($settingMChoice ? '[]' : ''), $settingOptions, $settingVals, array_merge(array('class' => 'form-control', 'id' => 'setting-' . $setting->setting_key), $settingMChoice ? array("multiple" => "multiple") : array())) }}
                            <?php
                                        break;
                                    case 'image':
                            ?>
                                {{ Form::hidden('setting[' . $setting->setting_key . ']', $settingValue) }}
                                <div class="input-group">
                                    <input type="text" class="form-control selected-file" readonly="" />
                                    <span class="input-group-btn">
                                        <span class="btn btn-primary btn-file">
                                            Browse {{ Form::file($setting->setting_key, array('class' => 'form-control' . ($setting->isRequired() ? ' required' : ''), 'id' => 'setting-' . $setting->setting_key, 'accept' => 'image/*')) }}
                                        </span>
                                    </span>
                                </div>
                                <?php if($settingValue != '') {
                                echo HTML::link(AssetsHelper::uploadUrl($settingValue), trans("settings-module::label.view_image"), array("rel" => "popup"));
                                } ?>
                            <?php
                                        break;
                                    case 'file':
                            ?>
                                {{ Form::hidden('setting[' . $setting->setting_key . ']', $settingValue) }}
                                <div class="input-group">
                                    <input type="text" class="form-control selected-file" readonly="" />
                                    <span class="input-group-btn">
                                        <span class="btn btn-primary btn-file">
                                            Browse {{ Form::file($setting->setting_key, array('class' => 'form-control' . ($setting->isRequired() ? ' required' : ''), 'id' => 'setting-' . $setting->setting_key)) }}
                                        </span>
                                    </span>
                                </div>
                                <?php if($settingValue != '') {
                                echo HTML::link(AssetsHelper::uploadUrl($settingValue), trans("settings-module::label.view_file"), array("target" => "_blank"));
                                } ?>
                            <?php
                                        break;
                                    case 'textarea':
                            ?>
                                {{ Form::textarea('setting[' . $setting->setting_key . ']', $settingValue, array('placeholder' => $pHolder, 'class' => 'form-control' . ($setting->isRequired() ? ' required' : '') . ($settingMChoice ? ' wysihtml5' : ''), 'id' => 'setting-' . $setting->setting_key, 'type' => $settingType)) }}
                            <?php
                                        break;
                                    case 'simple_textarea':
                            ?>
                                {{ Form::textarea('setting[' . $setting->setting_key . ']', $settingValue, array('placeholder' => $pHolder, 'rows' => 6, 'class' => 'form-control' . ($setting->isRequired() ? ' required' : ''), 'id' => 'setting-' . $setting->setting_key, 'type' => $settingType)) }}
                            <?php
                                        break;
                                    case 'text':
                                    case 'input':
                            ?>
                                {{ Form::text('setting[' . $setting->setting_key . ']', $settingValue, array('placeholder' => $pHolder, 'class' => 'form-control' . ($setting->isRequired() ? ' required' : ''), 'id' => 'setting-' . $setting->setting_key)) }}
                            <?php
                                        break;
                                    default:
                            ?>
                                {{ Form::custom($settingType, 'setting[' . $setting->setting_key . ']', $settingValue, array('placeholder' => $pHolder, 'class' => 'form-control' . ($setting->isRequired() ? ' required' : ''), 'id' => 'setting-' . $setting->setting_key)) }}
                            <?php
                                        break;
                                }
                            ?>
                            <div class="clearfix"></div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="form-actions">
    {{ Form::submit(trans("settings-module::label.btn_save"), array('class' => 'btn btn-primary')) }}
</div>
{{ Form::close() }}