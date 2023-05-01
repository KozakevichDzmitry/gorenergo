<?php
$arr_values = array_column($options, "name");
$min_value = min($arr_values);
$max_value = max($arr_values);
$values_is_float = false;
foreach ($arr_values as $value) {
    if (is_float($value + 0 )) {
        $values_is_float = true;
        break;
    }
}
$step = $values_is_float? 0.1 : 1;
?>
<fieldset class="filter-slider">
    <div class="input-number__wrapper">
        <input type="number" class="input-number" placeholder="<?php echo $min_value ?>" data-name="<?php echo $min_value ?>">
        <input type="number" class="input-number" placeholder="<?php echo $max_value ?>" data-name="<?php echo $max_value ?>">
    </div>
    <div class="range-slider">
        <input type="range" min="<?php echo $min_value ?>" max="<?php echo $max_value ?>" step="<?php echo $step ?>"
               value="<?php echo $min_value ?>" class="range-slider__input"/>
        <input type="range" min="<?php echo $min_value ?>" max="<?php echo $max_value ?>" step="<?php echo $step ?>"
               value="<?php echo $max_value ?>" class="range-slider__input"/>
        <div class="range-slider__display">
            <span class="range-slider__min-value"><?php echo $min_value ?></span>
            <span class="range-slider__max-value"><?php echo $max_value ?></span>
        </div>
    </div>
</fieldset>