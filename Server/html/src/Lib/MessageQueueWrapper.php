<?php

namespace App\Lib;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageQueueWrapper
{

  # Send an array of JSON message packets to RabbitMQ
  public function send($messages, $queueName)
  {
    # Configure RabbitMQ, and submit the latest data points to it
    # for rendering on the dashboard map.
    $connection = new AMQPStreamConnection('rabbitmq', 5672, 'rabbit', 'rabbit');
    $channel = $connection->channel();
    $channel->queue_declare($queueName, false, false, false, false);
    $channel->exchange_declare($queueName, 'fanout', false, false, false);
    $channel->queue_bind($queueName, $queueName);
    foreach ($messages as $message) {
      # Send data to RabbitMQ
      try {
        $amqpMessage = new AMQPMessage($message);
        $channel->basic_publish($amqpMessage, $queueName, $queueName);
        print_r("Published to ".$queueName." - ".$message."\n");
      } catch (\Exception $exception) {
        // Couldn't connect to AMQP server. Should probably create a notification here
        // but with a flag to only create one, so there aren't a ton of notifications created
        // every time the server dies for some reason.
        // $shell->out($e);
        //   print_r($e);die("?");
        print_r($exception);
      }
    }
    $channel->close();
    $connection->close();
  }
}
