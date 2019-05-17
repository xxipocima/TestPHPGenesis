<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UsersController.php',
        ]);
    }

    /**
     * Create User.
     * @Rest\Post("/user")
     *
     * @return array
     */
    public function postUsersAction(Request $request)
    {
        $user = new Users();
        $user->setFirstName($request->get('firstName'));
        $user->setLastName($request->get('lastName'));
        $phoneNumbers[] = $request->get('phoneNumbers');
        foreach ($phoneNumbers as $phoneNumber) {
            $user->setPhoneNumbers($phoneNumber);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return View::create($user, Response::HTTP_CREATED , []);

    }

    /**
     * Lists all Users.
     * @Rest\Get("/ListUsers")
     *
     * @return array
     */
    public function getUsersAction()
    {
        $repository = $this->getDoctrine()->getRepository(Users::class);

        // query for a single Product by its primary key (usually "id")
        $user = $repository->findall();

        return View::create($user, Response::HTTP_OK , []);
    }
}
