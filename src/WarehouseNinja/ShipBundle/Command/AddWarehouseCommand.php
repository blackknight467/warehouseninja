<?php

namespace WarehouseNinja\ShipBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use WarehouseNinja\ShipBundle\Entity\Warehouse;


class AddWarehouseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ship:warehouse:add')
            ->setDescription('add a warehouse to the system')
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
        $warehouse = new Warehouse();
        $helper = $this->getHelper('question');

        $question = new Question('What shall the name of the warehouse be? ', 'Warehouse 13');
        $name = $helper->ask($input, $output, $question);
        $warehouse->setName($name);

        $question = new Question('What is the address of the warehouse? ', '500 East Capitol Avenue, Pierre, SD 57501');
        $address = $helper->ask($input, $output, $question);
        $warehouse->setAddress($address);

        $latLon = $this->getContainer()->get('warehouse_ninja_ship.geolocation')->getLatLonForAddress($address);
        if ($latLon == null) {
            throw new \Exception('Latitude and Longitude could not be determined from address');
        }
        $warehouse->setLatitude($latLon['lat']);
        $warehouse->setLongitude($latLon['lon']);

        $this->getContainer()->get('warehouse_ninja_ship.ship')->save($warehouse);

        $output->writeln('Warehouse "' . $warehouse->getName() . '" Created.');
    }
}