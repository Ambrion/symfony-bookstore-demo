<?php

declare(strict_types=1);

namespace App\Front\Controller;

use App\Contact\DTO\ContactDTO;
use App\Contact\Entity\Contact;
use App\Contact\Factory\ContactFactoryInterface;
use App\Contact\Service\ContactServiceInterface;
use App\Front\Form\ContactForm;
use App\Mail\Message\SendContactEmailMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactPageController extends AbstractController
{
    public function __construct(
        private readonly ContactFactoryInterface $contactFactory,
        private readonly ContactServiceInterface $contactService,
        private readonly MessageBusInterface $bus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/contact', name: 'app_front_contact_page_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactForm::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contactRequestDTO = new ContactDTO(
                email: $contact->getEmail(),
                message: $contact->getMessage(),
                name: $contact->getName(),
                phone: $contact->getPhone()
            );

            $contact = $this->contactFactory->create($contactRequestDTO);
            $contactId = $this->contactService->createContact($contact);
            if ($contactId) {
                $this->bus->dispatch(new SendContactEmailMessage($contactId));

                $this->addFlash(
                    'success',
                    'Ваша сообщение успешно отправлено!'
                );

                return $this->redirectToRoute('app_front_contact_page_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('front/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
