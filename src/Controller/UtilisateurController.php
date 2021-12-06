<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\TwigConfig;
use App\Form\InscriptionType;
use App\Entity\Utilisateur;
use App\Entity\User;
use App\Entity\Fichier;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class UtilisateurController extends AbstractController
{
    #[Route('/miles/liste-utilisateur', name: 'listeutilisateur')]
    public function ListeUtilisateur(Request $request): Response
    {
        $doctrine = $this->getDoctrine();
        $em = $this->getDoctrine()->getManager();

        if ($request->get('id')!=null){
            $u = $doctrine->getRepository(Utilisateur::class)->find($request->get('id'));
            $em->remove($u);
            $em->flush();
            return $this->redirectToRoute('inscription');
        }

        $repoutilisateur = $this->getDoctrine()->getRepository(Utilisateur::class);
        $utilisateurs = $repoutilisateur->findBy(array(),array('nom'=>'ASC'));
        //$form = $this->createform(InscriptionType::class, $utilisateurs);
        
        $utilisateurs = $doctrine->getRepository(Utilisateur::class)->findBy(array(), array('nom'=>'ASC'));
        return $this->render('utilisateur/listeutilisateur.html.twig', ['utilisateurs'=>$utilisateurs]);
    }

    #[Route('/miles/modifutilisateur/{id}', name: 'modifutilisateur',requirements:["id"=>"\d+"])]
    public function modifUtilisateur(Request $request, int $id)
    {
        $utilisateur=$this->getDoctrine()->getRepository(Utilisateur::class)->find($id);

        $form = $this->createForm(InscriptionType::class,$utilisateur);
        if($request->isMethod('POST')){ 
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $em = $this-> getDoctrine()->getManager();
                $em->persist($utilisateur);
                $em->flush();

                return $this->redirectToRoute('listeutilisateur');
                
            }
        }               
          return $this->render('utilisateur/modifutilisateur.html.twig', ['form'=>$form->createView()]);  
    }

    #[Route('/profile', name: 'votreprofile')]
    public function profile(Request $request)
    {
        $utilisateur = new Utilisateur();
        $fichier = new Fichier();
        $doctrine = $this->getDoctrine();
        $this->getUser();

    return $this->render('utilisateur/profile.html.twig', ['fichiers'=>$fichier]);   
        
    }
}
