<?php

namespace WarehouseNinja\ShipBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use WarehouseNinja\ShipBundle\Entity\Product;


class AddProductCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ship:product:add')
            ->setDescription('add a product to the system')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $product = new Product();
        $helper = $this->getHelper('question');

        $question = new Question('What shall the name of the product be?', 'Adipose');
        $name = $helper->ask($input, $output, $question);
        $product->setName($name);

        $question = new Question('What is the height of the product? (in inches)', '1');
        $height = $helper->ask($input, $output, $question);
        $product->setHeight($height);

        $question = new Question('What is the width of the product? (in inches)', '1');
        $width = $helper->ask($input, $output, $question);
        $product->setWidth($width);

        $question = new Question('What is the length of the product? (in inches)', '1');
        $length = $helper->ask($input, $output, $question);
        $product->setLength($length);

        $question = new Question('What is the weight of the product? (in ounces)', '1');
        $weight = $helper->ask($input, $output, $question);
        $product->setWeight($weight);

        $this->getContainer()->get('warehouse_ninja_ship.ship')->save($product);

        $output->writeln('Product "' . $product->getName() . '" Created.');
    }
}