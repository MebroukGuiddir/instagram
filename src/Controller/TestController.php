<?php

namespace App\Controller;

use App\Entity\Account;
use App\Repository\AccountRepository;
use App\Entity\IgAccount;
use App\Entity\Task;
use App\Entity\User;
use App\Service\DBRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

class TestController extends AbstractController
{

    /**
     * @Route("/instagui/scheduling", name="inst_scheduling")
     */
    public function schedulingPage(DBRequest $DBRequest, LoggerInterface $logger)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $slots = $DBRequest->getSlots($this->getUser(), $logger);
        $status = $DBRequest->getStatus($this->getUser());
        return $this->render('instagui/scheduling.html.twig', ['page' => 'scheduling', 'slots' => $slots, 'status' => $status]);
    }

    public function sign(Request $request)
    {
        $task = new Task();
        $task->setTask('Form for instagram');
        $form = $this->createFormBuilder($task)
            ->add('task', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();
            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($task);
            // $entityManager->flush();
            return $this->redirectToRoute('task_success');
        }
        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/instagui/testIgAccount", name="test_ig_account", methods={"POST"},condition="request.isXmlHttpRequest()")
     */
    public function testIgAccount(Request $req)
    {
        $username = $req->request->get('username');
        $password = $req->request->get('password');
        try {
            $process = new Process('php bin/console insta:instance ' . $username . ' ' . $password);
            $process->setWorkingDirectory(getcwd());
            $process->setWorkingDirectory("../");
            $process->start();
            $process->wait();
            if ($process->isSuccessful()) {
                return new JsonResponse(["output" => "Successfully connected to " . $username], 200);
            } else {
                return new JsonResponse(["output" => "Please check password/username"], 400);
            }
        } catch (\Exception $e) {
            return new JsonResponse(["output" => "Error processing"], 403);
        }
    }

    /**
     * @Route("/findAccount")
     */
    public function testAccountTableDB(DBRequest $service,LoggerInterface $logger){
        $logger->info('Starting insert to DB');
        $account = $this->getDoctrine()
            ->getRepository(Account::class)
            ->selectAccount($this->getUser(),'testAccount','testPassword');
        $service->assignInstagramAccount($this->getUser(),$account,'testAccount','testPassword');
        $logger->info('went well');
        return new Response('test');
    }
}