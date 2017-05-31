<?php

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;
use yii\grid\GridView;

$this->title = 'Start';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Графики</h1>
    </div>

    <?php

    $rows = (new \yii\db\Query())
        ->select(['date' => 'query_date', 'c' => 'count(*)'])
        ->from('logs')
        ->where(['between', 'query_date', '2017-05-25 09:00:00+07', '2017-05-25 10:00:00+07'])
        ->groupBy('query_date')
        ->orderBy('query_date')
        ->all();

    if ($rows) {
        foreach ($rows as $row)
            echo 'в момент времени ' . $row['date'] . ' было запросов: ' . $row['c'] . '<hr>';
    }

    /*        $dataProvider = new ActiveDataProvider([
                'query' => \app\models\Logs::findBySql('select * from logs'),
                'pagination' => [
                    'pageSize' => 100,
                ],
            ]);
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'query_date',
                    'sip',
                ],
            ]);*/
    ?>
</div>

</div>
