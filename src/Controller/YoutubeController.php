<?php

namespace App\Controller;

use App\Service\YoutubeService;
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
    public function getStats(string $channelId): Response
    {
        try {
            $stats = $this->youtubeService->getChannelStats($channelId);

            return $this->render('youtube/stats.html.twig', [
                'stats' => $stats
            ]);

            // return new JsonResponse($stats);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}