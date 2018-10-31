<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ .'/../vendor/autoload.php';

$dotenv = new Dotenv(__DIR__ .'/../');
$dotenv->load();
$dotenv->required(['RABBITMQ_HOST','RABBITMQ_VHOST','RABBITMQ_PORT','RABBITMQ_USERNAME','RABBITMQ_PASSWORD']);

$connection = new AMQPStreamConnection(
	getenv('RABBITMQ_HOST'),
	getenv('RABBITMQ_PORT'),
	getenv('RABBITMQ_USERNAME'),
	getenv('RABBITMQ_PASSWORD'),
	getenv('RABBITMQ_VHOST')
);
$channel = $connection->channel();

$queueName = $exchangeName ='send-sendgrid-transactional-mail';

$channel->queue_bind($queueName, $exchangeName);

$emails = [];

$emails = array_map('trim', $emails);
$emails = array_unique($emails);

foreach ($emails as $email) {
	$message = [
		'templateId' => 'd-e252f1eaeaf2454d92a61f7cd259f4d8',
		'eventId' => '0f160482-ca35-41f4-9d29-6b58caee9890',
		'includeTicket' => 0,
		'from' => [
			'name' => 'AMSP ČR | Entry.do',
			'mail' => 'hello@entry.do',
		],
		'to' => [
			'name' => '',
			'mail' => $email,
		],
	];

	$msg = new AMQPMessage(json_encode($message));
	$channel->basic_publish($msg, $exchangeName);

	echo "[+] Published email to $email\n";
}

$channel->close();
$connection->close();

