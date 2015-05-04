<?php

namespace WarehouseNinja\ShipBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WarehouseNinja\ShipBundle\Entity\Product;
use WarehouseNinja\ShipBundle\Entity\Warehouse;

class ShipService {

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param ContainerInterface $container
     * @param EntityManager $em
     */
    public function __construct(ContainerInterface $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @param $id
     * @return null|Warehouse
     */
    public function getWarehouseById($id)
    {
        if (is_numeric($id)) {
            return $this->em->getRepository('WarehouseNinjaShipBundle:Warehouse')->find($id);
        }
        return null;
    }

    /**
     * @param $name
     * @return null|Warehouse
     */
    public function getWarehouseByName($name)
    {
        return $this->em->getRepository('WarehouseNinjaShipBundle:Warehouse')->findOneByName($name);
    }

    /**
     * @param $id
     * @return null|Product
     */
    public function getProductById($id)
    {
        if (is_numeric($id)) {
            return $this->em->getRepository('WarehouseNinjaShipBundle:Product')->find($id);
        }
        return null;
    }

    /**
     * @param $name
     * @return null|Product
     */
    public function getProductByName($name)
    {
        return $this->em->getRepository('WarehouseNinjaShipBundle:Product')->findOneByName($name);
    }

    /**
     * generic save utility function
     *
     * @param $object
     * @param boolean $flush
     */
    public function save($object, $flush = true)
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        $this->em->persist($object);

        if ($flush) {
            $this->em->flush();
        }
    }

}