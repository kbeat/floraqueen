<?php
    require_once __DIR__ . '/vendor/autoload.php';

    // variables
    $channel = 'test';
    $sampleEmail = [
        'email' => 'receiver@gmail.com',
        'subject' => 'Test subject',
        'message' => 'One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin. He lay on his armour-like back, and if he lifted his head a little he could see his brown belly, slightly domed and divided by arches into stiff sections.'
    ];
    $api_key = 'MAILCHIMP_API_KEY_HERE';

    $scriptMode = !empty($argv[1]) ? $argv[1] : 'consumer';

    // set the mailer type for consumer
    $config = [];
    if ($scriptMode === 'consumer') {
        $mailerType = !empty($argv[2]) ? $argv[2] : 'php';
        if ($mailerType === 'mailchimp') {
            $config['api_key'] = $api_key;
        }
    }

    $tester = new QueueTest('localhost');

    if ($scriptMode === 'consumer') {
        $tester->consume($channel, $mailerType, $config);
        $tester->wait();
    } else {
        $tester->produce($channel, $sampleEmail);
    }