<?php

namespace App\Controller\User;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Form\Invoice\InvoiceFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class InvoiceController extends AbstractController
{
    #[Route('{slug}/invoice', name: 'app_user_invoice_index')]
    public function list(EntityManagerInterface $entityManager, Company $company): Response
    {

        $invoices = $entityManager->getRepository(Invoice::class)->findBy(['company' => $company]);

        return $this->render('invoices/invoice_index.html.twig', [
            'invoices' => $invoices,
            'company' => $company,
        ]);
    }

    #[Route('{company_slug}/invoice/{id}', name: 'app_user_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoices/invoice_show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('{slug}/add', name: 'app_user_invoice_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, Company $company): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceFormType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('action.html.twig', [
            'action' => 'Ajouter une facture',
            'invoice' => $invoice,
            'form' => $form,
            'company' => $company,
        ]);
    }

    #[Route('{company_slug}/invoice/edit/{id}', name: 'app_user_invoice_edit', methods: ['GET', 'POST'])]
    #[ParamConverter('invoice', class: Invoice::class, options: ['mapping' => ['id' => 'id']])]
    #[ParamConverter('Company', class: Company::class, options: ['mapping' => ['company_slug' => 'slug']])]
    public function edit(Request $request, Company $company, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceFormType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('action.html.twig', [
            'action' => 'Modifier la facture n°' . $invoice->getId(),
            'categories' => $invoice,
            'form' => $form,
            'company' => $company,
        ]);
    }

    #[Route('{slug}/invoice/delete/{id}', name: 'app_user_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager, Company $company): Response
    {
        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_invoice_index', [
            'company' => $company,
        ], Response::HTTP_SEE_OTHER);
    }
}
