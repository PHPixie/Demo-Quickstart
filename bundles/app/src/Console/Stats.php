<?php

namespace Project\App\Console;

use PHPixie\Console\Command\Config;
use PHPixie\Database\Driver\PDO\Connection;
use PHPixie\Slice\Data;

/**
 * Command to display message statistics
 */
class Stats extends Command
{
    /**
     * Configure your command
     * @param Config $config
     */
    protected function configure($config)
    {
        $config->description("Display statistics");
    }

    /**
     * @param Data $argumentData
     * @param Data $optionData
     */
    public function run($argumentData, $optionData)
    {
        // Get the database component
        $database = $this->components()->database();

        /** @var Connection $connection */
        $connection = $database->get();

        // Calculate total items
        $total = $connection->countQuery()
            ->table('messages')
            ->execute();

        $this->writeLine("Total messages: $total");

        // Calculate messages per user
        $stats = $connection->selectQuery()
            ->fields([
                'name' => 'u.name',
                'count' => $database->sqlExpression('COUNT(1)'),
            ])
            ->table('messages', 'm')
            ->join('users', 'u')
                ->on('m.userId', 'u.id')
            ->groupBy('u.id')
            ->execute();

        foreach($stats as $row) {
            $this->writeLine("{$row->name}: {$row->count}");
        }
    }
}