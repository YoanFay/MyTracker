<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;
use App\Repository\SerieRepository;
use App\Repository\EpisodeShowRepository;
use App\Entity\Users;
use App\Entity\Serie;
use App\Entity\EpisodeShow;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class WebHook extends AbstractController
{

    /**
     * @Route("/webhook", name="webhook")
     */
    public function webhook(
        Request               $request,
        UsersRepository       $usersRepository,
        SerieRepository       $serieRepository,
        EpisodeShowRepository $episodeShowRepository
    ): Response
    {

        $em = $this->getDoctrine()->getManager();

        $jsonData = json_decode($_POST['payload'], true);

        //$jsonData = json_decode($data,true);

        $user = $usersRepository->findOneBy(['plexName' => $jsonData['Account']['title']]);

        if (!$user) {
            $user = new Users;

            $user->setPlexName($jsonData['Account']['title']);

            $em->persist($user);
            $em->flush();
        }

        $serieId = str_replace(["plex://show/"], [""], $jsonData['Metadata']['grandparentGuid']);

        $serie = $serieRepository->findOneBy(['plexId' => $serieId]);

        if (!$serie) {
            $serie = new Serie;

            $type = str_replace(['Quasinas ', ' A Deux', ' Chat', ' Doudou'], ['', '', '', ''], $jsonData['Metadata']['librarySectionTitle']);

            $serie->setPlexId($serieId);
            $serie->setName($jsonData['Metadata']['grandparentTitle']);
            $serie->setType($type);

            $em->persist($serie);
            $em->flush();
        }

        if ($jsonData['event'] === "media.scrobble") {

            $episodeId = null;
            $episode = null;

            if (isset($jsonData['Metadata']['guid'])) {
                $episodeId = str_replace(["plex://episode/", "local://"], ["", ""], $jsonData['Metadata']['guid']);

                $episode = $episodeShowRepository->findOneBy(['plexId' => $episodeId]);
            }

            if (!$episode) {

                $tvdbId = null;

                if (isset($jsonData['Metadata']['Guid'])) {
                    foreach ($jsonData['Metadata']['Guid'] as $guid) {
                        if (isset($guid['id']) && strpos($guid['id'], 'tvdb://') === 0) {
                            $tvdbId = str_replace(["tvdb://"], [""], $guid['id']);
                            break;
                        }
                    }
                }

                $episode = new EpisodeShow;

                $episode->setPlexId($episodeId);
                $episode->setName($jsonData['Metadata']['title']);
                $episode->setShowDate(new \DateTime());
                $episode->setSerie($serie);
                $episode->setUser($user);
                $episode->setTvdbId($tvdbId);
                $episode->setSaison($jsonData['Metadata']['parentTitle']);
                $episode->setSaisonNumber($jsonData['Metadata']['parentIndex']);
                $episode->setEpisodeNumber($jsonData['Metadata']['index']);
                $episode->setDuration(isset($jsonData['Metadata']['duration']) ?
                    $jsonData['Metadata']['duration'] :
                    null);

                $em->persist($episode);
                $em->flush();
            }

        }

        //if ($jsonData['X-Mailin-custom']) {
        //$tags = explode('/',$jsonData['event']);

        /*$company = $companyRepository->findOneBy(['id' => $tags[1], 'deletedAt' => null]);

        $order = $orderRepository->findOneBy(['id' => $tags[0], 'deletedAt' => null]);

        if($jsonData['date']){
            $date = new \DateTime($jsonData['date']);
        }
        else{
            $date = new \DateTime();
        }

        if ($tags[2] !== '_' && $tags[2] !== null) {
            $productOrder = $productOrderRepository->findOneBy(['id' => $tags[2], 'deletedAt' => null]);

            $orderLog = $orderLogRepository->findOneBy(['company' => $company, 'orderParent' => $order,'productOrder' => $productOrder]);
        }
        else{
            $orderLog = $orderLogRepository->findOneBy(['company' => $company, 'orderParent' => $order]);
        }

        if ($orderLog === null){
            $orderLog = new OrderLog();
            $orderLog->setCompany($company);
            $orderLog->setOrderParent($order);
            if ($tags[3] !== null) {
                $orderLog->setOrigin($tags[3]);
            }
            if (isset($productOrder)){
                $orderLog->setProductOrder($productOrder);
            }
        }

        switch ($jsonData['event']){
            case 'request':
                $orderLog->setDateSent($date);
                $em->persist($orderLog);
                break;

            case 'delivered':
                if ($tags[0] !== "") {
                    if ($orderLog->getDateSent() === NULL){
                        $orderLog->setDateSent($date);
                    }
                    $orderLog->setDateDelivered($date);
                    $em->persist($orderLog);
                }
                break;

            case 'unique_opened':
                if ($tags[0] !== ""){
                    $orderLog->setDateFirstOpened($date);
                    $em->persist($orderLog);
                }
                break;

            case 'opened':
                if ($tags[0] !== ""){
                $orderLog->setDateLastOpened($date);
                $em->persist($orderLog);
                }
                break;

            case 'click' :
                if ($tags[0] !== ""){
                $orderLog->setDateClick($date);
                $em->persist($orderLog);
                }
                break;

            default:
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/exports/sendinblue.txt', $content);
                return new Response('false');
        }

        $em->flush();

        return new Response('ok');
    } else {*/
        //file_put_contents('/home/vfmqnrmc/www/public/log/'.strtotime('now').'.txt', $content);
        return new Response('Pas de tags');
        //}
    }

}
