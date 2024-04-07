<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\SerieType;
use App\Repository\MovieRepository;
use App\Repository\SerieTypeRepository;
use App\Service\StrSpecialCharsLower;
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
		EpisodeShowRepository $episodeShowRepository,
		MovieRepository       $movieRepository,
		StrSpecialCharsLower  $strSpecialCharsLower,
        SerieTypeRepository $serieTypeRepository
		): Response
		{
			
			$em = $this->getDoctrine()->getManager();
			$payload = $_POST['payload'];
			//$payload = '{"event":"media.pause","user":true,"owner":true,"Account":{"id":94267393,"thumb":"https://plex.tv/users/7bde893376eeecef/avatar?c=1710702399","title":"yoan.f8"},"Server":{"title":"PC-PORTABLE","uuid":"4b87b0f5ee15c68369e4257697e658810bbfe062"},"Player":{"local":true,"publicAddress":"88.160.190.207","title":"Chrome","uuid":"xjrphbgzgos8pmgfs3byr94q"},"Metadata":{"librarySectionType":"show","ratingKey":"32261","key":"/library/metadata/32261","parentRatingKey":"32260","grandparentRatingKey":"4506","guid":"plex://episode/5d9c0d88e264b7001fc713cc","parentGuid":"plex://season/602e5d210f4bde002da26f46","grandparentGuid":"plex://show/5d9c080def619b002047c6f0","grandparentSlug":"overlord","type":"episode","title":"La mélancolie du souverain","titleSort":"melancolie du souverain","grandparentKey":"/library/metadata/4506","parentKey":"/library/metadata/32260","librarySectionTitle":"Quasinas Anime","librarySectionID":1,"librarySectionKey":"/library/sections/1","grandparentTitle":"Overlord","parentTitle":"Season 3","originalTitle":"支配者の憂鬱","contentRating":"TV-MA","summary":"Nazarick prospère. Voulant s\'assurer que ses subordonnés loyaux profitent de leurs congés, Ainz leur donne l\'ordre de se reposer et obtient des réactions variées.","index":1,"parentIndex":3,"audienceRating":9.0,"viewOffset":115000,"lastViewedAt":1710752919,"year":2018,"thumb":"/library/metadata/32261/thumb/1709314357","art":"/library/metadata/4506/art/1710560317","parentThumb":"/library/metadata/32260/thumb/1709314357","grandparentThumb":"/library/metadata/4506/thumb/1710560317","grandparentArt":"/library/metadata/4506/art/1710560317","grandparentTheme":"/library/metadata/4506/theme/1710560317","duration":1440000,"originallyAvailableAt":"2018-07-10","addedAt":1709314347,"updatedAt":1709314357,"audienceRatingImage":"themoviedb://image.rating","chapterSource":"media","Guid":[{"id":"imdb://tt8237188"},{"id":"tmdb://1515779"},{"id":"tvdb://6632216"}],"Rating":[{"image":"themoviedb://image.rating","value":9.0,"type":"audience"}],"Director":[{"id":112000,"filter":"director=112000","tag":"Naoyuki Itou","tagKey":"6542629a4e70d2f46a9d2741"},{"id":112012,"filter":"director=112012","tag":"Tatsuya Shiraishi","tagKey":"6141b953cd0d20b8e9b92bc5"}],"Writer":[{"id":111988,"filter":"writer=111988","tag":"Yukie Sugawara","tagKey":"5f402152fea1a1003f9ecf65"}],"Role":[{"id":7501,"filter":"actor=7501","tag":"Satoshi Hino","tagKey":"5d7768406f4521001ea9e3fb","role":"Momonga / Ains Ooal Gown (Voice)","thumb":"https://metadata-static.plex.tv/people/5d7768406f4521001ea9e3fb.jpg"},{"id":8490,"filter":"actor=8490","tag":"Yumi Hara","tagKey":"5d77687f7e5fa10020bf09a5","role":"Albedo (Voice)","thumb":"https://metadata-static.plex.tv/a/people/a328e5495091d5a92379d47ced130adf.jpg"},{"id":64,"filter":"actor=64","tag":"Sumire Uesaka","tagKey":"5d776a487a53e9001e7002fe","role":"Shalltear Bloodfallen (Voice)","thumb":"https://metadata-static.plex.tv/9/people/953922e232939b69ba9713554b8e3d49.jpg"},{"id":17618,"filter":"actor=17618","tag":"Manami Numakura","tagKey":"5d776aa77a53e9001e70b782","role":"Narberal Gamma (Voice)","thumb":"https://metadata-static.plex.tv/0/people/008155f228658967d105c81c923fb3a6.jpg"},{"id":682,"filter":"actor=682","tag":"Akeno Watanabe","tagKey":"5d776880fb0d55001f512527","role":"Virtuous King of the Forest (Voice)","thumb":"https://metadata-static.plex.tv/3/people/3d0e471a9e9526a882c7f94182616bd3.jpg"},{"id":515,"filter":"actor=515","tag":"Aoi Yuki","tagKey":"5d7769c323d5a3001f4fc133","role":"Clementine (Voice)","thumb":"https://metadata-static.plex.tv/3/people/39f3a61bf345dc0180bc8aaa14d1ec54.jpg"},{"id":80,"filter":"actor=80","tag":"Ayumu Murase","tagKey":"5d77708f6afb3d002061cf0c","role":"Nfirea Bareare (Voice)","thumb":"https://metadata-static.plex.tv/d/people/dfad931b82c47cebd39fce382f885f12.jpg"},{"id":654,"filter":"actor=654","tag":"Ayane Sakura","tagKey":"5d7768f096b655001fdc5997","role":"Solution Epsilon (Voice)","thumb":"https://metadata-static.plex.tv/a/people/ab46b5cd5813980cff08146f668e0773.jpg"},{"id":12768,"filter":"actor=12768","tag":"Emiri Kato","tagKey":"5d7769c323d5a3001f4fc134","role":"Aura Bella Fiora (voice)","thumb":"https://metadata-static.plex.tv/6/people/6aee3af4e728ff69adf3fb111e0fa1bd.jpg"},{"id":170,"filter":"actor=170","tag":"Yumi Uchiyama","tagKey":"5d776ada594b2b001e6c7d9a","role":"Mare Bello Fiore (voice)","thumb":"https://metadata-static.plex.tv/f/people/fce13e69ce7ad79076b44ecef589193e.jpg"},{"id":255,"filter":"actor=255","tag":"Masayuki Katou","tagKey":"5d776a4296b655001fde8a52","role":"Demiurge (voice)","thumb":"https://metadata-static.plex.tv/4/people/49949695e51ecad151915b672bb6dbd4.jpg"},{"id":653,"filter":"actor=653","tag":"Kenta Miyake","tagKey":"5d776888ad5437001f748fc2","role":"Cocytus (voice)","thumb":"https://metadata-static.plex.tv/1/people/13a3a724719f71bc20a28fcfeea8e93b.jpg"},{"id":12823,"filter":"actor=12823","tag":"Hiromi Igarashi","tagKey":"5d7768999ab54400214e8579","role":"Yuri Alpha (voice)","thumb":"https://metadata-static.plex.tv/8/people/89accc57aef7a931200f16a67143ff30.jpg"},{"id":38,"filter":"actor=38","tag":"Mikako Komatsu","tagKey":"5d7769fc51dd69001fe1e8a8","role":"Lupusregina Beta (voice)","thumb":"https://metadata-static.plex.tv/1/people/10c73ceec31766095958b659ba4e2531.jpg"},{"id":1397,"filter":"actor=1397","tag":"Asami Seto","tagKey":"5d776a06f617c90020167e8a","role":"CZ2128 Delta (voice)","thumb":"https://metadata-static.plex.tv/2/people/2636dd983fc6828abd94c50aca1dda7b.jpg"},{"id":663,"filter":"actor=663","tag":"Kei Shindo","tagKey":"5d7768688718ba001e31b297","role":"Entoma Vasilissa Zeta (voice)","thumb":"https://metadata-static.plex.tv/people/5d7768688718ba001e31b297.jpg"}]}}';
			
			$jsonData = json_decode($payload, true);
			
			if(stripos($jsonData['event'], 'media') !== FALSE){
				//$jsonData = json_decode($data,true);
				
				$user = $usersRepository->findOneBy(['plexName' => $jsonData['Account']['title']]);
				
				if (!$user) {
					/*$user = new Users;
					
					$user->setPlexName($jsonData['Account']['title']);
					
					$em->persist($user);
					$em->flush();*/
					
					return new Response('FALSE');
					
				}

				$type = str_replace(['Quasinas ', ' A Deux', ' Chat', ' Doudou'], ['', '', '', ''], $jsonData['Metadata']['librarySectionTitle']);
				
				if ($type === "Films") {
					if ($jsonData['event'] === "media.scrobble") {
						
						$movieId = str_replace(["plex://movie/"], [""], $jsonData['Metadata']['guid']);
						
						$movie = $movieRepository->findOneBy(['plexId' => $movieId, 'user' => $user]);
						
						if (!$movie) {
							
							$tvdbMovieId = null;
							
							if (isset($jsonData['Metadata']['Guid'])) {
								foreach ($jsonData['Metadata']['Guid'] as $guid) {
									if (isset($guid['id']) && strpos($guid['id'], 'tmdb://') === 0) {
										$tvdbMovieId = str_replace(["tmdb://"], [""], $guid['id']);
										break;
									}
								}
							}
							
							$movie = new Movie();
							
							$movie->setPlexId($movieId);
							$movie->setUser($user);
							$movie->setName($jsonData['Metadata']['title']);
							$movie->setShowDate(new \DateTime());
							$movie->setTmdbId($tvdbMovieId);
							$movie->setSlug($strSpecialCharsLower->serie($movie->getName()));
							$movie->setDuration(isset($jsonData['Metadata']['duration']) ?
							$jsonData['Metadata']['duration'] :
							null);
							
							$em->persist($movie);
							$em->flush();
						}
					}
					
				} else {
					
					$serieId = str_replace(["plex://show/"], [""], $jsonData['Metadata']['grandparentGuid']);
					
					$serie = $serieRepository->findOneBy(['plexId' => $serieId]);

                    $serieType = $serieTypeRepository->findOneBy(['name' => $type]);

                    if(!$serieType){
                        $serieType = new SerieType();

                        $serieType->setName($type);

                        $em->persist($serieType);
                        $em->flush();
                    }
					
					if (!$serie) {
						$serie = new Serie;
						
						$serie->setPlexId($serieId);
						$serie->setName($jsonData['Metadata']['grandparentTitle']);
						$serie->setSerieType($serieType);
						
						$serie->setSlug($strSpecialCharsLower->serie($serie->getName()));
						
						$em->persist($serie);
						$em->flush();
					}
					
					if ($jsonData['event'] === "media.scrobble") {
						
						$episodeId = null;
						$episode = null;
						
						if (isset($jsonData['Metadata']['guid'])) {
							$episodeId = str_replace(["plex://episode/", "local://"], ["", ""], $jsonData['Metadata']['guid']);
							
							$episode = $episodeShowRepository->findOneBy(['plexId' => $episodeId, 'user' => $user]);
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
				}
			}
			
			return new Response('OK');
		}
		
	}
	