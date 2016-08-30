<?php

namespace Daveg\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/* Routing*/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
/* Entities*/
use Daveg\BookingBundle\Entity\Booking;
/* Response Components*/
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
/* Request Components*/
use Symfony\Component\HttpFoundation\Request;
/* Serializer*/
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DefaultController extends Controller
{

    var $serializer = null;

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('DavegBookingBundle:Default:index.html.twig');
    }

    /**
     * @Route("/booking/insert/")
     */
     public function insertBookingAction() {

       //Extract details from submit
       $request = Request::createFromGlobals();
       $details = json_decode($request->getContent(), true);

       $booking = new Booking();

       //Populate object with form data
       $booking->setService($details['service']);
       $booking->setCustomer($details['customer']);
       $booking->setStaff($details['staff']);
       //Dates are supplied as string, so we convert to dates
       $start = \DateTime::createFromFormat('d/m/Y H:i', $details['start_date']) ;
       $end = \DateTime::createFromFormat('d/m/Y H:i', $details['end_date']) ;
       
       $booking->setStartDate($start);
       $booking->setEndDate($end);


       $em = $this->getDoctrine()->getManager();
       $em->persist($booking);
       $em->flush();

       return new Response('The new booking has been stored.');

     }

    /**
     * @Route("/booking/load/{booking_id}",
     * requirements={
     * "booking_id": "\d+"
     * })
     */
    public function loadBookingAction($booking_id)
    {

        $booking = $this->getDoctrine()
                            ->getRepository('DavegBookingBundle:Booking')
                            ->find($booking_id);

        if (!$booking) {
          throw $this->createNotFoundException(
            'No booking found for id '.$booking_id );
        }

        $jsonContent = $this->getSerializer()->serialize($booking, 'json');

        return new Response($jsonContent);

    }

    /**
     * @Route("/booking/load/all.json")
     */
    public function loadBookingAllAction(){

      $repository = $this->getDoctrine()->getRepository('DavegBookingBundle:Booking');
      $bookings = $repository->findAll();

      $jsonContent = $this->getSerializer()->serialize($bookings, 'json');

      return new Response($jsonContent);
    }

    /**
     * @Route("/booking/template/grid")
     */
    public function templateGridAction() {
/*
      $repository = $this->getDoctrine()->getRepository('DavegBookingBundle:Booking');
      $bookings = $repository->findAll();

       return $this->render('DavegBookingBundle:Default:bookings.html.twig',
                            array('bookings' => $bookings)
     );
*/

    return $this->render('DavegBookingBundle:Default:bookings.html.twig');

    }

    /**
     * @Route("/booking/template/form")
     */
    public function templateFormAction() {

       return $this->render('DavegBookingBundle:Default:bookingform.html.twig');

    }

    /**
     * @Route("/booking/template/tabs")
     */
    public function templateTabsAction() {

       return $this->render('DavegBookingBundle:Default:bookingtabs.html.twig');

    }


    //TODO: Extract into shared module
    private function getSerializer() {

      if ($this->serializer)
        return $this->serializer;

      $encoders = array(new JsonEncoder());
      $normalizers = array(new ObjectNormalizer());

      $this->serializer = new Serializer($normalizers, $encoders);

      return $this->serializer;
    }

}
