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
use Symfony\Component\Form\FormInterface;
use App\Entity\Users;
use App\Form\UserAdd;


class UsersController extends AbstractFOSRestController
{
    /**
     * @Route("/users", name="users")
     */

    public function getAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $filter = $request->query->get('filter');
        if (!empty($filter)) {
            $user = $repository->findBy(array('firstName' => $filter),array('firstName' => 'ASC'),5 ,0);
        }else{
            $user = $repository->findall();
        }
        return $this->render('users/index.html.twig', array('users' => $user));
    }

    /**
     * Create User.
     * @Route("/user" , name="register")
     *
     * @param Request $request
     * @return View|Response
     */
    public function postUsersAction(Request $request): Response
    {
        $user = new Users();
        $form = $this->createForm(UserAdd::class, $user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('users');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

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
}
