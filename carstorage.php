<?php
include_once("storage.php");

class CarsStorage extends Storage {
  public function __construct() {
    parent::__construct(new JsonIO('cars.json'));
  }
  public function getContactsByEmail ($email)
  {
    return $this->findAll(["email" => $email]);
  }
}