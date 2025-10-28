<?php

namespace App\Controller;

use App\Form\MusicType;
use App\Repository\MusicArtistRepository;
use App\Repository\MusicListenRepository;
use App\Repository\MusicRepository;
use App\Repository\MusicTagsRepository;
use App\Service\CoverArchiveService;
use App\Service\MBService;
use App\Service\TimeService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

#[Route('/music')]
class MusicController extends AbstractController
{
    #[Route('/', name: 'music')]
    public function index(MusicListenRepository $musicListenRepository): Response
    {

        return $this->render('music\index.html.twig', [
            'navLinkId' => 'music',
        ]);

    }


    #[Route('/list', name: 'music_list')]
    public function list(MusicRepository $musicRepository, Request $request): Response
    {

        $text = $request->request->get('text');

        $musics = $musicRepository->findByName($text);

        return $this->render('music\list.html.twig', [
            'musics' => $musics,
            'navLinkId' => 'music',
        ]);
    }


    #[Route('/tags', name: 'music_tags')]
    public function tags(MusicListenRepository $musicListenRepository): Response
    {

        $tags = $musicListenRepository->getListenByTags();

        $listTags = [
            'saison' => [],
            'type' => [],
            'annee' => [],
            'animeTag' => [],
            'origin' => [],
        ];

        foreach ($tags as $key => $tag) {

            if (is_numeric($tag['NAME'])) {
                $listTags['annee'][] = $tag;
                unset($tags[$key]);
            } else if (in_array($tag['NAME'], ['Printemps', 'Été', 'Automne', 'Hiver'])) {
                $listTags['saison'][] = $tag;
                unset($tags[$key]);
            } else if (in_array($tag['NAME'], ['Anime', 'Film', 'Série', 'Jeux'])) {
                $listTags['type'][] = $tag;
                unset($tags[$key]);
            } else if (in_array($tag['NAME'], ['Opening', 'Insert', 'Ending', 'OST'])) {
                $listTags['animeTag'][] = $tag;
                unset($tags[$key]);
            } else {
                $listTags['origin'][] = $tag;
                unset($tags[$key]);
            }

        }

        return $this->render('music\musicTags.html.twig', [
            'navLinkId' => 'music',
            'listTags' => $listTags
        ]);
    }


    /**
     * @throws NonUniqueResultException
     */
    #[Route('/tags/details/{id}', name: 'music_tags_details')]
    public function tagDetails(MusicTagsRepository $musicTagsRepository, MusicListenRepository $musicListenRepository, $id): Response
    {

        $tag = $musicTagsRepository->find($id);

        $listen = $musicListenRepository->getListenByOneTag($tag)['LISTEN'];

        return $this->render('music\musicTagDetails.html.twig', [
            'navLinkId' => 'music',
            'tag' => $tag,
            'listen' => $listen
        ]);
    }


    #[Route('/artist', name: 'music_artists')]
    public function artists(MusicListenRepository $musicListenRepository): Response
    {

        $artists = $musicListenRepository->getListenByArtist();

        return $this->render('music\musicArtists.html.twig', [
            'navLinkId' => 'music',
            'artists' => $artists
        ]);
    }


    /**
     * @throws NonUniqueResultException
     */
    #[Route('/artist/details/{id}', name: 'music_artists_details')]
    public function artistDetails(MusicArtistRepository $musicArtistRepository, MusicListenRepository $musicListenRepository, $id): Response
    {

        $artist = $musicArtistRepository->find($id);

        $listen = $musicListenRepository->getListenByOneArtist($artist)['LISTEN'];

        return $this->render('music\musicArtistDetails.html.twig', [
            'navLinkId' => 'music',
            'artist' => $artist,
            'listen' => $listen
        ]);
    }


    #[Route('/search', name: 'music_search')]
    public function search(MusicRepository $musicRepository): Response
    {

        $musicList = $musicRepository->findBy(['duration' => null], ['name' => 'ASC']);

        return $this->render('music\search.html.twig', [
            'navLinkId' => 'music',
            'musicList' => $musicList
        ]);
    }


    #[Route('/search/mbid', name: 'music_search_mbid')]
    public function searchMbid(Request $request, MusicRepository $musicRepository, MBService $MBService, TimeService $timeService): ?JsonResponse
    {

        $id = $request->request->get('id');

        $music = $musicRepository->find($id);

        $dataRelease = $MBService->searchRelease($music->getMusicArtist()->getName(), $music->getName());

        if ($dataRelease['count'] > 0) {

            $dataRelease = $MBService->searchRelease($music->getMusicArtist()->getName(), $music->getName())['releases'][0];

            $dataRecording = $MBService->searchRecording($dataRelease['id']);

            if ($dataRecording['count'] > 0 && isset($dataRecording['recordings'][0]['length'])) {
                $dataRecording = $dataRecording['recordings'][0];
            } else {

                return new JsonResponse([
                    'result' => null
                ]);

            }

            return new JsonResponse([
                'result' => $dataRelease['artist-credit'][0]['name']." - ".$dataRelease['title']." -> ".$timeService->convertirMillisecondeToMinuteSeconde($dataRecording['length'])." (".$dataRelease['id'].")",
                'mbid' => $dataRelease['id'],
            ]);

        }

        return new JsonResponse([
            'result' => null
        ]);

    }


    #[Route('/search/mbid/register', name: 'music_search_mbid_register')]
    public function registerMbid(ManagerRegistry $managerRegistry, Request $request, MusicRepository $musicRepository, MBService $MBService,): Response
    {

        $id = $request->request->get('id');
        $mbid = $request->request->get('mbid');

        $music = $musicRepository->find($id);

        try {

            $music->setMbid($mbid);
            $music->setDuration($MBService->searchRecording($mbid)['recordings'][0]['length']);

            $managerRegistry->getManager()->persist($music);
            $managerRegistry->getManager()->flush();

            return new Response(true);
        } catch (Exception) {
            return new Response(false);
        }

    }


    #[Route('/search/mbid/register/manually', name: 'music_search_mbid_register_manually')]
    public function registerManually(ManagerRegistry $managerRegistry, Request $request, MusicRepository $musicRepository, TimeService $timeService,): Response
    {

        $id = $request->request->get('id');
        $time = $request->request->get('time');

        $music = $musicRepository->find($id);

        try {

            $music->setDuration($timeService->convertirMinuteSecondeToMilliseconde($time));

            $managerRegistry->getManager()->persist($music);
            $managerRegistry->getManager()->flush();

            return new Response(true);
        } catch (Exception) {
            return new Response(false);
        }

    }


    #[Route('/detail/{id}', name: 'music_details')]
    public function detail(MusicRepository $musicRepository, int $id): Response
    {

        $music = $musicRepository->findOneBy(['id' => $id]);

        if (!$music) {

            $this->addFlash('error', 'music non trouvé');

            return $this->redirectToRoute('music');

        }

        $totalListen = count($music->getMusicListens()->getValues()) * $music->getDuration();

        $musicTags = $music->getMusicTags();

        return $this->render('music/details.html.twig', [
            'controller_name' => 'MusicController',
            'music' => $music,
            'musicTags' => $musicTags,
            'totalListen' => $totalListen,
            'navLinkId' => 'music',
        ]);
    }


    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    #[Route('/edit/{id}', name: 'music_edit')]
    public function edit(ManagerRegistry $managerRegistry, CoverArchiveService $coverArchiveService, MusicRepository $musicRepository, Request $request, int $id): Response
    {

        $music = $musicRepository->findOneBy(['id' => $id]);

        if (!$music) {

            $this->addFlash('error', 'Musique non trouvée');

            return $this->redirectToRoute('music');

        }

        $form = $this->createForm(MusicType::class, $music);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $check = $coverArchiveService->updateArtwork($music);

            if (!$check) {
                $music->setMbid(null);
            }

            $managerRegistry->getManager()->persist($music);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('music_details', ['id' => $id]);
        }

        return $this->render('music/edit.html.twig', [
            'controller_name' => 'MusicController',
            'form' => $form->createView(),
            'music' => $music,
            'navLinkId' => 'music_edit',
        ]);
    }


    #[Route('/history', name: 'music_history')]
    public function history(MusicListenRepository $musicListenRepository): Response
    {

        $listMonth = [
            '01' => 'janvier',
            '02' => 'fevrier',
            '03' => 'mars',
            '04' => 'avril',
            '05' => 'mai',
            '06' => 'juin',
            '07' => 'juillet',
            '08' => 'aout',
            '09' => 'septembre',
            '10' => 'octobre',
            '11' => 'novembre',
            '12' => 'decembre'
        ];

        $dates = $musicListenRepository->findMonth();

        /** @var array<string, mixed> $listDate */
        $listDate = [];

        foreach ($dates as $date) {

            $explode = explode('-', $date['DATE']);

            $year = $explode[0];
            $month = $listMonth[$explode[1]];
            $idMonth = $explode[1];

            if (!array_key_exists($year, $listDate)) {

                $listDate[$year] = [
                    'janvier' => [],
                    'fevrier' => [],
                    'mars' => [],
                    'avril' => [],
                    'mai' => [],
                    'juin' => [],
                    'juillet' => [],
                    'aout' => [],
                    'septembre' => [],
                    'octobre' => [],
                    'novembre' => [],
                    'decembre' => [],
                    'total' => 0,
                ];
            }

            if ($listDate[$year][$month] === []) {
                $listDate[$year][$month] = [
                    'duration' => 0,
                    'id' => $idMonth,
                ];
            }

            $listDate[$year][$month]['duration'] += $date['DURATION'];

            $listDate[$year]['total'] += $date['DURATION'];

        }

        krsort($listDate);

        return $this->render('music\history.html.twig', [
            'list' => $listDate,
            'navLinkId' => 'music',
        ]);

    }


    /**
     * @throws Exception
     */
    #[Route('/history/{year}/{month}', name: 'music_date')]
    public function historyDate(MusicListenRepository $musicListenRepository, string $year = '0', string $month = '0'): Response
    {

        $globalDuration = 0;

        $dataByDate = [];

        $musicsListen = $musicListenRepository->findByDate($year, $month);

        foreach ($musicsListen as $musicListen) {

            $dateKey = $musicListen->getListenAt()->format("Y-m-d");
            $music = $musicListen->getMusic();
            $duration = $music->getDuration();

            if (!array_key_exists($dateKey, $dataByDate)) {
                $dataByDate[$dateKey] = [
                    'totalDuration' => 0,
                    'history' => []
                ];
            }

            $type = null;

            $globalDuration += $duration;
            $dataByDate[$dateKey]['totalDuration'] += $duration;

            $name = $music->getName();

            $dataByDate[$dateKey]['history'][] = [
                'id' => $music->getId(),
                'name' => $name,
                'show' => $musicListen->getListenAt(),
                'duration' => $duration,
                'type' => $type,
            ];
        }

        $startDate = new DateTime($year.'-'.$month.'-01');

        $endDate = new DateTime('now');

        if (!($month === $endDate->format('m') && $year === $endDate->format('Y'))) {
            $endDate->setDate(intval($year), intval($month), 1);
            $endDate = $endDate->modify('last day of this month');
            $endDate->setTime(23, 59, 59, 999999);
        }
        $daysSinceStartOfYear = $endDate->diff($startDate)->days + 1;

        $globalDuration = $globalDuration / $daysSinceStartOfYear;

        krsort($dataByDate);

        $listMonth = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre'
        ];

        return $this->render('music/musicDate.html.twig', [
            'year' => $year,
            'month' => $listMonth[$month] ?? null,
            'dataByDate' => $dataByDate,
            'globalDuration' => $globalDuration,
            'daysSinceStartOfYear' => $daysSinceStartOfYear,
            'navLinkId' => 'episode',
        ]);
    }
}
                                    