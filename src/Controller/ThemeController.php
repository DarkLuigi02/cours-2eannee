<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ThemeType;
use App\Entity\Theme;
use Symfony\Component\HttpFoundation\Request;


class ThemeController extends AbstractController
{
    #[Route('/ajoutTheme', name: 'ajoutTheme')]
    public function ajoutTheme(Request $request): Response
    {
        $theme = new Theme();
        $form = $this->createform(ThemeType::class, $theme);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $this->addFlash('notice',"Merci de votre inscription");
                $em = $this-> getDoctrine()->getManager();
                $em->persist($theme);
                $em->flush();
                return $this->redirectToRoute('ajoutTheme');
            }
        }  

        return $this->render('theme/ajoutTheme.html.twig', ['form'=>$form->createView()]); 
    }

    #[Route('/miles/liste-theme', name: 'listetheme')]
    public function ListeTheme(): Response
    {
        $repotheme = $this->getDoctrine()->getRepository(Theme::class);
        $themes = $repoTheme->findBy(array(),array('nom'=>'ASC'));
        
        
        return $this->render('theme/listetheme.html.twig', ['themes'=>$themes]);
    }
}
