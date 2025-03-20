<?php
include_once("storage.php");

class UserStorage extends Storage {
  public function __construct() {
    parent::__construct(new JsonIO('user.json'));
  }
  public function getContactsByEmail ($email)
  {
    return $this->findAll(["email" => $email]);
  }
}