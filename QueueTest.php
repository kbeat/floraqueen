<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueTest {

    private $channel;

    /**
     * @param $host
     * @param $username
     * @param $password
     * @param $port
     */
    public function __construct(
        $host,
        $username = 'guest',
        $password = 'guest',
        $port = 5672
    )
    {
        $connection = new AMQPStreamConnection($host, $port, $username, $password);
        $this->channel = $connection->channel();
    }

    /**
     * Producer publishes an email object.
     *
     * @param string $channel
     * @param object $email
     * @return void
     */
    public function produce(string $channelName, array $email)
    {
        $this->declareChannel($channelName);
        $msg = new AMQPMessage(json_encode($email));
        $this->channel->basic_publish($msg, '', $channelName);
    }

    /**
     * Consumer reads the queue messages.
     *
     * @param $channelName
     * @param $mailerType
     * @param $config
     * @return void
     */
    public function consume($channelName, $mailerType = 'php', $config = [])
    {
        $this->declareChannel($channelName);
        $this->channel->basic_consume($channelName, '', false, false, false, false, function ($message) use ($mailerType, $config) {
            $data = json_decode($message->body);
            $this->sendMail($data, $mailerType, $config);
        });
    }

    public function wait()
    {
        while ($this->channel->is_open()) {
            $this->channel->wait();
        }
    }

    private function declareChannel(string $channel)
    {
        $this->channel->queue_declare($channel);
    }

    /**
     * If type set to 'mailchimp' then $config should contain api_key value.
     *
     * @param $data
     * @param $type
     * @param $config
     * @return void
     * @throws Exception
     */
    private function sendMail($data, $type, $config = [])
    {
        if (!empty($data->email) && !empty($data->subject) && !empty($data->message)) {
            if ($type === 'php') {
                mail(
                    $data->email,
                    $data->subject,
                    $data->message
                );
            } elseif ($type === 'mailchimp') {
                if (!empty($config['api_key'])) {
                    $mailchimpService = new MailchimpService($config['api_key']);
                    $response = $mailchimpService->sendMail($data);
                    if ($response[0]->status !== 'sent') {
                        print($response[0]->reject_reason . "\n");
                    }
                }
            } else {
                print("This mailer type is not supported.\n");
            }
        }
    }
}