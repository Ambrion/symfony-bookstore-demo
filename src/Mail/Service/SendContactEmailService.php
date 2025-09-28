<?php

declare(strict_types=1);

namespace App\Mail\Service;

use App\Contact\DTO\ContactDTO;
use App\Mail\DTO\EmailDTO;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class SendContactEmailService implements SendContactEmailServiceInterface
{
    public function __construct(
        private Environment $twig,
        private EmailManagerInterface $emailManager,
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Exception
     */
    public function sendContactEmail(string $from, string $to, ContactDTO $contactDTO): void
    {
        $templatePath = 'email/email-contact.twig';
        $subject = 'Новое сообщение с сайта';
        $context = [
            'subject' => $subject,
            'name' => $contactDTO->name,
            'phone' => $contactDTO->phone,
            'email' => $contactDTO->email,
            'message' => $contactDTO->message,
        ];

        $htmlBody = $this->twig->render($templatePath, $context);

        $emailDTO = new EmailDTO(
            from: $from,
            to: $to,
            subject: $subject,
            html: $htmlBody
        );

        try {
            $this->emailManager->sendEmail($emailDTO, true);
        } catch (\Exception $exception) {
            throw new \Exception('Ошибка отправки email: '.$exception->getMessage());
        }
    }
}
