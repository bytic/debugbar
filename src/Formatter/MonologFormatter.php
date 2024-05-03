<?php

declare(strict_types=1);

namespace Nip\DebugBar\Formatter;

use Monolog\Formatter\HtmlFormatter;
use Monolog\LogRecord;

/**
 * Class MonologFormatter
 * @package Nip\DebugBar\Formatter
 */
class MonologFormatter extends HtmlFormatter
{
    public function format(LogRecord $record): string
    {
        $title = $record->level->getName() . ' ' . $record->message;
        $return = str_pad($title, 100, ' ');
        $return .= parent::format($record);

        return $return;
    }
}
