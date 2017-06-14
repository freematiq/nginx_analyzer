<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;

class PlotCreation extends Model
{
    public $date_from;
    public $date_to;
    public $interval_quantity;
    public $some_attribute;

    public function rules()
    {
        return [
            [['interval_quantity'], 'number'],
            [['date_from', 'date_to', 'interval_quantity'], 'required'],
        ];
    }

    public function creation()
    {
        $plot = Yii::$app->db->createCommand(
            'with external as (
                                  with interior as (
                                                    select range 
                                                    from generate_series(:date_from,:date_to,:quantity::interval) range
                                                    )
                                  select range as interval, lead(range) over (order by range) upperbound
                                  from interior
                                  )
                 select count(log_id) quantity, external.interval
                 from external
                 left outer join logs on logs.query_date >= interval and logs.query_date < upperbound
                 group by external.interval 
                 order by external.interval', [
            'quantity' => $this->interval_quantity,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to
        ])->queryAll();
        return $plot;
    }

    public function average()
    {
        $plot = Yii::$app->db->createCommand(
            'with external as (
                                  with interior as (
                                                    select range 
                                                    from generate_series(:date_from,:date_to,:quantity::interval) range
                                                    )
                                  select range as interval, lead(range) over (order by range) upperbound
                                  from interior
                                  )
                 select coalesce(avg(query_time_numeric),0) quantity, external.interval
                 from external
                 left outer join logs on logs.query_date >= interval and logs.query_date < upperbound
                 group by external.interval 
                 order by external.interval', [
            'quantity' => $this->interval_quantity,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to
        ])->queryAll();
        return $plot;
    }

    public function groupbysip()
    {
        $plot = Yii::$app->db->createCommand(
            'SELECT sip::INET, count(*) queries 
                 FROM LOGS 
                 WHERE query_date BETWEEN :date_from AND :date_to 
                 GROUP BY sip 
                 ORDER BY queries 
                 DESC LIMIT 20', [
            'date_from' => $this->date_from,
            'date_to' => $this->date_to
        ])->queryAll();
        return $plot;
    }

    public function groupbycode()
    {
        $plot = Yii::$app->db->createCommand(
            'SELECT count(*) queries, query_code 
                 FROM logs 
                 WHERE query_date BETWEEN :date_from AND :date_to
                 GROUP BY query_code 
                 ORDER BY queries DESC', [
                'date_from' => $this->date_from,
                'date_to' => $this->date_to
            ]
        )->queryAll();
        return $plot;
    }

    public function groupbyurl()
    {
        $plot = Yii::$app->db->createCommand(
            'SELECT count(*) queries, url_query
                 FROM LOGS 
                 WHERE query_date BETWEEN :date_from AND :date_to 
                 GROUP BY url_query
                 ORDER BY queries
                 DESC LIMIT 20', [
            'date_from' => $this->date_from,
            'date_to' => $this->date_to
        ])->queryAll();
        return $plot;
    }

    public function groupbytime()
    {
        $plot = Yii::$app->db->createCommand(
            'SELECT sum(query_time_numeric) queries, url_query 
                 FROM logs
                 WHERE query_date BETWEEN :date_from AND :date_to
                 GROUP BY url_query 
                 ORDER BY queries
                 DESC LIMIT 20', [
            'date_from' => $this->date_from,
            'date_to' => $this->date_to
        ])->queryAll();
        return $plot;
    }

    public function longestquery()
    {
        $provider = new SqlDataProvider([
            'sql' => 'SELECT url_query URL, 
                             query_time_numeric Время_выполнения, 
                             query_date Дата_запроса 
                      FROM logs 
                      WHERE query_date BETWEEN :date_from AND :date_to
                      ORDER BY query_time_numeric DESC',
            'totalCount' => 20,
        ]);
        return $provider;
    }
}

