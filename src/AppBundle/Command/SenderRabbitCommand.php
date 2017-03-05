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
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Input\InputArgument;

class SenderRabbitCommand extends Command
{
    protected $output;
    protected $connection;
    protected $channel;
    protected $queue;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('rabbit:sender')

            // the short description shown while running "php bin/console list"
            ->setDescription('Command to send a message to rabbit queue.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('bla bla bla writing some else bla bla bla...')
            // new argument required
            ->addArgument('count', InputArgument::REQUIRED, 'Number of messages that you want to send.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->openConnection('localhost', 5672, 'guest', 'guest');
        $this->setQueue('messaging');
        $this->sendMessages($input->getArgument('count'));
        $this->closeConnection();
    }

    public function publishMessage(AMQPMessage $AMQPMessage)
    {
        $this->channel->basic_publish($AMQPMessage, '', 'messaging');
        $this->output->writeln('[x] Sent ' . $AMQPMessage->body);
    }

    public function openConnection($host, $port, $user, $password)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
    }

    public function closeConnection()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function setQueue($queue){
        $this->queue = $queue;
        if( $this->channel )
        {
            $this->channel->queue_declare($this->queue, false, false, false, false);
        }
    }

    public function sendMessages($count)
    {
        if($count > 0)
        {
            for($i = 1; $i <= $count; $i++)
            {
                $msg = new AMQPMessage('I am a message number ' . $i);
                $this->publishMessage($msg);
            }
        }
    }

}