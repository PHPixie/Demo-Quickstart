<?php

namespace Project\App\Console;

use PHPixie\Console\Command\Config;
use PHPixie\Slice\Data;

/**
 * Command to display latest messages in the console
 */
class Messages extends Command
{
    /**
     * Command configuration
     * @param Config $config
     */
    protected function configure($config)
    {
        // Set command description
        $config->description("Print latest messages");

        // add an option for filtering by userId
        $config->option('userId')
            ->description("Only print messages of this user");

        // add an argument to configure the amount of messages
        $config->argument('limit')
            ->description("Maximum number of messages to display, default is 5");
    }

    /**
     * @param Data $argumentData
     * @param Data $optionData
     */
    public function run($argumentData, $optionData)
    {
        $limit = $argumentData->get('limit', 5);

        $query = $this->components()->orm()->query('message')
            ->orderDescendingBy('date')
            ->limit($limit);

        $userId = $optionData->get('userId');
        if($userId) {
            $query->relatedTo('user', $userId);
        }

        // Get an array of found messages
        $messages = $query->find(['user'])->asArray();

        if(empty($messages)) {
            $this->writeLine("No messages found");
        }

        // Print the messages
        foreach($messages as $message) {
            $dateTime = new \DateTime($message->date);

            $this->writeLine($message->text);
            $this->writeLine(sprintf(
                "by %s on %s",
                $message->user()->name,
                $dateTime->format('j M Y, H:i')
            ));

            $this->writeLine();
        }
    }
}