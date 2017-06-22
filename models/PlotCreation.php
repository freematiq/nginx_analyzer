<?php

namespace app\models;

use Yii;
use yii\base\Model;

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

    /**
     * Данный метод содержит запрос, который считает количество запросов в каждом интервале за промежуток времени.
     * :date_from - начальное время, :date_to - конечное время, :quantity - шаг деления интервала, измеряется в секундах.
     * @return array $plot
     */
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

    /**
     * Данный метод содержит запрос, который считает суммарное время запросов в каждом интервале за промежуток времени.
     * :date_from - начальное время, :date_to - конечное время, :quantity - шаг деления интервала, измеряется в секундах.
     * @return array $plot
     */
    public function sumtime()
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
                 select sum(query_time_numeric)/60 quantity, external.interval
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

    /**
     * Данный метод содержит запрос, который считает среднее время выполнения запросов в каждом интервале за промежуток времени.
     * :date_from - начальное время, :date_to - конечное время, :quantity - шаг деления интервала, измеряется в секундах.
     * @return array $plot
     */
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

    /**
     * Данный метод содержит запрос, который получает Top 20 ip, с которых было больше всего запросов
     * @return array $plot
     */
    public function groupbyuserip()
    {
        $plot = Yii::$app->db->createCommand(
            'SELECT user_ip::INET, count(*) queries 
                 FROM LOGS 
                 WHERE query_date BETWEEN :date_from AND :date_to 
                 GROUP BY user_ip 
                 ORDER BY queries 
                 DESC LIMIT 20', [
            'date_from' => $this->date_from,
            'date_to' => $this->date_to
        ])->queryAll();
        return $plot;
    }

    /**
     * Данный метод содержит запрос, который получает количество запросов с определеным кодом запроса
     * @return array $plot
     */
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

    /**
     * Данный метод содержит запрос, который получает Top 20 url, которые чаще всего обрабатываются
     * @return array $plot
     */
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

    /**
     * Данный метод содержит запрос, который получает общее время выполнения запросов с url в секундах (20 самых долгих)
     * @return array $plot
     */
    public function groupbytime()
    {
        $plot = Yii::$app->db->createCommand(
            'SELECT sum(query_time_numeric) queries, url_query 
                 FROM logs
                 WHERE query_date BETWEEN :date_from AND :date_to
                 GROUP BY url_query 
                 ORDER BY queries  
                 DESC NULLS LAST LIMIT 20', [
            'date_from' => $this->date_from,
            'date_to' => $this->date_to
        ])->queryAll();
        return $plot;
    }

    /**
     * Данный метод содержит запрос, который получает время выполнения запросов в секундах (20 самых долгих)
     * @return string $provider
     */
    public function longestquery()
    {
        $provider = 'WITH external AS (
                  SELECT url_query, 
                         max(query_time_numeric) over wind, 
                         avg(query_time_numeric) over wind, 
                         min(query_time_numeric) over wind, 
                         query_date logs, 
                         query_time_numeric, 
                         CASE WHEN max(query_time_numeric) over wind = query_time_numeric then query_date end maxt, 
                         CASE WHEN min(query_time_numeric) over wind = query_time_numeric then query_date end mint 
                  FROM logs WHERE query_date BETWEEN :date_from AND :date_to
                  WINDOW wind as (partition by url_query)
                                    )
                  SELECT DISTINCT 
                         url_query, 
                         max Максимальное_время, 
                         round(avg, 3) Среднее_время, 
                         min Минимальное_время, 
                         max(maxt) over (partition by url_query) Время_максимального_запроса, 
                         min(mint) over (partition by url_query) Время_минимального_запроса 
                  FROM external
                  ORDER BY Максимальное_время DESC NULLS LAST';
        return $provider;
    }

    public function providetime()
    {
        return 0;
    }
}

