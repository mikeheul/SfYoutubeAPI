<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class YoutubeService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getChannelIdByHandle(string $handle): ?string
    {
        $url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q=@'. $handle . '&type=channel&key='. $_ENV['YOUTUBE_API_KEY'];

        $response = $this->client->request('GET', $url);
        $data = $response->toArray();

        if (!empty($data['items'][0]['id']['channelId'])) {
            return $data['items'][0]['id']['channelId'];
        }

        return null;
    }

    public function getChannelStats(string $channelId): array
    {
        $url = 'https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics,contentDetails,brandingSettings&id=' . $channelId . '&key=' . $_ENV['YOUTUBE_API_KEY'];

        $response = $this->client->request('GET', $url);
        $data = $response->toArray();

        if (empty($data['items'])) {
            throw new \Exception("Impossible de récupérer les données de la chaîne.");
        }

        // return $data['items'][0];

        return [
            'id' => $data['items'][0]['id'],
            'title' => $data['items'][0]['snippet']['title'] ?? null,
            'description' => $data['items'][0]['snippet']['description'] ?? null,
            'publishedAt' => $data['items'][0]['snippet']['publishedAt'] ?? null,
            'thumbnail' => $data['items'][0]['snippet']['thumbnails']['high']['url'] ?? null,
            'banner' => $data['items'][0]['brandingSettings']['image']['bannerExternalUrl'] ?? null,
            'statistics' => [
                'subscribers' => $data['items'][0]['statistics']['subscriberCount'] ?? 0,
                'views' => $data['items'][0]['statistics']['viewCount'] ?? 0,
                'videos' => $data['items'][0]['statistics']['videoCount'] ?? 0,
            ],
            'uploads_playlist' => $data['items'][0]['contentDetails']['relatedPlaylists']['uploads'] ?? null
        ];
    }
}
