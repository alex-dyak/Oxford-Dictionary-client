<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('word', TextType::class, ['row_attr' => ['class' => 'form-floating']])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return $this->redirectToRoute('search_page', ['word' => $data['word']]);
        }

        return $this->renderForm('index.html.twig', [
            'form' => $form,
        ]);
    }

    public function search(Request $request): Response
    {
        return new Response(
            '<html><body>You are searching the word: '.$request->get('word').'</body></html>'
        );
    }
}
