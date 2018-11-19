<?php
/**
 * @var \yii\web\View $this
 * @var string $language
 * @var string $webPath
 * @var string $iconExt
 */
use yii\helpers\Html;
?>

<li class="dropdown" style="margin-top: -3px;">
    <? foreach(LANGUAGES as $key => $item) : ?>
        <?php if($item == $language) : ?>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <?= Html::img('/' . $webPath . '/' . $language . '.' . $iconExt) . ' ' . $key; ?>
            </a>
        <?php endif ?>
    <? endforeach ?>

    <ul class="dropdown-menu">
        <? foreach(LANGUAGES as $key => $lang) : ?>
            <?php if($lang != $language) : ?>
                <?php
                $img = Html::img('/' . $webPath . '/' . $lang . '.' . $iconExt) . ' ' . $key;
                $url = Html::a($img, ['/' . TRANSLATE_MODULE . '/set-language', 'language' => $lang]);
                echo Html::tag('li', $url);
                ?>
            <?php endif ?>
        <? endforeach ?>
    </ul>
</li>
