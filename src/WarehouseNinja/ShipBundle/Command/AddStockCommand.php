<?php

namespace WarehouseNinja\ShipBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use WarehouseNinja\ShipBundle\Entity\Stock;


class AddStockCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ship:stock:add')
            ->setDescription('stock a warehouse with a product')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stock = new Stock();
        $shipService = $this->getContainer()->get('warehouse_ninja_ship.ship');
        $helper = $this->getHelper('question');
        $question = new Question('What warehouse are we stocking?', 'Warehouse 13');
        $warehouseName = $helper->ask($input, $output, $question);
        $warehouse = $shipService->getWarehouseById($warehouseName);
        if ($warehouse == null) {
            $warehouse = $shipService->getWarehouseByName($warehouseName);
        }
        if ($warehouse == null) {
            $output->writeln('That warehouse does not exist');
            return;
        }

        $question = new Question('What product are we stocking?', 'Adipose');
        $productName = $helper->ask($input, $output, $question);
        $product = $shipService->getProductById($productName);
        if ($product == null) {
            $product = $shipService->getProductByName($productName);
        }
        if ($product == null) {
            $output->writeln('That product does not exist');
            return;
        }

        $question = new Question('How much are we stocking?', '10');
        $question->setValidator(function ($answer) {
            if (!is_numeric($answer)) {
                throw new \RuntimeException(
                    'You must enter a valid integer amount'
                );
            }
            return $answer;
        });
        $stockAmount = $helper->ask($input, $output, $question);

        $stock->setProduct($product);
        $stock->setWarehouse($warehouse);
        $stock->setAmount($stockAmount);

        $shipService->save($stock);

        $output->writeln($warehouse->getName() . " has been stocked with " . $stock->getAmount() . ' ' . $product->getName() . '.');
    }
}