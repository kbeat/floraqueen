<?php

use MailchimpTransactional\ApiClient;

class MailchimpService {
    protected string $from;
    protected ApiClient $client;

    public function __construct(
        string $api_key,
        $from = 'hello@mail.com'
    )
    {
        $this->client = new ApiClient();
        $this->client->setApiKey($api_key);

        $this->from = $from;
    }

    public function sendMail(object $data)
    {
        return $this->client->messages->send([
            'message' => [
                'from_email' => $this->from,
                'to' => [
                    ['email' => $data->email]
                ],
                'subject' => $data->subject,
                'html' => $data->message
            ]
        ]);
    }
}