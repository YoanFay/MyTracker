<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\SerieRepository;
use App\Repository\GenresRepository;
use App\Repository\TagsRepository;
use App\Repository\TagsTypeRepository;
use App\Entity\Genres;
use App\Entity\Tags;
use App\Entity\TagsType;


class UpdateGenreTagCommand extends Command
{

    private serieRepository $serieRepository;
    private genresRepository $genresRepository;
    private tagsRepository $tagsRepository;
    private tagsTypeRepository $tagsTypeRepository;


    protected function configure(): void
    {

        $this->setName('app:update-genre-tag');
    }


    public function __construct(SerieRepository $serieRepository, GenresRepository $genresRepository, TagsRepository $tagsRepository, TagsTypeRepository $tagsTypeRepository, ManagerRegistry $managerRegistry)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->genresRepository = $genresRepository;
        $this->tagsRepository = $tagsRepository;
        $this->tagsTypeRepository = $tagsTypeRepository;
        $this->manager = $managerRegistry->getManager();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $client = new Client();

        $apiUrl = 'https://api4.thetvdb.com/v4';

        $apiToken = '8f3a7d8f-c61f-4bf7-930d-65eeab4b26ad';

        $response = $client->post($apiUrl."/login", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => ['apiKey' => $apiToken],
        ]);

        $data = json_decode($response->getBody(), true);

        // Récupérez le token
        $token = $data['data']['token'];
        
        $series = $this->serieRepository->findNoGenre();
        
        foreach($series as $serie){
            
                $response = $client->get($apiUrl."/series/".$serie->getTvdbId()."/extended", [
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                ]);

                $data = json_decode($response->getBody(), true);
                
                if($data['status'] === "success"){
                    
                    if($data['data']['genres'] !== null){
                    foreach($data['data']['genres'] as $genre){
                        
                        $genreEntity = $this->genresRepository->findOneBy(['nameEng' => $genre['name']]);
                        
                        if(!$genreEntity){
                            $genreEntity = new Genres();
                            
                            $genreEntity->setNameEng($genre['name']);

                            $this->manager->persist($genreEntity);
                            $this->manager->flush();
                        }
                        
                        $serie->addGenre($genreEntity);
                        
                    }
                    }
                    
                    if($data['data']['tags'] !== null){
                    foreach($data['data']['tags'] as $tags){
                        
                        $tagEntity = $this->tagsRepository->findOneBy(['nameEng' => $tags['name']]);
                        
                        if(!$tagEntity){
                            
                            $tagTypeEntity = $this->tagsTypeRepository->findOneBy(['nameEng' => $tags['tagName']]);
                            
                            if(!$tagTypeEntity){
                                $tagTypeEntity = new TagsType();
                                
                                $tagTypeEntity->setNameEng($tags['tagName']);

                                $this->manager->persist($tagTypeEntity);
                                $this->manager->flush();
                            }
                            
                            $tagEntity = new Tags();
                            
                            $tagEntity->setNameEng($tags['name']);
                            $tagEntity->setTagsType($tagTypeEntity);

                            $this->manager->persist($tagEntity);
                            $this->manager->flush();
                        }
                        
                        $serie->addTag($tagEntity);
                        
                    }
                    }
                    
                    $this->manager->persist($serie);
                    $this->manager->flush();
                    
                }
        }

        return Command::SUCCESS;
    }
}
