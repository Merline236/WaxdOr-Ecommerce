<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer){

      $this->mailer = $mailer;

    
    }

    public function send(
        string $from, //qui envoi
        string $to, //destinataire
        string $subject, //sujet
        string $template, //twig du mail
        array $context //contenu

        ): void
         {

            //on crée le mail templatedEmail
            $email = (new TemplatedEmail())
              ->from($from)
              ->to($to)
              ->subject($subject)
              ->htmlTemplate("emails/$template.html.twig")
              ->context($context);
              
              //on envoie le mail
              $this->mailer->send($email);
        }

}