<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Quazymodo\AbstractController;
use Quazymodo\ComponentFactory;

class Coinchange2 
{
  private int $amount = 0;
  private array $coins = [];
  private array $desiredBranches = [];
  private int $solution = 0;
  public function index(ServerRequestInterface $request)
  {
    $this->amount = $request->getQueryParams()['amount'] ?? 0;
    $coins = $request->getQueryParams()['coins'] ?? '1,2,5,10,20,50,100,200';
    $this->coins = explode(',', $coins);   
    
    
    //Solution using dynamic programming
    dumpe($this->dp_change($this->amount, $this->coins));
    
    
    rsort($this->coins);

    foreach($this->coins as $coin)
    {
      $this->makeChildBranches([$coin]);
    }


    dump("solution: " . $this->solution);
    dumpe($this->desiredBranches);

  }

  function makeChildBranches(array $branch):void
  {
    $branchSum = array_sum($branch);
    if($branchSum == $this->amount){
      //$this->desiredBranches[] = $this->branchItems($branch);
      $this->solution++;
    }else{
      $smallestCoin = end($branch);
      foreach($this->coins as $coin){
        if($branchSum + $coin <= $this->amount && $coin <= $smallestCoin){
          $this->makeChildBranches([...$branch, $coin]);
        }
      }
    }
  }

  function branchItems(array $branch):string{
    return implode(',', $branch);
  }

  public function dp_change(int $amount, array $coins) {
    // Solution using dynamic programming
    $memo = array_fill(0, $amount + 1, 0);
    $memo[0] = 1;
    foreach ($coins as $coin) {
      for ($i = $coin; $i <= $amount; $i++) {
        $memo[$i] += $memo[$i - $coin];
      }
    }
    return $memo[$amount];
  }
}