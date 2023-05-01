<?php ?>
<ul class="filter__options filter__options--row">
    <?php foreach ($options as $option): ?>
        <li class="filter__option">
            <label>
                <input type="checkbox" data-name="<?php echo $option['slug'] ?>">
                <span><?php echo $option['name']; ?></span>
            </label>
        </li>
    <?php endforeach; ?>
</ul>