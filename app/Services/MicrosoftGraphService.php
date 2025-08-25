<?php

namespace App\Services;

use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MicrosoftGraphService
{
    protected $clientId;
    protected $clientSecret;
    protected $tenantId;
    protected $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.microsoft.client_id');
        $this->clientSecret = config('services.microsoft.client_secret');
        $this->tenantId = config('services.microsoft.tenant');
        $this->redirectUri = config('services.microsoft.redirect');
    }

    /**
     * Get an access token for Microsoft Graph API
     */
    public function getAccessToken()
    {
        // Check if we have a cached token
        $cachedToken = Cache::get('microsoft_graph_token');
        if ($cachedToken && !$this->isTokenExpired($cachedToken)) {
            return $cachedToken['access_token'];
        }

        // Get new token using client credentials flow
        try {
            $token = $this->getClientCredentialsToken();
            
            // Cache the token
            Cache::put('microsoft_graph_token', $token, now()->addSeconds($token['expires_in'] - 300));
            
            return $token['access_token'];
            
        } catch (\Exception $e) {
            Log::error('Failed to get Microsoft Graph access token', [
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get token using client credentials flow
     */
    protected function getClientCredentialsToken()
    {
        $client = new Client();
        
        $response = $client->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope' => 'https://graph.microsoft.com/.default'
            ]
        ]);

        $tokenData = json_decode($response->getBody()->getContents(), true);
        
        if (!isset($tokenData['access_token'])) {
            throw new \Exception('Failed to get access token from Microsoft');
        }
        
        return $tokenData;
    }

    /**
     * Check if token is expired
     */
    protected function isTokenExpired($token)
    {
        // Calculate expiration time based on expires_in
        $expiresAt = time() + $token['expires_in'];
        return time() >= $expiresAt;
    }

    /**
     * Send email using Microsoft Graph API
     */
    public function sendEmail($to, $subject, $body, $from = null)
    {
        try {
            // Create Graph client with client credentials
            $tokenRequestContext = new ClientCredentialContext(
                tenantId: $this->tenantId,
                clientId: $this->clientId,
                clientSecret: $this->clientSecret
            );

            $graphClient = new GraphServiceClient($tokenRequestContext);

            // Create the email message using proper Graph objects
            $emailMessage = new \Microsoft\Graph\Generated\Users\Item\SendMail\SendMailPostRequestBody();
            
            // Create message object
            $message = new \Microsoft\Graph\Generated\Models\Message();
            $message->setSubject($subject);
            
            // Create body object
            $bodyObject = new \Microsoft\Graph\Generated\Models\ItemBody();
            $bodyObject->setContentType(new \Microsoft\Graph\Generated\Models\BodyType('html'));
            $bodyObject->setContent($body);
            $message->setBody($bodyObject);
            
            // Create recipients
            $recipients = [];
            foreach ((array) $to as $email) {
                $recipient = new \Microsoft\Graph\Generated\Models\Recipient();
                $emailAddress = new \Microsoft\Graph\Generated\Models\EmailAddress();
                $emailAddress->setAddress($email);
                $recipient->setEmailAddress($emailAddress);
                $recipients[] = $recipient;
            }
            $message->setToRecipients($recipients);
            
            // Set from address
            if ($from) {
                $fromRecipient = new \Microsoft\Graph\Generated\Models\Recipient();
                $fromEmailAddress = new \Microsoft\Graph\Generated\Models\EmailAddress();
                $fromEmailAddress->setAddress($from);
                $fromRecipient->setEmailAddress($fromEmailAddress);
                $message->setFrom($fromRecipient);
            }
            
            $emailMessage->setMessage($message);
            $emailMessage->setSaveToSentItems(true);

            // Send email using Microsoft Graph API with application permissions
            // For now, let's try a simpler approach using the application's identity
            try {
                // Try to send using the application's identity (if it has Mail.Send permission)
                $response = $graphClient->users()->byUserId($from)->sendMail()->post($emailMessage);
            } catch (\Exception $e) {
                // If that fails, try to find the user first
                try {
                    $users = $graphClient->users()->get();
                    $user = null;
                    
                    foreach ($users->getValue() as $u) {
                        if ($u->getMail() === $from || $u->getUserPrincipalName() === $from) {
                            $user = $u;
                            break;
                        }
                    }
                    
                    if ($user) {
                        // Send email as this user
                        $response = $graphClient->users()->byUserId($user->getId())->sendMail()->post($emailMessage);
                    } else {
                        throw new \Exception("User with email {$from} not found in the tenant");
                    }
                } catch (\Exception $innerException) {
                    throw new \Exception("Failed to send email via Microsoft Graph: " . $innerException->getMessage());
                }
            }

            Log::info('Email sent successfully via Microsoft Graph', [
                'to' => $to,
                'subject' => $subject,
                'response' => $response
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Failed to send email via Microsoft Graph', [
                'error' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject
            ]);

            // Fallback: Log the email locally so it's not completely lost
            Log::info('Email logged locally due to Microsoft Graph failure', [
                'to' => $to,
                'subject' => $subject,
                'body' => $body,
                'from' => $from,
                'timestamp' => now()->toISOString()
            ]);

            throw $e;
        }
    }

    /**
     * Test the Microsoft Graph connection
     */
    public function testConnection()
    {
        try {
            // Create Graph client with client credentials
            $tokenRequestContext = new ClientCredentialContext(
                tenantId: $this->tenantId,
                clientId: $this->clientId,
                clientSecret: $this->clientSecret
            );

            $graphClient = new GraphServiceClient($tokenRequestContext);

            // Test connection by trying to get users list (requires application permissions)
            $response = $graphClient->users()->get();
            $userCount = count($response->getValue());

            return [
                'success' => true,
                'message' => "Microsoft Graph connection successful. Found {$userCount} users in tenant.",
                'user' => $response
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Microsoft Graph connection failed: ' . $e->getMessage()
            ];
        }
    }
}
