<?php

$log = new \Monolog\LogRecord(
    new DateTimeImmutable(),
    'production',
    \Monolog\Level::from(100),
    'Test log message',
);

$log = $log->with(...[
    'message' => 'Test log message',
    'context' => [],
    'extra' => [],
]);

return $log;