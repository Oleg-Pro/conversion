<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Client;
use AppBundle\Form\Type\ClientFormType;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="clients")
     */
    public function indexAction()
    {
        return $this->render(
            'default/index.html.twig',
            ['clients' => $this->getDoctrine()->getRepository(Client::class)->findBy([], ['id' => 'asc'])]
        );
    }

    /**
     * @Route("/clients/create", name="client_create")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $client = new Client();

        $form = $this->createForm(ClientFormType::class, $client);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($client);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Clients');

            return $this->redirectToRoute('clients');
        }

        return $this->render(
            'default/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/{id}/edit",  requirements={"id" = "\d+"}, name="client_update")
     *
     * @param Request $request
     * @param  Client $client
     * @return array|RedirectResponse
     */
    public function updateAction(Request $request, Client $client)
    {
        $form = $this->createForm(ClientFormType::class, $client);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($client);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Client updated');

            return $this->redirectToRoute('clients');
        }

        return $this->render(
            'default/update.html.twig',
            [
                'client' => $client,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"},  name="client_delete")
     *
     * @param Client $client
     * @return RedirectResponse
     */
    public function deleteAction(Client $client)
    {
        $this->getDoctrine()->getManager()->remove($client);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Card brand deleted');

        return $this->redirectToRoute('clients');
    }

    /**
     * @Route("/clients/conversion", name="conversion")
     */
    public function conversionAction(Request $request)
    {
        
        $period = $request->query->get('period', 7);;
        $dbConnection = $this->get('doctrine')->getManager()->getConnection();
        $sth = $dbConnection->prepare("CALL clients_convertion(?)");
        $sth->bindValue(1, $period);
        $sth->execute();
        $conversionInfo = $sth->fetchAll();
        foreach($conversionInfo as &$result) {
            $registeredNumber = floatVal($result['registered_clients_number']);
            $allNumber = floatVal($result['clients_number']);
            $result['conversion'] = $allNumber ? 100*$registeredNumber/$allNumber : 0;
        }

        return $this->render(
            'default/conversion.html.twig',
            [
                'conversionInfo' => $conversionInfo,
            ]
        );

    }
}
