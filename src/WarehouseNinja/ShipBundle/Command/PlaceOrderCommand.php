<?php

namespace WarehouseNinja\ShipBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use WarehouseNinja\ShipBundle\Entity\OrderProduct;
use WarehouseNinja\ShipBundle\Entity\ShipOrder;
use WarehouseNinja\ShipBundle\Entity\Warehouse;


class PlaceOrderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ship:order:place')
            ->setDescription('place an order')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shipService = $this->getContainer()->get('warehouse_ninja_ship.ship');
        $shipOrder = new ShipOrder();

        $helper = $this->getHelper('question');
        $question = new Question('What is the address we are shipping to? ', '435 Indio Way, Sunnyvale, CA 94085');
        $address = $helper->ask($input, $output, $question);
        $shipOrder->setAddress($address);

        $latLon = $this->getContainer()->get('warehouse_ninja_ship.geolocation')->getLatLonForAddress($address);
        if ($latLon == null) {
            throw new \Exception('Latitude and Longitude could not be determined from address');
        }
        $shipOrder->setLatitude($latLon['lat']);
        $shipOrder->setLongitude($latLon['lon']);

        $shipService->save($shipOrder);

        /** @var Warehouse[] $warehouses */
        $warehouses = [];

        $sorted = $shipService->getWarehousesOrderedByDistance($shipOrder->getLatitude(), $shipOrder->getLongitude());
        foreach ($sorted as $s) {
            $warehouses[] = $s['warehouse'];
        }

        while(1) {
            $question = new Question('What product? (leave blank to finish) ');
            $productName = $helper->ask($input, $output, $question);
            if ($productName == null) {
                break;
            }
            $product = $shipService->getProductById($productName);
            if ($product == null) {
                $product = $shipService->getProductByName($productName);
            }
            if ($product == null) {
                $output->writeln('<error>That product does not exist</error>');
                continue;
            }

            $question = new Question('How many? ');
            $question->setValidator(function ($answer) {
                if (!is_numeric($answer)) {
                    throw new \RuntimeException(
                        'You must enter a valid integer amount'
                    );
                }
                return $answer;
            });
            $orderAmount = $helper->ask($input, $output, $question);

            $orderFulfilled = false;
            foreach($warehouses as $warehouse) {
                if ($orderFulfilled != true) {
                    $stock = $shipService->calulateStockForProductInWarehouse($warehouse, $product);
                    if ($stock >= $orderAmount) {
                        //we can fufill the order
                        $op = new OrderProduct();
                        $op->setWarehouse($warehouse);
                        $op->setProduct($product);
                        $op->setProductAmount($orderAmount);
                        $op->setShipOrder($shipOrder);
                        $shipService->save($op);
                        $orderFulfilled = true;
                    }
                }
            }
            if ($orderFulfilled == false) {
                $output->writeln('<error>There is not enough inventory in any warehouse to fulfill that order</error>');
            }
        }

        $output->writeln('Order Successfully Created');

    }
}