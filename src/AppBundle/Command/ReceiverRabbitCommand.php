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

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('rabbit:receiver')

            // the short description shown while running "php bin/console list"
            ->setDescription('Command to start up a FrontController which will receive a messages from a sender.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('bla bla bla writing some else bla bla bla...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('messaging', false, false, false, false);

        $this->output->writeln(' [*] Waiting for messages. To exit press CTRL+C');

        $callback = function($msg){
            $this->output->writln(printf("[x] Received %s", $msg));
        };

        $channel->basic_consume('messaging', '', false, true, false, false, $callback);

        while( true ) {
            $channel->wait();
        }
    }
}