<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Service\YoutubeService;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class YoutubeController extends AbstractController
{
    private YoutubeService $youtubeService;

    public function __construct(YoutubeService $youtubeService)
    {
        $this->youtubeService = $youtubeService;
    }

    #[Route('/youtube', name: 'app_youtube')]
    public function index(): Response
    {
        return $this->render('youtube/index.html.twig', [
            'controller_name' => 'YoutubeController',
        ]);
    }

    // #[Route('/youtube/id/{handle}', name: 'youtube_get_id')]
    // public function getChannelId(string $handle): JsonResponse
    // {
    //     $channelId = $this->youtubeService->getChannelIdByHandle($handle);

    //     if (!$channelId) {
    //         return new JsonResponse(['error' => 'Aucune chaîne trouvée pour ce handle.'], 404);
    //     }

    //     return new JsonResponse(['channelId' => $channelId]);
    // }

    #[Route('/youtube/favoris', name: 'show_favorites')]
    public function showFavorites(EntityManagerInterface $em, FavoriteRepository $fr): Response
    {
        // Récupérer les favoris de l'utilisateur
        $favorites = $fr->findBy([], ['createdAt' => 'DESC']);

        // Afficher la page avec les favoris
        return $this->render('youtube/favorites.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route('/youtube/{handle}', name: 'youtube_get_id')]
    public function getChannelId(string $handle): JsonResponse|RedirectResponse
    {
        // Récupérer l'ID de la chaîne à partir du handle
        $channelId = $this->youtubeService->getChannelIdByHandle($handle);

        if (!$channelId) {
            // Si l'ID de la chaîne n'est pas trouvé, renvoyer une erreur JSON
            return new JsonResponse(['error' => 'Aucune chaîne trouvée pour ce handle.'], 404);
        }

        // Si l'ID de la chaîne est trouvé, rediriger vers la route des statistiques avec l'ID de la chaîne
        return $this->redirectToRoute('youtube_stats', ['channelId' => $channelId]);
    }

    #[Route('/youtube/stats/{channelId}', name: 'youtube_stats')]
    // public function getStats(string $channelId): JsonResponse
    public function getStats(string $channelId, FavoriteRepository $fr): Response
    {
        try {
            $stats = $this->youtubeService->getChannelStats($channelId);
            $lastVideo = $this->youtubeService->getLastVideoByChannelId($channelId);
            $embedUrl = $lastVideo ? $this->youtubeService->getEmbedUrl($lastVideo['id']['videoId']) : null;

            $isFavorite = $fr->findOneBy(['channelId' => $channelId]) !== null;

            return $this->render('youtube/stats.html.twig', [
                'stats' => $stats,
                'isFavorite' => $isFavorite,
                'video' => $lastVideo,
                'embedUrl' => $embedUrl,
            ]);

            // return new JsonResponse($stats);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/youtube/favoris/{channelId}', name: 'add_to_favorites', methods: ['POST'])]
    public function addToFavorites(EntityManagerInterface $em, Request $request, string $channelId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $customUrl = $data['customUrl'] ?? null;
        $customUrlWithoutAt = ltrim($customUrl, '@');

        if (!$customUrl) {
            return new JsonResponse(['error' => 'Le customUrl est requis'], 400);
        }

        // Ajouter les données aux favoris dans la base de données
        $this->addFavoriteToDatabase($em, $channelId, $customUrlWithoutAt);

        return new JsonResponse(['message' => 'Chaîne ajoutée aux favoris'], 200);
    }

    private function addFavoriteToDatabase(EntityManagerInterface $em, string $channelId, string $customUrl)
    {
        $favorite = new Favorite();
        $favorite->setChannelId($channelId);
        $favorite->setCustomUrl($customUrl);

        $em->persist($favorite);
        $em->flush();
    }
}