<?php

namespace App\Mail\Transport;

use Illuminate\Mail\Transport\Transport;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Swift_Mime_SimpleMessage;
use Illuminate\Support\Facades\Log;

class MicrosoftGraphTransport extends Transport
{
    protected $graphClient;
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->graphClient = $this->createGraphClient();
    }

    protected function createGraphClient()
    {
        // Create Graph client with client credentials
        $tokenRequestContext = new ClientCredentialContext(
            tenantId: config('services.microsoft.tenant'),
            clientId: config('services.microsoft.client_id'),
            clientSecret: config('services.microsoft.client_secret')
        );

        return new GraphServiceClient($tokenRequestContext);
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        try {
            // Get message details
            $from = $this->getFromAddress($message);
            $to = $this->getToAddresses($message);
            $subject = $message->getSubject();
            $body = $this->getMessageBody($message);

            // Create the email message using proper Graph objects
            $emailMessage = new \Microsoft\Graph\Generated\Users\Item\SendMail\SendMailPostRequestBody();
            
            // Create message object
            $messageObj = new \Microsoft\Graph\Generated\Models\Message();
            $messageObj->setSubject($subject);
            
            // Create body object
            $bodyObject = new \Microsoft\Graph\Generated\Models\ItemBody();
            $bodyObject->setContentType('HTML');
            $bodyObject->setContent($body);
            $messageObj->setBody($bodyObject);
            
            // Create recipients
            $recipients = [];
            foreach ($to as $email) {
                $recipient = new \Microsoft\Graph\Generated\Models\Recipient();
                $emailAddress = new \Microsoft\Graph\Generated\Models\EmailAddress();
                $emailAddress->setAddress($email);
                $recipient->setEmailAddress($emailAddress);
                $recipients[] = $recipient;
            }
            $messageObj->setToRecipients($recipients);
            
            // Set from address
            $fromRecipient = new \Microsoft\Graph\Generated\Models\Recipient();
            $fromEmailAddress = new \Microsoft\Graph\Generated\Models\EmailAddress();
            $fromEmailAddress->setAddress($from);
            $fromRecipient->setEmailAddress($fromEmailAddress);
            $messageObj->setFrom($fromRecipient);
            
            $emailMessage->setMessage($messageObj);
            $emailMessage->setSaveToSentItems(true);

            // Send email using Microsoft Graph API
            $response = $this->graphClient->me()->sendMail()->post($emailMessage);

            Log::info('Email sent via Microsoft Graph', [
                'to' => $to,
                'subject' => $subject,
                'response' => $response
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Failed to send email via Microsoft Graph', [
                'error' => $e->getMessage(),
                'to' => $to ?? [],
                'subject' => $subject ?? 'Unknown'
            ]);

            throw $e;
        }
    }

    protected function getFromAddress($message)
    {
        $from = $message->getFrom();
        if (is_array($from)) {
            return array_keys($from)[0];
        }
        return $from;
    }

    protected function getToAddresses($message)
    {
        $to = $message->getTo();
        if (is_array($to)) {
            return array_keys($to);
        }
        return [$to];
    }

    protected function getMessageBody($message)
    {
        $body = $message->getBody();
        
        // If no HTML body, try to get text body
        if (empty($body)) {
            $body = $message->getChildren()[0]->getBody() ?? '';
        }
        
        return $body;
    }
}
