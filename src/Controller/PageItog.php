<?php

namespace Drupal\report\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Controller routines for page example routes.
 */
class PageItog extends ControllerBase {

  /**
   * A more complex _controller callback that takes arguments.
   */
  public function report($start, $end) {
    $smena = $this->getSmena($start, $end);
    $source = [];
    $nids = FALSE;
    $cost_smena_sum = 0;
    $kub_sum_sosna_1 = 0;
    $kub_sum_sosna_2 = 0;
    $kub_sum_sosna_3 = 0;
    $kub_sum_el_1 = 0;
    $kub_sum_el_2 = 0;
    $kub_sum_el_3 = 0;
    foreach ($smena as $key => $node) {
      // Делаем из поля "ссылка на команду" массив людей.
      $team = $this->getSmenaTeam($node->field_smena_ref_team);
      $vyhod_pilom = $this->getVyhodPilom($node->field_smena_ref_vyhod_pilom);
      $source = [
        'название смены' => $node->title->value,
        'работники' => $team,
      ];
      $id = $node->id();
      if (!empty($smena)) {
        $nids = array_keys($smena);
      }
      $cost_smena = $node->field_smena_cost_sum->value;
      $cost_smena_sum = $cost_smena_sum + $cost_smena;

      foreach ($vyhod_pilom as $key => $array) {
        $kubat = $array['кубатура'];
        if ($array['порода'] == "Сосна") {
          if ($array['сорт'] == "1") {
            $kub_sum_sosna_1 = $kub_sum_sosna_1 + $kubat;
          }
          if ($array['сорт'] == "2") {
            $kub_sum_sosna_2 = $kub_sum_sosna_2 + $kubat;
          }
          if ($array['сорт'] == "3") {
            $kub_sum_sosna_3 = $kub_sum_sosna_3 + $kubat;
          }
        }
        if ($array['порода'] == "Ель") {
          if ($array['сорт'] == "1") {
            $kub_sum_el_1 = $kub_sum_el_1 + $kubat;
          }
          if ($array['сорт'] == "2") {
            $kub_sum_el_2 = $kub_sum_el_2 + $kubat;
          }
          if ($array['сорт'] == "3") {
            $kub_sum_el_3 = $kub_sum_el_3 + $kubat;
          }
        }
      }
    }

    $teams = $this->getTeam();
    $team = [];
    foreach ($teams as $key => $node_team) {
      $id = $node_team->id();
      $team[$id] = [
        'title' => $node_team->title->value,
      ];
    }
    $exhour = $this->getExhour($nids);
    $zarpata_vsego = 0;
    if ($exhour && !empty($exhour)) {
      foreach ($exhour as $key => $node_oplata) {
        $human = $node_oplata->field_oplata_ref_team->entity;
        if ($human) {
          $tid = $human->id();
          $zarplata = $node_oplata->field_oplata->value;
          if (!isset($team[$tid]['zarplata_itogo'])) {
            $team[$tid]['zarplata'][] = $zarplata;
            $team[$tid]['zarplata_itogo'] = 0;
          }
          $team[$tid]['zarplata_itogo'] = $team[$tid]['zarplata_itogo'] + $zarplata;
          $team[$tid]['zarplata_human'] = number_format($team[$tid]['zarplata_itogo'], 0, ",", " ");
          $zarpata_vsego = $zarpata_vsego + $zarplata;
        }
      }
      foreach ($team as $key => $value) {
        if (!isset($value['zarplata_itogo'])) {
          unset($team[$key]);
        }
      }
    }

    // А вот и сам массив, данные из которого мы выводим на странице.
    $renderable = [];
    $renderable['info'] = [
      '#markup' => "Отчет с " . format_date(strtotime($start), 'custom', 'd-m-Y')
      . " по " . format_date(strtotime($end), 'custom', 'd-m-Y'),
    ];
    $data = [
      'team' => $team,
      'zarpata_vsego' => number_format($zarpata_vsego, 0, ",", " "),
      'cost_smena_sum' => number_format($cost_smena_sum, 0, ",", " "),
      'kub_sum_sosna_1' => number_format($kub_sum_sosna_1, 3, ".", " "),
      'kub_sum_sosna_2' => number_format($kub_sum_sosna_2, 3, ".", " "),
      'kub_sum_sosna_3' => number_format($kub_sum_sosna_3, 3, ".", " "),
      'kub_sum_el_1' => number_format($kub_sum_el_1, 3, ".", " "),
      'kub_sum_el_2' => number_format($kub_sum_el_2, 3, ".", " "),
      'kub_sum_el_3' => number_format($kub_sum_el_3, 3, ".", " "),
    ];
    $renderable['h'] = [
      '#theme' => 'report-header',
      '#data' => $data,
    ];
    return $renderable;
  }

  /**
   * Делаем из поля "ссылка на команду" массив людей.
   */
  public function getSmenaTeam($field_smena_ref_team) {
    $team = [];
    foreach ($field_smena_ref_team as $key => $man) {
      $node_team = $man->entity;
      if ($node_team) {
        $name = $node_team->field_team_name->value;
        $team[] = [
          'ФИО работника' => $name,
        ];
      }

    }
    return $team;
  }

  /**
   * Делаем из поля "ссылка на выход пилом" массив с инфой по пиломатериалам.
   */
  public function getVyhodPilom($field_smena_ref_vyhod_pilom) {
    $vyhod_pilom = [];
    foreach ($field_smena_ref_vyhod_pilom as $key => $value) {
      $node_vyhod_pilom = $value->entity;
      if ($node_vyhod_pilom) {
        $poroda = $node_vyhod_pilom->field_poroda->entity->name->value;
        $sort = $node_vyhod_pilom->field_sort->entity->name->value;
        $kubat = $node_vyhod_pilom->field_pilom_kubatura->value;
        $vyhod_pilom[] = [
          'порода' => $poroda,
          'сорт' => $sort,
          'кубатура' => $kubat,
        ];
      }
    }
    return $vyhod_pilom;
  }

  /**
   * Функциями getХххх() формируем из ноды объекты.
   */
  public function getSmena($start, $end) {
    $start = strtotime($start);
    $end = strtotime($end);
    $start_norm = format_date($start, 'custom', "Y-m-d");
    $end_norm = format_date($end, 'custom', "Y-m-d");
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'smena');
    $query->condition('field_smena_date', $start_norm, '>');
    $query->condition('field_smena_date', $end_norm, '<');
    $entity_ids = $query->execute();
    $smena = Node::loadMultiple($entity_ids);
    return $smena;
  }

  /**
   * A getOgders.
   */
  public function getExhour($nids = []) {
    $exhour = FALSE;
    if (!empty($nids)) {
      $query = \Drupal::entityQuery('node');
      $query->condition('status', 1);
      $query->condition('type', 'oplata');
      $query->condition('field_oplata_ref_smena', $nids, 'IN');
      $entity_ids = $query->execute();
      $exhour = Node::loadMultiple($entity_ids);
    }
    return $exhour;
  }

  /**
   * A getOgders.
   */
  public function getTeam() {
    $query = \Drupal::entityQuery('node');
    $query->condition('status', 1);
    $query->condition('type', 'team');
    $entity_ids = $query->execute();
    $teams = Node::loadMultiple($entity_ids);
    return $teams;
  }

}
