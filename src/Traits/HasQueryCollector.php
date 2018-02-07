<?php

namespace Nip\DebugBar\Traits;

use Nip\Database\Connections\Connection;
use Nip\Database\DatabaseManager;
use Nip\DebugBar\DataCollector\QueryCollector;
use Nip\Profiler\Adapters\DebugBar as ProfilerDebugBar;

/**
 * Trait HasQueryCollector
 * @package Nip\DebugBar\Traits
 */
trait HasQueryCollector
{
    public function doBootQueryCollector()
    {
        if (app()->has('db')) {
            $this->addQueryCollector();
        }
    }

    public function addQueryCollector()
    {
        $this->addCollector(new QueryCollector());

        $databaseManager = app('db');
        $databaseManager->connection();

        $this->populateQueryCollector();
    }

    public function populateQueryCollector()
    {
        /** @var DatabaseManager $databaseManager */
        $databaseManager = app('db');
        $connections = $databaseManager->getConnections();

        foreach ($connections as $connection) {
            $this->initDatabaseConnection($connection);
        }
    }

    /**
     * @param Connection $connection
     */
    public function initDatabaseConnection($connection)
    {
        $profiler = $connection->getAdapter()->newProfiler()->setEnabled(true);
        $writer = $profiler->newWriter('DebugBar');

        /** @var ProfilerDebugBar $writer */
        $writer->setCollector($this->getCollector('queries'));
        $profiler->addWriter($writer);
        $connection->getAdapter()->setProfiler($profiler);
    }
}