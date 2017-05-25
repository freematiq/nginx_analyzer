<?php

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>nginx_analyzer</h1>
    </div>


    <?php
    $file = file_get_contents("/home/jaroslav/nginx_analyzer/log.txt");
    $rows = explode("\n", $file);
    array_pop($rows);

    foreach ($rows as $row => $data) {
        preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $data, $matches);
        //print_r($matches[0]);
        $string = $matches[0][5];
        $string = str_replace('"', '', $string);
        $row_data1 = explode(' ', $string);
        $row_data = $matches;
        $logs[$row]['sip'] = $row_data[0][0];
        $logs[$row]['query_date'] = str_replace('[', '', $row_data[0][3]) . str_replace(']', '', $row_data[0][4]);
        $logs[$row]['query_type'] = $row_data1[0];
        $logs[$row]['url_query'] = $row_data1[1];
        $logs[$row]['query_code'] = $row_data[0][6];
        $logs[$row]['query_size'] = $row_data[0][7];
        $logs[$row]['query_time'] = $row_data[0][8];
        $logs[$row]['quested_page'] = str_replace('"', '', $row_data[0][9]);
        $logs[$row]['browser_info'] = str_replace('"', '', $row_data[0][10]);
        $logs[$row]['user_ip'] = str_replace('"', '', $row_data[0][11]);

        echo '<br />';
        echo $row . ')' . ' sip: ' . $logs[$row]['sip'] . '<br />';
        echo $row . ')' . ' query_date: ' . $logs[$row]['query_date'] . '<br />';
        echo $row . ')' . ' query_type: ' . $logs[$row]['query_type'] . '<br />';
        echo $row . ')' . ' url_query: ' . $logs[$row]['url_query'] . '<br />';
        echo $row . ')' . ' query_code: ' . $logs[$row]['query_code'] . '<br />';
        echo $row . ')' . ' query_size: ' . $logs[$row]['query_size'] . '<br />';
        echo $row . ')' . ' query_time: ' . $logs[$row]['query_time'] . '<br />';
        echo $row . ')' . ' quested_page: ' . $logs[$row]['quested_page'] . '<br />';
        echo $row . ')' . ' browser_info: ' . $logs[$row]['browser_info'] . '<br />';
        echo $row . ')' . ' user_ip: ' . $logs[$row]['user_ip'] . '<br />';


        echo '<br />';
    } ?>


</div>
