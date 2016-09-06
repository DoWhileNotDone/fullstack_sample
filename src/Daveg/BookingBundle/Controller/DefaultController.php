<?php

namespace Daveg\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/* Routing*/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
/* Entities*/
use Daveg\BookingBundle\Document\Booking;
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
       return $this->renderTemplate('index');
    }

    /**
     * @Route("/bookings")
     * Valid Params:
     *   GET: {no params} INDEX
     *   * Data: Input - {} | Output  - text/html
     *   GET: template='name of bundle template'
     *   * Data: Input - {} | Output  - text/html
     *   GET: data = $booking_id, all
     *   * Data: Input - {} | Output  - application/json, application/xml
     *   POST: insert
     *   * Data: Input - application/json | Output  - text/plain
     */
    public function restfulAPI() {

      $request = Request::createFromGlobals();

      switch($request->getMethod()) {
        case 'GET':
          $response = $this->handleGetRequest();
          break;
        case 'POST':
          $response = $this->handlePostRequest();
          break;
        default:
          //TODO: Extend Response to set params
          //Return Not Supported Request Method
          $response = new Response();

          $response->setContent('Request Method Not Supported');
          $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

          //TODO: Set Content Type to match Request Accept
          $response->headers->set('Content-Type', 'text/plain');

      }

      return $response;

    }

    private function handleGetRequest(){

      $request = Request::createFromGlobals();

      //Handle index
      if (!count($request->query->all()))
        return $this->handleGetTemplateRequest('index');

      //Handle template
      if ($request->query->has('template'))
        return $this->handleGetTemplateRequest($request->query->get('template'));

      //Handle data
      if ($request->query->has('data'))
        return $this->handleGetDataRequest($request->query->get('data'));

      //TODO: Return Invalid GET Request

    }

    private function handleGetDataRequest($booking_id) {

      $accepted_request_content_types = array();
      $accepted_response_content_types = array('application/json');

      if ($response = $this->validateRequest($accepted_request_content_types,
                                              $accepted_response_content_types))
        return $response;


      if ($booking_id == 'all')
         $data =  $this->loadBookingAllAction();
      else
         $data =  $this->loadBookingAction($booking_id);

      //TODO: FORMAT RESPONSE§

      return $data;

    }

    private function handleGetTemplateRequest($template_name) {

      //At the moment the only expected POST request is Insert Booking
      $accepted_request_content_types = array();
      //Angular Request Accept Settings are set to
      // application/json, text/plain, */*
      // However the expected response is text/html
      $accepted_response_content_types = array('text/html', 'text/plain');

      if ($response = $this->validateRequest($accepted_request_content_types,
                                              $accepted_response_content_types))
        return $response;

      return $this->renderTemplate($template_name);

    }


    private function handlePostRequest(){

      //At the moment the only expected POST request is Insert Booking
      $accepted_request_content_types = array('application/json');
      $accepted_response_content_types = array('text/plain');

      if ($response = $this->validateRequest($accepted_request_content_types,
                                              $accepted_response_content_types))
        return $response;

      return $this->insertBookingAction();

    }

    private function validateRequest($accepted_request_content_types, $accepted_response_content_types) {

      $request = Request::createFromGlobals();

      $content_type = $request->headers->get('content_type');

      if (!empty($accepted_request_content_types) && !in_array($content_type, $accepted_request_content_types)) {

        $response = new Response();

        $response->setContent('Request Content Type Not Supported');
        $response->setStatusCode(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);

        //TODO: Set Content Type to match Request Accept
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
      }

      //TODO: Check Accepted Response matches what the interface can supply
      //TODO: Include Wildcards *
      $accepted = $request->getAcceptableContentTypes();

      if (false) {

        $response = new Response();

        $response->setContent('Response cannot meet Request Accept');
        $response->setStatusCode(Response::HTTP_NOT_ACCEPTABLE);

        //TODO: Set Content Type to match Request Accept
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
      }

      return false;

    }


    /**
     * @Route("/booking/insert/")
     */
     public function insertBookingAction() {

       //Extract details from submit
       $request = Request::createFromGlobals();
       $details = json_decode($request->getContent(), true);

       $booking = new Booking();
       //Sanitize User Input
       $service = filter_var($details['service'], FILTER_SANITIZE_STRING);
       $customer = filter_var($details['customer'], FILTER_SANITIZE_STRING);
       $staff = filter_var($details['staff'], FILTER_SANITIZE_STRING);
       $start_date = filter_var($details['start_date'], FILTER_SANITIZE_STRING);
       $end_date = filter_var($details['end_date'], FILTER_SANITIZE_STRING);

       //Dates are supplied as string, so we convert to dates
       $start = \DateTime::createFromFormat('d/m/Y H:i', $start_date) ;
       $end = \DateTime::createFromFormat('d/m/Y H:i', $end_date) ;

       //Populate object with form data
       $booking->setService($service);
       $booking->setCustomer($customer);
       $booking->setStaff($staff);
       $booking->setStartDate($start);
       $booking->setEndDate($end);

       $em = $this->get('doctrine_mongodb')->getManager();
       $em->persist($booking);
       $em->flush();

       return new Response('The new booking has been stored.');

     }

    /**
     * @Route("/booking/load/{booking_id}.json",
     * requirements={
     * "booking_id": "\d+"
     * })
     */
    public function loadBookingAction($booking_id)
    {

        $booking = $this->get('doctrine_mongodb')
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

      $repository = $this->get('doctrine_mongodb')->getRepository('DavegBookingBundle:Booking');
      $bookings = $repository->findAll();

      $jsonContent = $this->getSerializer()->serialize($bookings, 'json');

      return new Response($jsonContent);
    }

    /**
     * @Route("/booking/template/grid")
     */
    public function templateGridAction() {
      return $this->renderTemplate('bookings');
    }

    /**
     * @Route("/booking/template/form")
     */
    public function templateFormAction() {
       return $this->renderTemplate('bookingform');
    }

    /**
     * @Route("/booking/template/tabs")
     */
    public function templateTabsAction() {
       return $this->renderTemplate('bookingtabs');
    }

    private function renderTemplate($templateName) {

      try {

        $template = "DavegBookingBundle:Default:{$templateName}.html.twig";
        return $this->render($template);

      } catch (Exception $e) {


      }

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
