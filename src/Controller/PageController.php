<?php

namespace App\Controller;

use App\Entity\Chocolate;
use App\Form\ChocolateFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\String\Slugger\SluggerInterface;

class PageController extends AbstractController
{
    use TargetPathTrait;

    

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('page/contact.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/chocolate', name: 'chocolate')]
    public function chocolate(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Chocolate::class);
        $chocolates = $repository->findAll();
        return $this->render('page/chocolate.html.twig', [
            'controller_name' => 'PageController',
            'chocolates' => $chocolates
        ]);
    }

    #[Route('/testimonial', name: 'testimonial')]
    public function testimonial(): Response
    {
        return $this->render('page/testimonial.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/chocolate/new', name: 'new_chocolate')]
    public function newChocolate(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, SessionInterface $session): Response
    {

        $this->saveTargetPath($session, 'main', '/new');
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $chocolate = new Chocolate();

        $form = $this->createForm(ChocolateFormType::class, $chocolate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $file = $form->get('file')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                // Move the file to the directory where images are stored
                try {

                    $file->move(
                        $this->getParameter('images_directory'), $newFilename
                    );
                    $filesystem = new Filesystem();
                    $filesystem->copy(
                        $this->getParameter('images_directory') . '/'. $newFilename, 
                        $this->getParameter('portfolio_directory') . '/'.  $newFilename, true);

                } catch (FileException $e) {
                    return new Response("Error: " . $e->getMessage());
                }

                // updates the 'file$filename' property to store the PDF file name
                // instead of its contents
                $chocolate->setFile($newFilename);
            }
            
            $chocolate = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($chocolate);

            try {
                $entityManager->flush();
                return $this->redirectToRoute('chocolate');
            } catch (\Exception $e) {
                return new Response("Error: " . $e->getMessage());
            }
        }

        return $this->render('new.html.twig', [
            'controller_name' => 'PageController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/chocolate/edit/{id}', name: 'edit_chocolate')]
    public function editChocolate(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger, SessionInterface $session, $id): Response
    {

        $this->saveTargetPath($session, 'main', '/edit/' . $id);
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $doctrine->getRepository(Chocolate::class);
        $chocolate = $repository->find($id);

        if ($chocolate) {
            $form = $this->createForm(ChocolateFormType::class, $chocolate);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                $file = $form->get('file')->getData();
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                    // Move the file to the directory where images are stored
                    try {

                        $file->move(
                            $this->getParameter('images_directory'), $newFilename
                        );
                        $filesystem = new Filesystem();
                        $filesystem->copy(
                            $this->getParameter('images_directory') . '/'. $newFilename, 
                            $this->getParameter('portfolio_directory') . '/'.  $newFilename, true);

                    } catch (FileException $e) {
                        return new Response("Error: " . $e->getMessage());
                    }

                    // updates the 'file$filename' property to store the PDF file name
                    // instead of its contents
                    $chocolate->setFile($newFilename);
                }
                
                $chocolate = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($chocolate);

                try {
                    $entityManager->flush();
                    return $this->redirectToRoute('chocolate');
                } catch (\Exception $e) {
                    return new Response("Error: " . $e->getMessage());
                }
            }

            return $this->render('new.html.twig', [
                'controller_name' => 'PageController',
                'form' => $form->createView()
            ]);
        } else {
            return $this->redirectToRoute('chocolate'); 
        }
        
    }
}
