<?php
namespace App\Consumer;

use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class NotificationConsumer
 */
class MailSenderConsumer implements ConsumerInterface
{
    private $delayedProducer;

    private $entityManager;

    /**
     * MailSenderConsumer constructor.
     * @param ProducerInterface      $delayedProducer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ProducerInterface $delayedProducer, EntityManagerInterface $entityManager)
    {
        $this->delayedProducer = $delayedProducer;
        $this->entityManager = $entityManager;

        gc_enable();
    }

    /**
     * @var AMQPMessage $msg
     * @return void
     */
    public function execute(AMQPMessage $msg)
    {
        $body = $msg->getBody();

        echo 'В очереди '.$body.' ...'.PHP_EOL;

        try {
            if ($body == 'bad') {
                throw new \Exception();
            }

            echo 'Записано'.PHP_EOL;
        } catch (\Exception $exception) {
            echo 'ERROR'.PHP_EOL;
            $this->delayedProducer->publish($body);
        }

        $this->entityManager->clear();
        $this->entityManager->getConnection()->close();

        gc_collect_cycles();
    }
}