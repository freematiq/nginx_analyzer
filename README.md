# nginx analyzer

This application works with nginx logs. Two formats of incoming data are available:
1) server's ip - the date of query - the type of query - the incoming url of query - the code of query - the size of query - the time of query - the url of page that was quested - user agents - user's ip
2) The same as first but without the time of query

_Before starting be sure that your data in format 1 or 2!_

To start parsing you can use command line. Choose folder with this application and use command bellow

`php yii parse/parse <filename>`

_File should be in the root folder of the project_

Also you can use browser interface for parsing. Choose section "Парсер логов". In this case you can take file from anywhere on your PC.

_Be careful! Parsing through browser works fine only with small files. For good productivity parse your file through command line._

If you work with command line wait for message "Успешно загружено". If you work with browser wait for message "Файл добавлен в БД".

After successful parsing you can analyze uploaded data. Choose tab "График из файла", find the raw with file you want and press link in the last column. You will be automatically redirected to tab "Графики" with next parameters: minimum and maximum dates that is contained in file and step which counts as difference between maximum and minimum dates divided to 48 parts.

_Be careful. Large time gaps could contain data from another files because there is no filtering by file, application uses only time gaps._

For your own selection you can use tab "Графики" directly. Choose time period you want and interval which actually is the step of fragmentation. Interval measures in seconds (60 means one minute, 120 means two minutes and so on). For example, if you choose period in one day you can split it by hours using 3600 as interval value.

_Be careful! If you choose a 1 year period and set interval step as 1 hour you risk to not get results._

After successful choosing of period and step you can analyze your data on 5 charts and 2 tables.

##### Chart 1

This chart shows the number of requests and their average execution time at the time point. Use labels under chart for filtering.

##### Chart 2

This chart shows top 20 ip, from which there were the most queries ordered by the quantity of queries.

##### Chart 3

This chart shows the number of requests with a defined request code.

##### Chart 4

This chart shows top 20 url, which are most often processed.

##### Chart 5

This chart shows total time of execution of queries with url in seconds.

##### Table 1

This table shows request execution time in seconds. If maximum or minimum time of request reached more often than once in two last columns you can see latest of these time.

##### Table 2

This table shows number of request codes from url (all, except 200). If period of time contains only code 200 you will see concominant message.