<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\Picture;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\CommentFormType;
use App\Form\TrickFormType;
use App\Repository\CommentRepository;
use App\Repository\GroupRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use App\Service\PictureService;
use App\Service\VideoService;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory as FactoryClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(TrickRepository $trickRepository): Response
    {
        // $tricks = $trickRepository->findAll();
        $tricksPaginated = $trickRepository->findTricksPaginated(1);

        return $this->render('trick/homepage.html.twig', [
            'tricks' => $tricksPaginated['data'],
            'pages' => $tricksPaginated['pages'],
        ]);
    }

    #[Route('/json-tricks/{page}', name: 'app_json_tricks')]
    public function jsonTricks(
        int $page,
        TrickRepository $trickRepository
    ): Response {
        /*
         $classMetadataFactory = new FactoryClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
         $serializer = new Serializer([new ObjectNormalizer($classMetadataFactory)], [new JsonEncoder()]);
         $tricks = $trickRepository->findAll();
         $jsonGroups = $serializer->serialize($tricks, 'json');*/
        $tricks = $trickRepository->findTricksPaginated($page)['data'];
        $jsonTricks = [];

        foreach ($tricks as $trick) {
            $tabTrick = [
                'id' => $trick->getId(),
                'name' => $trick->getName(),
                'slug' => $trick->getSlug(),
            ];
            $tabGroup = [];
            foreach ($trick->getGroup() as $group) {
                array_push($tabGroup, $group->getName());
            }
            $tabTrick['groups'] = $tabGroup;

            if (count($trick->getPictures()) > 0) {
                $tabTrick['pic'] = $trick->getPictures()[0]->getId();
            }

            array_push($jsonTricks, $tabTrick);
        }

        $str_json = json_encode($jsonTricks, JSON_FORCE_OBJECT);

        return new jsonResponse($str_json);
    }

    #[Route('/trick/add', name: 'app_trick_add')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $sluggerInterface,
        GroupRepository $groupRepository,
        PictureService $pictureService,
        VideoService $videoService
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $classMetadataFactory = new FactoryClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $serializer = new Serializer([new ObjectNormalizer($classMetadataFactory)], [new JsonEncoder()]);

        $trick = new Trick();
        $groups = $groupRepository->findAll();
        $jsonGroups = $serializer->serialize($groups, 'json');
        $trickForm = $this->createForm(TrickFormType::class, $trick);

        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            $this->addFlash('notice', 'La figure '.$trick->getName().' à été ajouté.');

            /* Ajout des groupes */
            $trick->getGroup()->clear();
            $groups = $trickForm->get('group')->getData();
            foreach ($groups as $group) {
                if (!$group->getId()) {
                    $g = new Group();
                    $g->setName($group->getName());
                } else {
                    $g = $groupRepository->findOneById($group->getId());
                }
                $trick->addGroup($g);
            }

            /*** Ajout d'un lien  *
            $videos = $trick->getVideos();
            foreach($videos as $video) {
                $video->setLink($videoService->getLinks($video->getLink()));

                $trick->addVideo($video);
                $entityManagerInterface->persist($video);
            }


            /**** */

            $slug = $sluggerInterface->slug($trick->getName())->lower();
            $trick->setSlug($slug);
            $trick->setCreatedAt(new \DateTimeImmutable());
            $entityManagerInterface->persist($trick);
            $entityManagerInterface->flush();

            $images = $trickForm->get('pictures')->getData();

            foreach ($images as $key => $image) {
                $folder = '';

                $img = new Picture();
                $img->setTrick($trick);
                $entityManagerInterface->persist($img);
                $entityManagerInterface->flush();

                $fichier = $pictureService->add($image, $trick->getId().'-'.$img->getId(), '/tricks_pictures', 300, 300);
            }

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render(
            'trick/add.html.twig',
            [
            'trickForm' => $trickForm->createView(),
            'jsonGroups' => $jsonGroups,
        ]
        );

        // return $this->renderForm('trick/add.html.twig', compact("trickForm"));
    }

    #[Route('/trick/edit/{slug}', name: 'app_edit_trick')]
    public function editTrick(
        Trick $trick,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $sluggerInterface,
        GroupRepository $groupRepository,
        PictureService $pictureService,
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $classMetadataFactory = new FactoryClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $serializer = new Serializer([new ObjectNormalizer($classMetadataFactory)], [new JsonEncoder()]);
        $groups = $groupRepository->findAll();
        $jsonGroups = $serializer->serialize($groups, 'json');
        $jsonTrickGroup = $serializer->serialize($trick->getGroup(), 'json');

        $trickForm = $this->createForm(TrickFormType::class, $trick);

        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {

            $this->addFlash('notice', 'La figure '.$trick->getName().' a été modifé');
            /* Ajout des groupes */

            $trick->getGroup()->clear();
            $groups = $trickForm->get('group')->getData();
            foreach ($groups as $group) {
                if (!$group->getId()) {
                    $g = new Group();
                    $g->setName($group->getName());
                } else {
                    $g = $groupRepository->findOneById($group->getId());
                }
                $trick->addGroup($g);
            }

            $images = $trickForm->get('pictures')->getData();

            foreach ($images as $key => $image) {
                $folder = '';

                $img = new Picture();
                $img->setTrick($trick);
                $entityManagerInterface->persist($img);
                $entityManagerInterface->flush();

                $fichier = $pictureService->add($image, $trick->getId().'-'.$img->getId(), '/tricks_pictures', 300, 300);
            }

            $slug = $sluggerInterface->slug($trick->getName())->lower();
            $trick->setSlug($slug);
            $trick->setUpdatedAt(new \DateTimeImmutable());

            $entityManagerInterface->persist($trick);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('trick/edit.html.twig', [
            'trickForm' => $trickForm->createView(),
            'trick' => $trick,
            'jsonGroups' => $jsonGroups,
            'jsonTrickGroup' => $jsonTrickGroup,
        ]);
    }

    #[Route('/trick/remove/{slug}', name: 'app_remove_trick')]
    public function removeTrick(Trick $trick, EntityManagerInterface $entityManagerInterface, VideoRepository $videoRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $videos = $videoRepository->findBy(['trick' => $trick]);
        foreach ($videos as $video) {
            $entityManagerInterface->remove($video);
            $entityManagerInterface->flush();
        }

        $entityManagerInterface->remove($trick);
        $entityManagerInterface->flush();
        $this->addFlash('notice', 'La figure '.$trick->getName(). ' a été suprimé');

        return $this->redirectToRoute('app_homepage');
    }

    #[Route('/trick/{slug}', name: 'app_trick')]
    public function trick(Trick $trick, CommentRepository $commentRepository, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $page = $request->query->getInt('page', 1);
        $comments = $commentRepository->findCommentsPaginated($page, $trick->getSlug());

        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);

        if ($this->getUser() && $commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUser($this->getUser());
            $comment->setTrick($trick);
            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_trick', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/picture/remove/{id}', name: 'app_picture_remove')]
    public function removePicture(
        Picture $picture,
        PictureService $pictureService,
        EntityManagerInterface $entityManagerInterface
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if ($pictureService->delete(''.$picture->getTrick()->getId().'-'.$picture->getId().'.webp', '/tricks_pictures', 300, 300)) {
            $entityManagerInterface->remove($picture);
            $entityManagerInterface->flush();
            $this->addFlash('notice', 'L\'image a été supprimé');
        }

        return $this->redirectToRoute('app_edit_trick', ['slug' => $picture->getTrick()->getSlug()]);
    }

    #[Route('/video/remove/{id}', name: 'app_video_remove')]
    public function removeVideo(Video $video, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $entityManagerInterface->remove($video);
        $entityManagerInterface->flush();
        $this->addFlash('notice', 'La vidéo a été supprimé');

        return $this->redirectToRoute('app_edit_trick', ['slug' => $video->getTrick()->getSlug()]);
    }
}
