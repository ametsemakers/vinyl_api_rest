<?php
namespace App\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\Type\UserType;
use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;


class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\View()
     * @Rest\Get("/users")
     */
    public function getUsersAction(Request $request)
    {
        $users = $this->getDoctrine()
                      ->getRepository('App:User')
                      ->findAll()
        ;
        /* @var $users User[] */

        return $users;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users/{user_id}")
     */
    public function getUserAction(Request $request)
    {
        $user = $this->getDoctrine()
                     ->getRepository('App:User')
                     ->find($request->get('user_id'))
        ;
        /* @var $user User */

        if (empty($user)) 
        {
            //return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found');
        }

        return $user;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post("/users")
     */
    public function postUsers(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User;

        $form = $this->createForm(UserType::class, $user, ['validation_groups'=>['Default', 'New']]);

        $form->submit($request->request->all());

        if ($form->isValid())
        {
            $encoded = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $user;
        }
        else
        {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/users/{user_id}")
     */
    public function removeUser(Request $request)
    {
        $em = $this->getDoctrine();
        $user = $em->getRepository('App:User')
                   ->find($request->get('user_id'))
        ;
        /* @var $user User */

        if ($user)
        {
            $em->remove($user);
            $em->flush();
        }
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Put("/users/{id}")
     */
    public function updateUser(Request $request)
    {
        return $this->userUpdate($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Patch("/users/{id}")
     */
    public function patchUser(Request $request)
    {
        return $this->userUpdate($request, false);
    }

    private function userUpdate(Request $request, $clearMissing)
    {
        $user = $this->getDoctrine()
                     ->getRepository('App:User')
                     ->find($request->get('id'))
        ;
        /* @var $user User */

        if (empty($user))
        {
            return $this->userNotFound();
        }

        if ($clearMissing) // si 'put', le mot de pass doit être validé
        {
            $options = ['validation_groups'=>['Default', 'FullUpdate']];
        }
        else // sinon, si 'patch', le groupe de validation est Default
        {
            $options = []; 
        }

        $form = $this->createForm(UserType::class, $user, $options);

        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid())
        {
            // si l'utilisateur veut changer son mot de passe
            if (!empty($user->getPlainPassword))
            {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($encoded);
            }
            $em = $this->getDoctrine();
            $em->merge($user);
            $em->flush();

            return $user;
        }
        else
        {
            return $form;
        }
    }

    private function userNotFound()
    {
        //return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('User not found');
    }
}