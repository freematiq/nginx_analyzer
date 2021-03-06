<?php

/**
 * @var app\models\PlotReference $plotCreation
 */

use app\models\PlotReference;
use yii\data\SqlDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = 'Plot for uploaded file';
?>

<?php

$total = Yii::$app->db->createCommand('
                                           SELECT count(*) 
                                           FROM upload_history
                                           ')->queryScalar();

$provider = new SqlDataProvider([
    'sql' => 'SELECT filename_id,
              filename,
              date
              FROM upload_history
              ORDER BY date DESC',
    'totalCount' => (int)$total,
    'pagination' => [
        'pageSize' => 20,]]);

?>
<div class="container container-table">
    <h2>
        <p class="text-center">Загруженные файлы</p>
    </h2>
</div>

<?php

echo GridView::widget([
    'summary' => false,
    'dataProvider' => $provider,
    'captionOptions' => ['class' => 'h4 text-center text-info'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'filename_id',
            'label' => 'Номер файла в базе данных',],
        ['attribute' => 'filename',
            'label' => 'Имя файла в базе данных'],
        ['attribute' => 'date',
            'label' => 'Время загрузки файла'],
        ['label' => 'Построить график по этому файлу',
            'format' => 'raw',
            'content' => function ($plotCreation) {
                $plot = Yii::$app->db->createCommand(
                    'SELECT min(query_date) a, 
                        max(query_date) b 
                FROM logs 
                WHERE uploaded_file = :filename_id', ['filename_id' => $plotCreation['filename_id']])->queryAll();
                $diff = strtotime($plot[0]['b']) - strtotime($plot[0]['a']);
                $diff = $diff / 48;
                if ($diff===0) {$diff=1;}
                if (empty($plot[0]['a']) === false || empty($plot[0]['b']) === false) {
                    $url = \yii\helpers\Url::toRoute([
                        'parsing/plot',
                        'PlotCreation[date_from]' => $plot[0]['a'],
                        'PlotCreation[date_to]' => $plot[0]['b'],
                        'PlotCreation[interval_quantity]' => ceil($diff),
                    ]);
                } else
                    $url = \yii\helpers\Url::to([
                        'parsing/plotfromhistory'
                    ]);
                return Html::a('<span class="glyphicon glyphicon-share-alt"></span>', $url,
                    [
                        'title' => 'Перейти',
                        'target' => '_blank'
                    ]
                );
            }
        ]
    ]
]);

?>

<?php


?>
