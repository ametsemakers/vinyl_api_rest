<?php

namespace App\Controller\Api\Vinyl;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Vinyl;
use App\Entity\Song;
use App\Form\Type\SongType;

class SongController extends AbstractController
{
    /**
     * @Rest\View(serializerGroups={"vinyl"})
     * @Rest\Get("/vinyls/{id}/songs")
     */
    public function getSongsFromVinyl(Request $request)
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

        return $vinyl->getSongs();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"vinyl"})
     * @Rest\Post("/vinyls/{id}/songs")
     */
    public function postSongs(Request $request)
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

        $song = new Song();
        $song->setVinyl($vinyl);

        $form = $this->createForm(SongType::class, $song);

        $form->submit($request->request->all()); //validation

        if ($form->isValid())
        {
            $em = $this->getDoctrine();
            $em->persist($song);
            $em->flush();

            return $song;
        }
        else
        {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"vinyl"})
     * @Rest\Put("/vinyls/{id_vinyl}/songs/{id_song}")
     */
    public function putSong(Request $request)
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

        $song = $this->getDoctrine()
                      ->getRepository('App:Song')
                      ->getSongFromVinyl($request->get('id_vinyl'))
        ;
        /* @var $song Song */

        if (empty($song))
        {
            return $this->queryNotFound('song');
        }
        if ($song->getIdVinyl() !== $vinyl->getId())
        {
            //return \FOS\RestBundle\View\View::create(['message' => 'Song not on vinyl'], Response::HTTP_NOT_FOUND);
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Song not on vinyl');
        }

        $songObject = new Song();
        $song->setVinyl($vinyl);

        $form = $this->createForm(SongType::class, $songObject);

        $form->submit($request->request->all());

        if ($form->isValid())
        {
            $em = $this->getDoctrine();
            // l'entité vient de la base, donc le merge n'est pas nécessaire,
            // il est uniquement utilisé par soucis de clarté.
            $em->merge($song);
            $em->flush();

            return $song;
        }
        else
        {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"vinyl"})
     * @Rest\Patch("/vinyls/{id_vinyl}/songs/{id_song}")
     */
    public function patchSong(Request $request)
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

        $song = $this->getDoctrine()
                      ->getRepository('App:Song')
                      ->getSongFromVinyl($request->get('id_vinyl'))
        ;
        /* @var $song Song */

        if (empty($song))
        {
            return $this->queryNotFound('song');
        }
        if ($song->getIdVinyl() !== $vinyl->getId())
        {
            //return \FOS\RestBundle\View\View::create(['message' => 'Song not on vinyl'], Response::HTTP_NOT_FOUND);
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Song not on vinyl');
        }

        $song->setVinyl($vinyl);

        $form = $this->createForm(SongType::class, $song);

         // Le paramètre false dit à Symfony de garder les valeurs dans notre 
         // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), false);

        if ($form->isValid())
        {
            $em = $this->getDoctrine();
            
            $em->merge($song);
            $em->flush();

            return $song;
        } 
        else 
        {
            return $form;
        }
    }

    /**
     * @Rest\View(serializerGroups={"song"})
     * @Rest\Get("/songs")
     */
    public function getSongs(Request $request)
    {
        $songs = $this->getDoctrine()
                ->getRepository('App:Song')
                ->findAll();
        /* @var $song Song[] */
        
        return $songs;
    }

    /**
     * @Rest\View(serializerGroups={"song"})
     * @Rest\Get("/songs/{id}")
     */
    public function getSong(Request $request)
    {
        $song = $this->getDoctrine()
                ->getRepository('App:Song')
                ->find($request->get('id'));
        /* @var $song Song */

        if (empty($song)) 
        {
            return $this->queryNotFound('song');
        }

        return $song;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"song"})
     * @Rest\Delete("/songs/{id}")
     */
    public function removeSongs(Request $request)
    {
        $em = $this->getDoctrine();
        $song = $em->getRepository('App:Song')
                    ->find($request->get('id'))
        ;
        /* @var $song Song */

        if ($song)
        {
            $em->remove($song);
            $em->flush();
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
            //return \FOS\RestBundle\View\View::create(['message' => 'Song not found'], Response::HTTP_NOT_FOUND);
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Song not found');
        }       
    }
}