<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class SendEmailService
{
    public function __construct(private MailerInterface $mailer)
    {}

    public function send(string $from, string $to, string $subject, string $template, array $context): void
    {
        // Création du mail

        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/$template")
            ->context($context);

        // Envoie du mail
        $this->mailer->send($email);
    }
}