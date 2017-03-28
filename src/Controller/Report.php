<?php

namespace Drupal\report\Controller;

/**
 * @file
 * Contains \Drupal\node_orders\Controller\Page.
 */
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller routines for page example routes.
 */
class Report extends ControllerBase {

  /**
   * Page Callback.
   */
  public static function report() {
    $start = strtotime("01-01-2017");
    $end = time();
    return [
      'form' => \Drupal::formBuilder()->getForm('Drupal\report\Form\DateChoice'),
    ];
  }

}
