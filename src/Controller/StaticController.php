<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use App\Form\AvisType;
use Symfony\Component\HttpFoundation\Request;

class StaticController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render('static/accueil.html.twig', []);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, \Swift_Mailer $mailer): Response
    {
        $form = $this->createform(ContactType::class);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $nom= $form->get('nom')->getData();
                $sujet= $form->get('sujet')->getData();
                $contenu= $form->get('message')->getData();
                $this->addFlash('notice',"les info sont bien envoyé");
                $message = (new \Swift_Message($form->get('sujet')->getData()))
                ->setFrom($from->get('email')->getData())
                ->setTo('coquelle.gael@gmail.com')
                //->setBody($from->get('message')->getData());
                ->setBody($this->renderView('emails/contact-mail.html.twig', array('nom'=>$nom, 'sujet'=>$sujet, 'message'=>$contenu)),'text/html');
                $mailer->send($message);
                return $this->redirectToRoute('contact');
            }
        }
        return $this->render('static/contact.html.twig', ['form'=>$form->createView()]);
    }
    
    #[Route('/avis', name: 'avis')]
    public function avis(Request $request): Response
    {
        $form = $this->createform(AvisType::class);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $nom = $form->get('nom')->getData();
                $this->addFlash('notice',"Merci d'avoir donné votre avis m.".$nom);
            }
        }
        return $this->render('static/avis.html.twig', ['form'=>$form->createView()]);
    }

    #[Route('/mention', name: 'mention')]
    public function mention(): Response
    {
        return $this->render('static/mention.html.twig', []);
    }

    #[Route('/apropos', name: 'apropos')]
    public function apropos(): Response
    {
        return $this->render('static/apropos.html.twig', []);
    }
}