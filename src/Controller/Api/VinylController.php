<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use App\Entity\Vinyl;
use App\Form\Type\VinylType;
use JMS\Serializer\SerializerInterface;

class VinylController extends AbstractController
{
    /**
     * @Rest\View(serializerGroups={"vinyl"})
     * @Rest\Get("/vinyls")
     * @QueryParam(name="offset", requirements="\d+", default="", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="", description="Index de fin de la pagination")
     * @QueryParam(name="sort", requirements="(asc|desc)", nullable=true, description="Ordre de tri (basé sur le nom d'artiste"))
     */
    public function getVinyls(Request $request, ParamFetcher $paramFetcher, SerializerInterface $serialize)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('v')
           ->from('App:Vinyl', 'v');

        if ($offset != "")
        {
            $qb->setFirstResult($offset);
        }
        if ($limit != "")
        {
            $qb->setMaxResults($limit);
        }
        if (in_array($sort, ['asc', 'desc']))
        {
            $qb->orderBy('v.artist', $sort);
        }

        $vinyls = $qb->getQuery()->getResult();

        // $vinyls = $this->getDoctrine()
        //         ->getRepository('App:Vinyl')
        //         ->findAll();
        // /* @var $vinyls Vinyl[] */
        
        //$serialize->serialize($vinyls, 'json');
        return $vinyls;
    }

    /**
     * @Rest\View(serializerGroups={"vinyl"})
     * @Rest\Get("/vinyls/{id}")
     */
    public function getVinyl(Request $request)
    {
        $vinyl = $this->getDoctrine()
                ->getRepository('App:Vinyl')
                ->find($request->get('id'));
        /* @var $vinyl Vinyl */

        if (empty($vinyl)) 
        {
            // methode FosRest
            // return \FOS\RestBundle\View\View::create(['message' => 'Vinyl not found'], Response::HTTP_NOT_FOUND);
            // methode symfony
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Vinyl not found');
        }

        return $vinyl;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"vinyl"})
     * @Rest\Post("/vinyls")
     */
    public function postVinyls(Request $request)
    {
        $vinyl = new Vinyl();

        $form = $this->createForm(VinylType::class, $vinyl);

        $form->submit($request->request->all()); //validation

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            foreach ($vinyl->getSongs() as $song)
            {
                $song->setVinyl($vinyl);
                $em->persist($song);
            }
            $image = $vinyl->getImage();
            
            $image->setVinyl($vinyl);
            $em->persist($image);
            
            $em->persist($vinyl);
            $em->flush();

            return $vinyl;
        }
        else
        {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"vinyl"})
     * @Rest\Delete("/vinyls/{id}")
     */
    public function removeVinyl(Request $request)
    {
        $em = $this->getDoctrine();
        $vinyl = $em->getRepository('App:Vinyl')
                    ->find($request->get('id'))
        ;
        /* @var $vinyl Vinyl */

        if (!$vinyl)
        {
            return;
        }

        foreach ($vinyl->getSongs() as $song)
        {
            $em->remove($song);
        }

        $em->remove($vinyl);
        $em->flush();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"vinyl"})
     * @Rest\Put("/vinyls/{id}")
     */
    public function putVinyl(Request $request)
    {
        $vinyl = $this->getDoctrine()
                      ->getRepository('App:Vinyl')
                      ->find($request->get('id'))
        ;
        /* @var $vinyl Vinyl */

        if (empty($vinyl))
        {
            //return \FOS\RestBundle\View\View::create(['message' => 'Vinyl not found'], Response::HTTP_NOT_FOUND);
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Vinyl not found');
        }

        $vinylObject = new Vinyl();
        $form = $this->createForm(VinylType::class, $vinylObject);

        $form->submit($request->request->all());

        if ($form->isValid())
        {
            $em = $this->getDoctrine();
            // l'entité vient de la base, donc le merge n'est pas nécessaire,
            // il est uniquement utilisé par soucis de clarté.
            $em->merge($vinyl);
            $em->flush();

            return $vinyl;
        }
        else
        {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"vinyl"})
     * @Rest\Patch("/vinyls/{id}")
     */
    public function patchVinyl(Request $request)
    {
        $vinyl = $this->getDoctrine()
                      ->getRepository('App:Vinyl')
                      ->find($request->get('id'))
        ;
        /* @var $vinyl Vinyl */

        if (empty($vinyl))
        {
            //return \FOS\RestBundle\View\View::create(['message' => 'Vinyl not found'], Response::HTTP_NOT_FOUND);
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Vinyl not found');
        }

        $form = $this->createForm(VinylType::class, $vinyl);

         // Le paramètre false dit à Symfony de garder les valeurs dans notre 
         // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), false);

        if ($form->isValid())
        {
            $em = $this->getDoctrine();
            
            $em->merge($vinyl);
            $em->flush();

            return $vinyl;
        } 
        else 
        {
            return $form;
        }
    }
}