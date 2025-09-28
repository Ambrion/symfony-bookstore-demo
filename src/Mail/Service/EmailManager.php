<?php

declare(strict_types=1);

namespace App\Mail\Service;

use App\Mail\DTO\EmailDTO;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

readonly class EmailManager implements EmailManagerInterface
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmail(EmailDTO $emailDTO, bool $isHtml = false): void
    {
        if ($isHtml) {
            $class = new TemplatedEmail();
        } else {
            $class = new Email();
        }

        $email = $class
            ->from($emailDTO->from)
            ->to($emailDTO->to)
            ->subject($emailDTO->subject);

        if ($isHtml) {
            $email->html($emailDTO->html);
        } else {
            $email->text($emailDTO->text);
        }

        $this->mailer->send($email);
    }
}
