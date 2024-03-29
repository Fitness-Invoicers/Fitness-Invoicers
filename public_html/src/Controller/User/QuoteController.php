<?php

namespace App\Controller\User;

use App\Entity\Quote;
use App\Form\Quote\QuoteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    #[Route('/quote', name: 'app_user_quote_index')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $quote = $entityManager->getRepository(Quote::class)->findAll();

        return $this->render('quotes/quote_index.html.twig', [
            'quote' => $quote,
        ]);
    }

    #[Route('/add', name: 'app_user_quote_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quote = new Quote();
        $form = $this->createForm(QuoteFormType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quote);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('action.html.twig', [
            'action' => 'Ajouter un devis',
            'quote' => $quote,
            'form' => $form,
        ]);
    }

    #[Route('quote/edit/{id}', name: 'app_user_quote_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuoteFormType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_quote_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('action.html.twig', [
            'action' => 'Modifier le devis n°' . $quote->getId(),
            'quote' => $quote,
            'form' => $form,
        ]);
    }

    #[Route('quote/delete/{id}', name: 'app_user_quote_delete', methods: ['POST'])]
    public function delete(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quote->getId(), $request->request->get('_token'))) {
            $entityManager->remove($quote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_quote_index', [], Response::HTTP_SEE_OTHER);
    }
}
