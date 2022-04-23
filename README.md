<h1>Implementation of the test tasks</h1>

<h5>Configure</h5>
Set up Mailchimp API key in index.php and the message data.

<h5>Run docker</h5>
Docker will run rabbitmq and install the required libs via composer.

`docker-compose up -d`

<h5>Run consumer script</h5>
In terminal run the consumer script.

`php index.php consumer` 

By default mailer type is set to `php` to use the standard php `mail()` function. To use Mailchimp run the following command 

`php index.php consumer mailchimp`

<h5>Push the messages to the queue</h5>
`php index.php producer`

