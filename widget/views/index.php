<?php
/**
 * @var \yii\web\View $this
 * @var string $language
 * @var array $languages
 * @var string $webPath
 * @var string $iconExt
 */
use yii\helpers\Html;
?>

<li class="dropdown" style="margin-top: -3px;">
    <? foreach($languages as $key => $item) : ?>
        <?php if($item == $language) : ?>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <?= Html::img('/' . $webPath . '/' . $language . '.' . $iconExt) . ' ' . $key; ?>
            </a>
        <?php endif ?>
    <? endforeach ?>

    <ul class="dropdown-menu">
        <? foreach($languages as $key => $lang) : ?>
            <?php if($lang != $language) : ?>
                <?php
                $img = Html::img('/' . $webPath . '/' . $lang . '.' . $iconExt) . ' ' . $key;
                $url = Html::a($img, ['/translate/set-language', 'language' => $lang]);
                echo Html::tag('li', $url);
                ?>
            <?php endif ?>
        <? endforeach ?>
    </ul>
</li>
