<?php
namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\Type\CredentialsType;
use App\Entity\AuthToken;
use App\Entity\Credentials;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class AuthTokenController extends AbstractFOSRestController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/auth-tokens")
     */
    public function postAuthTokensAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine();

        $user = $em->getRepository('App:User')
                   ->findOneByEmail($credentials->getLogin())
        ;

        if (!$user)
        {
            return $this->invalidCredentials();
        }

        $isPasswordValid = $passwordEncoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid)
        {
            return $this->invalidCredentials;
        }

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $em->getManager()->persist($authToken);
        $em->getManager()->flush();

        return$this->handleView($this->view($authToken));
        //return $authToken;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/auth-tokens/{id}")
     */
    public function removeAuthToken(Request $request)
    {
        $em = $this->getDoctrine();
        $authToken = $em->getRepository('App:AuthToken')
                        ->find($request->get('id'))
        ;
        /* @var $authToken AuthToken */

        $connectedUser = $this->get('security.token_storage')->getToken()->getUser();

        if ($authToken && $authToken->getUser()->getId() === $connectedUser->getId())
        {
            $em->getManager()->remove($authToken);
            $em->getManager()->flush();
        }
        else
        {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('Invalid token');
        }
    }

    private function invalidCredentials()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Invalid credentials'], Response::HTTP_BAD_REQUEST);
    }
}