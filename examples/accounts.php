<?php

require __DIR__ . '/../vendor/autoload.php';

use Ehimen\DuCollection\DuCollection;

class Account
{
    private $balance;
    
    public function __construct(int $startingBalance)
    {
        $this->balance = $startingBalance;
    }
    
    public function print()
    {
        echo sprintf('Account balance: %d%s', $this->balance, PHP_EOL);
    }
    
    public function getBalance() : int
    {
        return $this->balance;
    }
    
    public function credit($amount)
    {
        $this->balance += $amount;
    }
}

/*
 * Create a collection of accounts.
 */
$collection = new DuCollection(Account::class);
$collection->add(new Account(10));
$collection->add(new Account(20));
$collection->add(new Account(30));

/*
 * Invoke a method of Account on the collection itself.
 * This invokes the method on all contained arguments.
 */
$collection->print();
// Account balance: 10
// Account balance: 20
// Account balance: 30

/*
 * Methods which return a value will be returned in an array when called on the collection.
 */
$balanceSum = array_sum($collection->getBalance());
echo $balanceSum;
// 60

/*
 * Credit all accounts with 10.
 * Note that methods which don't return a value will not return an array of nulls, but instead return null.
 */
$return = $collection->credit(10);
echo gettype($return);
// NULL

$collection->print();
// Account balance: 20
// Account balance: 30
// Account balance: 40