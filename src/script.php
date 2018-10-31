<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Nette\Utils\Json;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ .'/../vendor/autoload.php';

if ($argc !== 4) {
	throw new InvalidArgumentException('Invalid usage: "php src/script.php <sourceFile> <queueName> <exchangeName>"');
}

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

$filePath = $argv[1];
$queueName = $argv[2];
$exchangeName = $argv[3];

$fileContent = file_get_contents($filePath);
$data = Json::decode($fileContent);

$channel->queue_bind($queueName, $exchangeName);

foreach ($data as $index => $message) {
	$msg = new AMQPMessage(Json::encode($message));

	$channel->basic_publish($msg, $exchangeName);

	echo "[+] Message $index published\n";
}

echo "[x] Publishing finished\n";

$channel->close();
$connection->close();

