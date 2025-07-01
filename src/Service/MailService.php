<?php

namespace App\Service;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;

class MailService
{
    private TransactionalEmailsApi $emailApi;

    public function __construct(string $apiKey)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
        $this->emailApi = new TransactionalEmailsApi(null, $config);
    }


    /**
     * @param mixed[]  $params
     * @param string $toEmail
     * @param string $toName
     * @param int    $template
     *
     * @return void
     */
    public function sendEmail(array $params, string $toEmail = "nomuas.nf@gmail.com", string $toName = "Nomuas", int $template = 1): void
    {
        $sendSmtpEmail = new SendSmtpEmail([
            'to' => [
                ['email' => $toEmail, 'name' => $toName]
            ],
            'templateId' => $template,
            'params' => $params
        ]);

        try {
            $this->emailApi->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error sending email: ' . $e->getMessage());
        }
    }
}
