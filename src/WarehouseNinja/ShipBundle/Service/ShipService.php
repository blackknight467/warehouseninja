<?php

namespace WarehouseNinja\ShipBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WarehouseNinja\ShipBundle\Entity\OrderProduct;
use WarehouseNinja\ShipBundle\Entity\Product;
use WarehouseNinja\ShipBundle\Entity\Stock;
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
     * @param Warehouse $warehouse
     * @param Product $product
     * @return Stock[]
     */
    public function getStockByWarehouseProduct(Warehouse $warehouse, Product $product)
    {
        return $this->em->getRepository('WarehouseNinjaShipBundle:Stock')->getByWarehouseProduct($warehouse, $product);
    }

    /**
     * @param Warehouse $warehouse
     * @param Product $product
     * @return OrderProduct[]
     */
    public function getOrderProductsByWarehouseProduct(Warehouse $warehouse, Product $product)
    {
        return $this->em->getRepository('WarehouseNinjaShipBundle:OrderProduct')->getByWarehouseProduct($warehouse, $product);
    }

    /**
     * @param Warehouse $warehouse
     * @param Product $product
     * @return int
     */
    public function calulateStockForProductInWarehouse(Warehouse $warehouse, Product $product)
    {
        //get the all the stock for the product in the warehouse
        $stock = $this->getStockByWarehouseProduct($warehouse, $product);
        //get all the order products for that product where the warehouse was the filler
        $orderProducts = $this->getOrderProductsByWarehouseProduct($warehouse, $product);

        $total = 0;
        foreach($stock as $s) {
            $total += $s->getAmount();
        }
        foreach($orderProducts as $op) {
            $total -= $op->getProductAmount();
        }

        return $total;
    }

    /**
     * @param $lat
     * @param $lon
     * @return array
     */
    public function getWarehousesOrderedByDistance($lat, $lon)
    {
        $warehouses = $this->em->getRepository('WarehouseNinjaShipBundle:Warehouse')->findAll();
        $geoService = $this->container->get('warehouse_ninja_ship.geolocation');
        $sorted = [];
        foreach($warehouses as $warehouse) {
            $startCount = count($sorted);
            $distance = $geoService->calculateDistance($lat, $lon, $warehouse->getLatitude(), $warehouse->getLongitude());
            foreach ($sorted as $i => $s) {
                if ($s['distance'] > $distance) {
                    array_splice($sorted, $i, 0, [['warehouse' => $warehouse, 'distance' => $distance]]);
                }
            }
            if ($startCount == count($sorted)) {
                $sorted[] = ['warehouse' => $warehouse, 'distance' => $distance];
            }
        }

        return $sorted;
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