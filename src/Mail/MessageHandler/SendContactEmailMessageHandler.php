<?php

declare(strict_types=1);

namespace App\Mail\MessageHandler;

use App\Contact\DTO\ContactDTO;
use App\Contact\Repository\ContactRepositoryInterface;
use App\Mail\Message\SendContactEmailMessage;
use App\Mail\Service\SendContactEmailServiceInterface;
use App\Settings\Service\SettingManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SendContactEmailMessageHandler
{
    public function __construct(
        private SendContactEmailServiceInterface $sendContactEmailService,
        private ParameterBagInterface $parameterBag,
        private SettingManagerInterface $settingManager,
        private ContactRepositoryInterface $contactRepository,
    ) {
    }

    public function __invoke(SendContactEmailMessage $sendContactEmailJob): void
    {
        $contact = $this->contactRepository->findOneContactBy(['id' => $sendContactEmailJob->getId()]);
        if (null !== $contact) {
            $contactDTO = new ContactDTO(
                email: $contact->getEmail(),
                message: $contact->getMessage(),
                name: $contact->getName(),
                phone: $contact->getPhone()
            );

            $from = $this->parameterBag->get('app.email_from');

            $settingsTo = null;
            $defaultTo = $this->parameterBag->get('app.manager_contact_form_email');
            $settingTo = $this->settingManager->findOneByName('app.manager_contact_form_email');
            if (!empty($settingTo)) {
                $settingsTo = $settingTo->value;
            }

            $to = (string) ($settingsTo ?: $defaultTo);

            $this->sendContactEmailService->sendContactEmail($from, $to, $contactDTO);
        }
    }
}
