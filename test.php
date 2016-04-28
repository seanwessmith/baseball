<?php
class Dog extends Animal {

  const CIRCLES_REQUIRED_TO_LIE_DOWN = 3;

  private $favoriteFood = 'dirt';

  public function getFavoriteFood() {
    return $this->favoriteFood;
  }
}
?>
