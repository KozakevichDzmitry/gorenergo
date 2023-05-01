<?php ?>
<div class="filters__wrapper">
    <ul class="filters__list">
        <?php foreach ($filters as $filter):
            $filter_settings_default = [
                'filter_type' => 'checkbox-column',
                'filter_title' => __('Заголовок не задан'),
                'view_used_attributes' => 1];
            $filter_name = $filter['value'];
            $filter_slug = 'pa_' . $filter_name;
            $filter_settings = !empty($arr_filter_options[$filter_name]) ? $arr_filter_options[$filter_name] : $filter_settings_default;
            $filter_type = $filter_settings['filter_type'];
            $options = $all_attributes[$filter_slug]['options'];
            if ($filter_settings['view_used_attributes'] && !empty($used_attributes[$filter_slug])) {
                $options = $used_attributes[$filter_slug]['options'];
                if (empty($options)) continue;
            } ?>
            <?php if (!empty($options)) : ?>
            <li class="filter <?php if ($filter_name !== 'brand') echo 'filter--line' ?>"
                data-filter_type="<?php echo $filter_type ?>"
                data-attribute_slug="<?php echo $filter_slug ?>">
                <h4 class="filter__title <?php if ($filter_name === 'brand') echo 'filter__title-brand' ?>"><?php _e($filter_settings['filter_title']) ?></h4>
                <?php include get_stylesheet_directory() . '/filter-templates/filter-' . $filter_type . '.php'; ?>
            </li>
        <?php endif;
        endforeach; ?>
    </ul>

    <div class="filter__footer">
        <button type="button"
                class="filter__footer-btn filter__footer-btn--reset"><?php echo __("Сбросить", TM_TEXTDOMAIN) ?></button>
        <button type="button"
                class="filter__footer-btn filter__footer-btn--submit"
                data-loaded_posts="0"><?php echo __("Применить", TM_TEXTDOMAIN) ?></button>
    </div>
</div>