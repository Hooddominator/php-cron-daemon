# php-cron-daemon

This a library to enable daemon task with php application using zf2 and pcntl extension.

Install using
```
composer require wiryonolau/php-cron-daemon
```

Add to your module.config.php, below is example
```
"cron_daemon" => array(
    array(
        "program" => "/usr/bin/php"
        "arguments" => array("/public/index.php", "application", "init", "-y"),
        "schedule" => "* * * * *",
        "work_count" => 1,
        "timeout" => 10
    )
)
```

Parameters
- program : an executable
- arguments : script path and their argument separate with array
- schedule (optional) :
  - default to false, it will be run as many as work_count once, useful for infinite loop script such as rabbitmq worker
  - if specify ( must use cronjob format ) , daemon will run it when schedule is reach
- work_count ( optional ) : default to 1 maximum 10, how many script have to run for current task
- timeout (optional) : task timeout in second default is 0 no timeout 

To run the script as daemon you can use either init.d or systemd

If you use php task that using phpampqlib set set_close_on_destruct to false, so daemon can kill it gracefully without leaving zombie process
```
$connection->set_close_on_destruct(false);
```