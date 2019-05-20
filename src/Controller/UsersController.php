<?php

namespace App\Controller;

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Entity\Users;
use App\Form\UserAdd;


class UsersController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/users", name="users")
     */

    public function getAction()
    {
        $repository = $this->getDoctrine()->getRepository(Users::class);

        // query for a single Product by its primary key (usually "id")
        $user = $repository->findall();
        //$request = $this->get('request');
        if(!empty($filter))
        {
            $finder = $this->container->get('fos_elastica.finder.user');

            $andOuter = new \Elastica\Filter\Bool();
            foreach($filter as $optionKey=>$arrValues)
            {

                $orOuter = new \Elastica\Filter\Bool();
                foreach($arrValues as $value)
                {

                    $andInner = new \Elastica\Filter\Bool();
                    $optionKeyTerm = new \Elastica\Filter\Term();
                    $optionKeyTerm->setTerm('productOptionValues.productOption', $optionKey);

                    $valueTerm = new \Elastica\Filter\Term();
                    $valueTerm->setTerm('productOptionValues.value', $value);
                    $andInner->addMust($optionKeyTerm);
                    $andInner->addMust($valueTerm);

                    $orOuter->addShould($andInner);
                }
                $andOuter->addMust($orOuter);
            }

            $filtered = new \Elastica\Query\Filtered();
            $filtered->setFilter($andOuter);
            $user = $finder->find($filtered);
        }
        else
        {
            //$filter = $request->query->get('filter');
        }
        //return $this->render('TestBundle:Default:filter.html.twig', array('users' => $user, 'filter' => $filter));
        return View::create($user, Response::HTTP_OK , []);
    }

    /**
     * Create User.
     * @Rest\get("/user" , name="register")
     *
     * @param Request $request
     * @return View|Response
     */
    public function postUsersAction(Request $request)
    {
        $user = new Users();
        $form = $this->createForm(UserAdd::class, $user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return View::create($user, Response::HTTP_CREATED , []);
        }

        return View::create($user, Response::HTTP_CREATED , []);

    }

    /**
     * Create User.
     * @Rest\Post("/json_add_user")
     *
     * @param Request $request
     * @return View|Response
     */
    public function postJsonUsersAction(Request $request)
    {
            $user = new Users();
            $user->setFirstName($request->get('firstName'));
            $user->setLastName($request->get('lastName'));
            $user->setActual($request->get('actual'));
            $phoneNumbers[] = $request->get('phoneNumbers');
            foreach ($phoneNumbers as $phoneNumber) {
                $user->setPhoneNumbers($phoneNumber);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return View::create($user, Response::HTTP_CREATED, []);
    }

    protected function verification(Request $request)
    {

        $message = ["Type"=>"VerificationEmail","Firstname"=>$request->get('firstName'),"Lastname"=>$request->get('lastName'),"Phonenumbers"=>$request->get('phoneNumbers')];
        $rabbitMessage = json_encode($message);

        $this->get('old_sound_rabbit_mq.emailing_producer')->setContentType('application/json');
        $this->get('old_sound_rabbit_mq.emailing_producer')->publish($rabbitMessage);

        return new JsonResponse(array('Status' => 'OK'));
    }


}
