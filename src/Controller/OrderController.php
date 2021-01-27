<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use AndyDune\MgmtIntaxxApi\Configuration;
use AndyDune\MgmtIntaxxApi\Api\DefaultApi;
use AndyDune\MgmtIntaxxApi\Model\Order as AndyOrder;
use App\Entity\Order;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function getForm()
    {
        return $this->render('order.html.twig');
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function sendOrder(Request $request, EntityManagerInterface $entityManager)
    {

        $config = Configuration::getDefaultConfiguration()
            ->setApiKey(
                'X-API-KEY', 
                'hspi7bcb2s0hd5x2u7nuuuc01b6cpa7wkx1h1h48r363a674rxl3le6mplckafgxxkk25mpwfx8r2a02x5e43g'
            )
            ->setHost('https://dev-mgmt.intaxx.com/api/v1');

        $apiInstance = new DefaultApi(
            new Client(),
            $config
        );

        $body = new AndyOrder([
            'email' => $request->request->get('email'),
            'gender' => $request->request->get('gender'),
            'phone' => $request->request->get('phone'), 
            'birthday' => $request->request->get('birthday'), 
            'firstName' => $request->request->get('firstName'), 
            'lastName' => $request->request->get('lastName'), 
            'street' => $request->request->get('street'), 
            'house' => $request->request->get('house'), 
            'zip' => $request->request->get('zip'), 
            'city' => $request->request->get('city')
        ]);

        try {
            $apiInstance->createOrderPost($body);

            $order = new Order();
            $order->setEmail($request->request->get('email'));
            $order->setGender($request->request->get('gender'));
            $order->setPhone($request->request->get('phone'));
            $order->setBirthday(new DateTime($request->request->get('birthday')));
            $order->setFirstName($request->request->get('firstName'));
            $order->setLastName($request->request->get('lastName'));
            $order->setStreet($request->request->get('street'));
            $order->setHouse($request->request->get('house'));
            $order->setZip($request->request->get('zip'));
            $order->setCity($request->request->get('city'));

            $entityManager->persist($order);
            $entityManager->flush();

        } catch (Exception $e) {
            echo 'Exception when calling createOrderPost: ', $e->getMessage(), PHP_EOL;
            die;
        }

        return $this->redirect('/');
    }
}
