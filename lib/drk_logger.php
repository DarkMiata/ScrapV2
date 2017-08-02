<?php

require_once PATH_CLASS . "drk_loggerMsg.php";

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of drk_logger
 *
 * @author sam
 */
class drk_logger
  {
  private $file;
  private $type;
  private $timestamp;
  private $mess;
  // ========================================

  public function get_file() {
    return $this->file;
  }

  private function get_type() {
    return $this->type;
  }

  private function get_timestamp() {
    return $this->timestamp;
  }

  private function get_mess() {
    return $this->mess;
  }
  // ========================================

  private function set_file($file) {
    $this->file = $file;
  }

  private function set_type($type) {
    $this->type = $type;
  }

  private function set_timestamp($timestamp) {
    $this->timestamp = $timestamp;
  }

  private function set_mess($mess) {
    $this->mess = $mess;
  }
    // ========================================

  public function __construct($file) {
    $this->file = $file;
  }

  // ========================================
  // ========================================


  // ========================================
  }
