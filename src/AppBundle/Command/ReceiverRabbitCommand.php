<?php
/**
 * Created by PhpStorm.
 * User: Andreu
 * Date: 05/03/2017
 * Time: 21:36
 */

namespace AppBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ReceiverRabbitCommand extends Command
{
    protected $output;
    protected $connection;
    protected $channel;
    protected $queue;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('rabbit:receiver')

            // the short description shown while running "php bin/console list"
            ->setDescription('Command to process a queue of messages that a sender sent.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('bla bla bla writing some else bla bla bla...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->openConnection('localhost', 5672, 'guest', 'guest');
        $this->setQueue('messaging');

        $this->output->writeln(' [*] Waiting for messages. To exit press CTRL+C');

        $callback = function($msg){
            $this->output->writeln("[x] Received " . $msg->body);
        };

        $this->consumeMessage($callback);

        while( true ) {
            $this->channel->wait();
        }
    }

    public function openConnection($host, $port, $user, $password)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
    }

    public function setQueue($queue){
        $this->queue = $queue;
        if( $this->channel )
        {
            $this->channel->queue_declare($this->queue, false, false, false, false);
        }
    }

    public function consumeMessage($callback)
    {
        $this->channel->basic_consume($this->queue, '', false, true, false, false, $callback);
    }

}