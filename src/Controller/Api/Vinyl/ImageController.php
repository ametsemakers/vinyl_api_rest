<?php

namespace App\Controller\Api\Vinyl;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Vinyl;
use App\Entity\Image;
use App\Form\Type\ImageType;

class ImageController extends AbstractController
{
    /**
     * @Rest\View(serializerGroups={"vinyl"})
     * @Rest\Get("/vinyls/{id}/images")
     */
    public function getImage(Request $request)
    {
        $vinyl = $this->getDoctrine()
                      ->getRepository('App:Vinyl')
                      ->find($request->get('id'))
        ;
        /* @var $vinyl Vinyl */

        if (empty($vinyl))
        {
            return $this->queryNotFound('vinyl');
        }

        return $vinyl->getImage();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"vinyl"})
     * @Rest\Post("/vinyls/{id}/images")
     */
    public function postImage(Request $request)
    {
        $vinyl = $this->getDoctrine()
                      ->getRepository('App:Vinyl')
                      ->find($request->get('id'))
        ;
        /* @var $vinyl Vinyl */

        if (empty($vinyl))
        {
            return $this->queryNotFound('vinyl');
        }

        $image = new Image();
        $image->setVinyl($vinyl);

        $form = $this->createForm(ImageType::class, $image);

        $form->submit($request->request->all()); //validation

        if ($form->isValid())
        {
            $em = $this->getDoctrine();
            $em->persist($image);
            $em->flush();

            return $image;
        }
        else
        {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"vinyl"})
     * @Rest\Put("/vinyls/{id_vinyl}/images/{id_image}")
     */
    public function putImage(Request $request)
    {
        $vinyl = $this->getDoctrine()
                      ->getRepository('App:Vinyl')
                      ->find($request->get('id_vinyl'))
        ;
        /* @var $vinyl Vinyl */

        if (empty($vinyl))
        {
            return $this->queryNotFound('vinyl');
        }

        $image = $this->getDoctrine()
                      ->getRepository('App:Image')
                      ->getImageFromVinyl($request->get('id_vinyl'))
        ;
        /* @var $image Image */

        if (empty($image))
        {
            return $this->queryNotFound('image');
        }
        if ($image->getIdVinyl() !== $vinyl->getId())
        {
            //return \FOS\RestBundle\View\View::create(['message' => 'Image not from vinyl'], Response::HTTP_NOT_FOUND);
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Image not from vinyl');
        }

        $imageObject = new Image();
        $image->setVinyl($vinyl);

        $form = $this->createForm(ImageType::class, $imageObject);

        $form->submit($request->request->all());

        if ($form->isValid())
        {
            $em = $this->getDoctrine();
            // l'entité vient de la base, donc le merge n'est pas nécessaire,
            // il est uniquement utilisé par soucis de clarté.
            $em->merge($image);
            $em->flush();

            return $image;
        }
        else
        {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"vinyl"})
     * @Rest\Patch("/vinyls/{id_vinyl}/images/{id_image}")
     */
    public function patchImage(Request $request)
    {
        $vinyl = $this->getDoctrine()
                      ->getRepository('App:Vinyl')
                      ->find($request->get('id_vinyl'))
        ;
        /* @var $vinyl Vinyl */

        if (empty($vinyl))
        {
            return $this->queryNotFound('vinyl');
        }

        $image = $this->getDoctrine()
                      ->getRepository('App:Image')
                      ->getImageFromVinyl($request->get('id_image'))
        ;
        /* @var $image Image */

        if (empty($image))
        {
            return $this->queryNotFound('image');
        }

        $image->setVinyl($vinyl);

        $form = $this->createForm(ImageType::class, $image);

         // Le paramètre false dit à Symfony de garder les valeurs dans notre 
         // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), false);

        if ($form->isValid())
        {
            $em = $this->getDoctrine();
            
            $em->merge($image);
            $em->flush();

            return $image;
        } 
        else 
        {
            return $form;
        }
    }
   
    private function queryNotFound($type)
    {
        if ($type == 'vinyl')
        {
            //return \FOS\RestBundle\View\View::create(['message' => 'Vinyl not found'], Response::HTTP_NOT_FOUND);
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Vinyl not found');
        }
        else
        {
            //return \FOS\RestBundle\View\View::create(['message' => 'Image not found'], Response::HTTP_NOT_FOUND);
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Image not found');
        }       
    }
}