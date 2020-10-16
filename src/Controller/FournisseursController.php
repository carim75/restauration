<?php

namespace App\Controller;

use App\Entity\Livraison;
use App\Entity\Produit;
use App\Entity\Societe;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FournisseursController extends AbstractController
{
    /**
     * @Route("/fournisseur", name="fournisseur")
     */
    public function index()
    {
        return $this->render('fournisseur/index.html.twig', [
            'controller_name' => 'FournisseurController',
        ]);
    }

    /**
     *
     * @Route("/ajouterproduit/{idsoc}")
     *
     */
    public function ajouterProduit($idsoc, Produit $produit = null, Request $request, EntityManagerInterface $manager, ProduitRepository $produitRepository, SocieteRepository $societeRepository)
    {


        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setSociete($societeRepository->find($idsoc));
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash('success', 'Produit ajouté avec succès');
            return $this->redirectToRoute('app_index_index');
        }

        return $this->render('fournisseur/creationproduit.html.twig', [
            'FormProduit' => $form->createView(),
            'idsoc' => $idsoc
        ]);

    }

    /**
     *
     * @Route("modifierproduit/{id}")
     *
     */
    public function modifProduit(EntityManagerInterface $manager, Request $request, Produit $produit)
    {


        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash('success', 'Produit modifié avec succès');
            return $this->redirectToRoute('app_index_index');
        }

        return $this->render('fournisseur/creationproduit.html.twig', [
            'FormProduit' => $form->createView(),
        ]);

    }

    /**
     * @Route("/supprimerproduit/{id}")
     */
    public function supprimerProduit(Request $request, Produit $produit)
    {

        $delete = $this->getDoctrine()->getManager();
        $delete->remove($produit);
        $delete->flush();
        $this->addFlash('success', 'Produit supprimé avec succés');
        return $this->redirectToRoute('app_index_listeproduit');
    }

    /**
     *
     * @Route("/produitfournisseur/{id}")
     *
     */
    public function prodFournisseur($id)
    {


        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $rep->findAllOrderBy($id);

        return $this->render('fournisseur/produitsfournisseur.html.twig', [
            'produits' => $produits

        ]);


    }

    /**
     * @Route("/commandefournisseur/{id}")
     */
    public function commandesFournisseur($id)
    {

        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $rep->find($id);

        $soc = '';

        $tot = '';


        return $this->render('fournisseur/commandesfournisseur.html.twig', [

            'societe' => $societe,

            'soc' => $soc,
            'tot' => $tot
        ]);

    }

    /**
     * @Route("/livraison/{id}")
     */
    public function livraison($id)
    {
        $rep = $this->getDoctrine()->getRepository(Societe::class);
        $societe = $rep->find($id);

        $rep = $this->getDoctrine()->getRepository(Livraison::class);
        $livraisons = $rep->findAll();

        return $this->render('fournisseur/listelivraisonsfournisseur.html.twig',[
            'societe'=>$societe,
            'livraisons'=>$livraisons
        ]);

    }
    /**
     *
     * @Route("/promosfournisseur/{id}")
     *
     */
    public function promosFourn(Request $request, $id)
    {


        $rep = $this->getDoctrine()->getRepository(Produit::class);
        $produits = $rep->findAllOrderBy($id);

        $promotion = $request->query->all();
        $produitsEnPromo = $rep->findBy([
            'promotion' => $promotion
        ]);

        return $this->render('fournisseur/promofournisseur.html.twig', [
            'produits' => $produits,
            'promotion' => $produitsEnPromo
        ]);


    }
}