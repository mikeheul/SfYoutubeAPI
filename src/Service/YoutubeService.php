<?php

namespace App\Service;

use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class YoutubeService
{
    private HttpClientInterface $client;
    private CacheInterface $cache; 

    public function __construct(HttpClientInterface $client, CacheInterface $cache)
    {
        $this->client = $client;
        $this->cache = $cache;
    }

    // public function getChannelIdByHandle(string $handle): ?string
    // {
    //     $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=@'. $handle . '&type=channel&key='. $_ENV['YOUTUBE_API_KEY'];

    //     $response = $this->client->request('GET', $url);
    //     $data = $response->toArray();

    //     if (!empty($data['items'][0]['id']['channelId'])) {
    //         return $data['items'][0]['id']['channelId'];
    //     }

    //     return null;
    // }

    public function getChannelIdByHandle(string $handle): ?string
    {
        return $this->cache->get('youtube_channel_id_' . $handle, function (ItemInterface $item) use ($handle) {
            $item->expiresAfter(3600); // Cache pendant 1 heure (3600 secondes)

            $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=@' . $handle . '&type=channel&key=' . $_ENV['YOUTUBE_API_KEY'];

            $response = $this->client->request('GET', $url);
            $data = $response->toArray();

            return $data['items'][0]['id']['channelId'] ?? null;
        });
    }

    public function getLastVideoByChannelId(string $channelId): ?array
    {
        $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=' . $channelId . '&order=date&maxResults=1&key=' . $_ENV['YOUTUBE_API_KEY'];

        $response = $this->client->request('GET', $url);
        $data = $response->toArray();

        if (!empty($data['items'])) {
            // La dernière vidéo est dans le premier élément du tableau
            return $data['items'][0];
        }

        return null;
    }

    public function getLastVideoEmbed(string $channelId): ?array
    {
        $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=' . $channelId . '&order=date&maxResults=1&key=' . $_ENV['YOUTUBE_API_KEY'];

        $response = $this->client->request('GET', $url);
        $data = $response->toArray();

        if (!empty($data['items'])) {
            return $data['items'][0];
        }

        return null;
    }

    public function getEmbedUrl(string $videoId): string
    {
        return 'https://www.youtube.com/embed/' . $videoId;
    }

    // public function getChannelStats(string $channelId): array
    // {
    //     $url = 'https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics,contentDetails,brandingSettings&id=' . $channelId . '&key=' . $_ENV['YOUTUBE_API_KEY'];

    //     $response = $this->client->request('GET', $url);
    //     $data = $response->toArray();

    //     if (empty($data['items'])) {
    //         throw new \Exception("Impossible de récupérer les données de la chaîne.");
    //     }

    //     return [
    //         'id' => $data['items'][0]['id'],
    //         'title' => $data['items'][0]['snippet']['title'] ?? null,
    //         'customUrl' => $data['items'][0]['snippet']['customUrl'] ?? null,
    //         'description' => $data['items'][0]['snippet']['description'] ?? null,
    //         'publishedAt' => $data['items'][0]['snippet']['publishedAt'] ?? null,
    //         'thumbnail' => $data['items'][0]['snippet']['thumbnails']['high']['url'] ?? null,
    //         'banner' => $data['items'][0]['brandingSettings']['image']['bannerExternalUrl'] ?? null,
    //         'statistics' => [
    //             'subscribers' => $data['items'][0]['statistics']['subscriberCount'] ?? 0,
    //             'views' => $data['items'][0]['statistics']['viewCount'] ?? 0,
    //             'videos' => $data['items'][0]['statistics']['videoCount'] ?? 0,
    //         ],
    //         'uploads_playlist' => $data['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ?? null
    //     ];
    // }

    public function getChannelStats(string $channelId = null): array
    {
        // return $this->cache->get('youtube_channel_stats_' . $channelId, function (ItemInterface $item) use ($channelId) {
        //     $item->expiresAfter(3600); // Cache pendant 1 heure

            // $url = 'https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics,contentDetails,brandingSettings&id=' . $channelId . '&key=' . $_ENV['YOUTUBE_API_KEY'];

            try {
                // $response = $this->client->request('GET', $url);
                // $data = $response->toArray();

                // if (empty($data['items'])) {
                //     throw new \Exception("Aucune donnée trouvée.");
                // }

                // return [
                //     'id' => $data['items'][0]['id'],
                //     'title' => $data['items'][0]['snippet']['title'] ?? "Chaîne YouTube Test",
                //     'customUrl' => ltrim($data['items'][0]['snippet']['customUrl'] ?? "testchannel", '@'),
                //     'description' => $data['items'][0]['snippet']['description'] ?? "Description factice",
                //     'publishedAt' => $data['items'][0]['snippet']['publishedAt'] ?? "2020-01-01T00:00:00Z",
                //     'thumbnail' => $data['items'][0]['snippet']['thumbnails']['high']['url'] ?? "https://via.placeholder.com/150",
                //     'banner' => $data['items'][0]['brandingSettings']['image']['bannerExternalUrl'] ?? "https://via.placeholder.com/1024x300",
                //     'statistics' => [
                //         'subscribers' => $data['items'][0]['statistics']['subscriberCount'] ?? rand(1000, 50000),
                //         'views' => $data['items'][0]['statistics']['viewCount'] ?? rand(100000, 5000000),
                //         'videos' => $data['items'][0]['statistics']['videoCount'] ?? rand(10, 500),
                //     ],
                //     'uploads_playlist' => $data['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ?? null
                // ];

                return [
                    'id' => $channelId,
                    'title' => "Chaîne YouTube Test",
                    'customUrl' => "testchannel",
                    'description' => "Description factice - L'API YouTube est hors service ou le quota est dépassé.",
                    'publishedAt' => "2020-01-01T00:00:00Z",
                    'thumbnail' => "https://placehold.co/150",
                    'banner' => "https://placehold.co/1024x500",
                    'statistics' => [
                        'subscribers' => rand(1000, 50000),
                        'views' => rand(100000, 5000000),
                        'videos' => rand(10, 500),
                    ],
                    'uploads_playlist' => null
                ];
            } catch (\Exception $e) {
                // Données factices en cas d'échec
                return [
                    'id' => $channelId,
                    'title' => "Chaîne YouTube Test",
                    'customUrl' => "testchannel",
                    'description' => "Description factice - L'API YouTube est hors service ou le quota est dépassé.",
                    'publishedAt' => "2020-01-01T00:00:00Z",
                    'thumbnail' => "https://placehold.co/150",
                    'banner' => "https://placehold.co/1024x500",
                    'statistics' => [
                        'subscribers' => rand(1000, 50000),
                        'views' => rand(100000, 5000000),
                        'videos' => rand(10, 500),
                    ],
                    'uploads_playlist' => null
                ];
            }
        // });
    }
}
