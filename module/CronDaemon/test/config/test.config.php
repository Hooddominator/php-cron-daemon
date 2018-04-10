<?php

return array(
    'cron_daemon' => array(
        'tasks' => array(
            array(
                "work_count" => 1,
                "arguments" => array(__DIR__."/script/loop.php"),
            ),
            array(
                "work_count" => 1,
                "arguments" => array(__DIR__."/script/test.php"),
            ),
            array(
                "work_count" => 1,
                "schedule" => "* * * * *",
                "arguments" => array(__DIR__."/script/loop.php"),
                "timeout" => 10
            )
        )
    )
);

?>
