<?php

// src/Command/LikeAndFollowUsersCommand.php
namespace App\Command;

use InstagramAPI\Response\Model\FriendshipStatus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use App\Service\DBRequest;
use Psr\Log\LoggerInterface;

class LikeAndFollowUsersCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:likeAndFollowUsers';

    /**
     * @var DBRequest
     */
    private $db;
    
    /**
    * @var LoggerInterface
    */
    private $logger;
    
    /*public function __construct(DBrequest $dbRequest,LoggerInterface $logger){
        $this->logger = $logger;
        $this->db = $dbRequest;
        parent::__construct();
    }*/

    public function __construct(DBrequest $bdRequest,LoggerInterface $logger){
        $this->logger = $logger;
        $this->db = $bdRequest;
        parent::__construct();

    }

    protected function configure()
    {
        $this 
        ->setDescription('Like medias and follow Instagram users from the People table for an account')
        ->addArgument('username', InputArgument::REQUIRED, 'My username')
        ->addArgument('password', InputArgument::REQUIRED, 'My password')
    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /////// CONFIG ///////
        $debug          = false;
        $truncatedDebug = false;
        //////////////////////
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
        try {
            //Login Instagram account
            $output->writeln('test!');
            $ig->login($username, $password);
            //Get account instance
            $account=$this->db->findAccountByUsername($username);
            $output->writeln($account->getUsername());
            $output->writeln('account récupéré!');
            //DBRequest to get peopleToInteract
            $peopleToInteract = $this->db->getAllPeopleForAccount($account);
            $output->writeln('people récupérés!');
            $likeUserMediasCommand = $this->getApplication()->find('app:likeUserMedias');
            $followCommand = $this->getApplication()->find('app:follow'); 
            //LikeUserMedias and follow people
            foreach($peopleToInteract as $person) {
                //Like 2 pictures of person
                $output->writeln($person->getInstaID());
                $likeUserMediasArguments = [
                    'command' => 'app:likeUserMedias',
                    'username' => $username,
                    'password' => $password,
                    'userId' => $person->getInstaID(),
                ];
                $likeUserMediasInput = new ArrayInput($likeUserMediasArguments);
                //$likeUserMediasCommand->run($likeUserMediasInput, $output);
                //Follow person
                $followCommandArguments = [
                    'command' => 'app:follow',
                    'username' => $username,
                    'password' => $password,
                    'userId' => $person->getInstaID(),
                ];
                $followInput = new ArrayInput($followCommandArguments);
                //sleep between the likes and the follow commands
                sleep(rand(3,6));
                //$followCommand->run($followInput, $output); 
                //Update person in People table by
                //setting toFollow to 'false' and update Date in updated 
                //$this->db->getPeopleById($person->getinstaID(),$account)->setToFollow(false);
                //$this->db->getPeopleById($person->getinstaID(),$account)->setUpdated(new \DateTime);
                //$output->writeln("Person updated!");
                //Pause of 30 seconds between actual person and the next one
                //sleep(30);
            }
            //$output->writeln("People table updated!");
        }
        catch (\Exception $e) {
            throw new \Exception('Something went wrong: ' . $e->getMessage());
        }        
    }    
}