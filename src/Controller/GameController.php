<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader; // @dth
use Symfony\Component\HttpFoundation\File\File; // @dth: edit persisted file


/**
 * @Route("/games")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/", name="game_index", methods={"GET"})
     */
    public function index(GameRepository $gameRepository): Response
    {
        return $this->render('game/index.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="game_new", methods={"GET","POST"})
     */
    // public function new(Request $request): Response
    public function new(Request $request,FileUploader $fileUploader)
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // file upload
            /** @var UploadedFile $gameFilename */
            $gameFilename = $form['gameFilename']->getData();
            if ($gameFilename) {
                $gameFilename = $fileUploader->upload($gameFilename);
                $game->setGameFilename($gameFilename);
            }

            // creation date
            $game->setCreatedAt(new \DateTime());

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('game_index');
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="game_show", methods={"GET"})
     */
    public function show(Game $game): Response
    {
        return $this->render('game/show.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="game_edit", methods={"GET","POST"})
     */
    // public function edit(Request $request, Game $game): Response
    public function edit(Request $request, Game $game,FileUploader $fileUploader)
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $this->getDoctrine()->getManager()->flush();
            $entityManager = $this->getDoctrine()->getManager();

            // file upload
            /** @var UploadedFile $gameFilename */
            $gameFilename = $form['gameFilename']->getData();
            if ($gameFilename) {
                // delete the old file
                $toDelete = new File($this->getParameter('gamefiles_directory').'/'.$game->getGameFilename());
                unlink($toDelete);

                // upload the new file
                $gameFilename = $fileUploader->upload($gameFilename);
                $game->setGameFilename($gameFilename);
            }

            //$entityManager->persist($game);
            $entityManager->flush();   

            return $this->redirectToRoute('game_index');
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="game_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Game $game): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            
            // delete the old file
            $toDelete = new File($this->getParameter('gamefiles_directory').'/'.$game->getGameFilename());
            unlink($toDelete);

            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('game_index');
    }
}
