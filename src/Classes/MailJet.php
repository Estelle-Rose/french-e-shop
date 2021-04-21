<?php


namespace App\Classes;

use Mailjet\Client;
use \Mailjet\Resources;

class MailJet
{
    private $apiKey = 'a6cf103ca584fc24cd43acfa1b82a0d2';
    private $apiSecretKey = 'f806a19b505e5107fcd41cc8ff2e2a83';

    public function send($to_email, $to_name, $subject, $content)
{
    $mj = new Client($this->apiKey,$this->apiSecretKey,true,['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "contact.aranea@gmail.com",
                    'Name' => "La boutique française"
                ],
                'To' => [
                    [
                        'Email' => $to_email,
                        'Name' => $to_name
                    ]
                ],
                'TemplateID' => 2826063,
                'TemplateLanguage' => true,
                'Subject' => $subject,
                'Variables' => [
                    'content' => $content
                ]
            ]
        ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success();

}
}