<?php

use yii\web\UploadedFile;

/* @var $model app\models\Logs */
/* @var $file UploadedFile file attribute */
/* @var $form yii\bootstrap\ActiveForm */

use yii\widgets\ActiveForm;

$this->title = 'Upload';
?>
<div class="site-index">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <div class="page-header">
                <h1>ПАРСЕР ЛОГОВ</h1>
            </div>

            <div class="panel panel-info">

                <div class="panel-heading">
                    <h3 class="panel-title">Загрузка файла</h3>
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="info">
                            <span class="label label-info"><?php echo Yii::$app->session->getFlash('success'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

                <div class="panel-body">
                    <?= $form->field($model, 'file')->fileInput() ?>
                    <button type="submit" class="btn btn-info" onclick="this.disabled = true; this.innerHTML='подождите'">Парсинг</button>
                </div>

                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
