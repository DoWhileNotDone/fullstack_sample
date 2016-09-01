<?php

namespace Daveg\BookingBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 * @MongoDB\Document(repositoryClass="Daveg\BookingBundle\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    private $service;

    /**
     * @MongoDB\Field(type="string")
     */
    private $customer;

    /**
     * @MongoDB\Field(type="string")
     */
    private $staff;

    /**
     * @MongoDB\Field(type="date")
     */
    private $start_date;

    /**
     * @MongoDB\Field(type="date")
     */
    private $end_date;


    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set service
     *
     * @param string $service
     * @return $this
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Get service
     *
     * @return string $service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set customer
     *
     * @param string $customer
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Get customer
     *
     * @return string $customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set staff
     *
     * @param string $staff
     * @return $this
     */
    public function setStaff($staff)
    {
        $this->staff = $staff;
        return $this;
    }

    /**
     * Get staff
     *
     * @return string $staff
     */
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     * Set startDate
     *
     * @param date $startDate
     * @return $this
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;
        return $this;
    }

    /**
     * Get startDate
     *
     * @return date $startDate
     */
    public function getStartDate()
    {
        return date_format($this->start_date, 'd/m/Y H:i');
    }

    /**
     * Set endDate
     *
     * @param date $endDate
     * @return $this
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;
        return $this;
    }

    /**
     * Get endDate
     *
     * @return date $endDate
     */
    public function getEndDate()
    {
        return date_format($this->end_date, 'd/m/Y H:i');
    }
}
