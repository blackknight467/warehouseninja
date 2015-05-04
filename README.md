Warehouse Ninja
===============

A command line interface built with symfony to test out some order fulfillment and google's geocoding api.

Setup
-----
Requires PHP 5.4+, a MYSQL database connection, and [Composer](https://getcomposer.org/).

`$ composer.phar install`

You will be prompted to enter some setup information such as your database connection information,  you can ignore the the mail setup.

To create your database, simply run the following commands:

`$ php app/console doctrine:database:create`

`$ php app/console doctrine:schema:create`

You are now fully up and running.

Commands
--------
To add a warehouse:

`$ php app/console ship:warehouse:add`

To add a product:

`$ php app/console ship:product:add`

To stock a warehouse with a product:

`$ php app/console ship:stock:add`

To simulate an order placement:

`$ php app/console ship:order:place`


To Do
-----
- Add Tests
- Add Fixtures
- Move warehouse inventory count to a synchronized key value store such as redis or aws dynamodb
- Answer Question: If no single warehouse has enough inventory to fulfill an order, but we have enough total inventory, should we divide up the order among each of the warehouses?

