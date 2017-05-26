<?php
use yii\widgets\ActiveForm;
$this->title = 'Upload';
?>
    <div class="site-index">

    <div class="jumbotron">
        <h1>upload file</h1>
    </div>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'file')->fileInput() ?>

    <button type="submit" onclick="this.disabled = true; this.innerHTML='parsing process'">Parse</button>
<?php ActiveForm::end() ?>