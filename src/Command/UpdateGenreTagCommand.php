<?php

namespace App\Command;

use App\Service\TVDBService;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
    private TVDBService $TVDBService;
    private ObjectManager $manager;


    protected function configure(): void
    {

        $this->setName('app:update-genre-tag');
        $this->setDescription('Pour les  tags des séries');
    }


    public function __construct(SerieRepository $serieRepository, GenresRepository $genresRepository, TagsRepository $tagsRepository, TagsTypeRepository $tagsTypeRepository, ManagerRegistry $managerRegistry, TVDBService $TVDBService)
    {

        parent::__construct();
        $this->serieRepository = $serieRepository;
        $this->genresRepository = $genresRepository;
        $this->tagsRepository = $tagsRepository;
        $this->tagsTypeRepository = $tagsTypeRepository;
        $this->manager = $managerRegistry->getManager();
        $this->TVDBService = $TVDBService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $series = $this->serieRepository->findNoGenre();
        
        foreach($series as $serie){

                $data = $this->TVDBService->getData("/series/".$serie->getTvdbId()."/extended");
                
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
