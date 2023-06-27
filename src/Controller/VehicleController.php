<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Vehicle;
use App\Form\SearchType;
use App\Form\VehicleType;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class VehicleController extends AbstractController
{

    #[Route('/catalogue', name: 'vehicle.index', methods: ['GET'])]
    public function index(VehicleRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchType::class, $data);
        $form->handleRequest($request);
        $vehicles = $repository->findSearch($data);


        return $this->render('pages/vehicle/index.html.twig', [
            'vehicles' => $vehicles,
            'form' => $form->createView()
        ]);
    }

    #[Route('/vehicle/add', 'vehicle.new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle = $form->getData();

            // gestion de l'upload d'image
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                try {
                    $brochureFile->move(
                        $this->getParameter('vehicle_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    print "Erreur dans l'upload de l'image";
                }
                $vehicle->setImage($newFilename);

                $this->addFlash(
                    'success',
                    'Vehicule créé avec succès'
                );
            }

            $manager->persist($vehicle);
            $manager->flush();

            return $this->redirectToRoute('vehicle.index');
        }

        return $this->render(
            'pages/vehicle/new.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/vehicle/edit/{id}', 'vehicle.edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        EntityManagerInterface $manager,
        Vehicle $vehicle,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle = $form->getData();

            // gestion de l'upload d'image
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                try {
                    $brochureFile->move(
                        $this->getParameter('vehicle_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    print "Erreur dans l'upload de l'image";
                }
                $vehicle->setImage($newFilename);

                
            }

            $this->addFlash(
                'success',
                'Vehicule modifié avec succès'
            );

            $manager->persist($vehicle);
            $manager->flush();


            return $this->redirectToRoute('vehicle.index');
        }

        return $this->render('pages/vehicle/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/vehicle/delete/{id}', 'vehicle.delete', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(EntityManagerInterface $manager, Vehicle $vehicle): Response
    {

        if(!$vehicle){
            $this->addFlash(
                'success',
                "Le véhicule n'a pas été trouvé"
            );
        }else {
            $this->addFlash(
                'success',
                "Le véhicule a bien  été supprimé"
            );
        }
        
        $manager->remove($vehicle);
        $manager->flush();

        return $this->redirectToRoute('vehicle.index');

    }
}
