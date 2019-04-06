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
    
    public function __construct(DBrequest $dbRequest,LoggerInterface $logger){
        $this->logger = $logger;
        $this->db = $dbRequest;
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
            $ig->login($username, $password);
            $account=$this->db->findAccountByUsername($username);
            //$output->writeln($account->getUsername());
            $peopleToInteract = $this->db->getAllPeopleForAccount($account);
            $likeUserMediasCommand = $this->getApplication()->find('app:likeUserMedias');
            $followCommand = $this->getApplication()->find('app:follow'); 
            foreach($peopleToInteract as $person) {
                //$output->writeln($person->getUsername());
                $likeUserMediasArguments = [
                    'command' => 'app:likeUserMedias',
                    'username' => $username,
                    'password' => $password,
                    'userId' => $person->getInstaID(),
                ];
                $likeUserMediasInput = new ArrayInput($likeUserMediasArguments);
                $likeUserMediasCommand->run($likeUserMediasInput, $output);
                $followCommandArguments = [
                    'command' => 'app:follow',
                    'username' => $username,
                    'password' => $password,
                    'userId' => $person->getInstaID(),    
                ];
                $followInput = new ArrayInput($followCommandArguments);
                sleep(rand(3,6));
                $followCommand->run($followInput, $output); 
                $this->db->updatePersonByInstaID($person->getInstaID(),$account);
                //$output->writeln($person->getUsername().' followed correctly and updated in People table');
                sleep(30);
            }
            //$output->writeln('end');
        }
        catch (\Exception $e) {
            throw new \Exception('Something went wrong: ' . $e->getMessage());
        }        
    }    
}