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
    $name = [];
    $raspil = [];
    $raspil2 = [];
    $raspil3 = [];
    $raspil4 = [];
    $raspil5 = [];
    $raspil6 = [];
    $result = [];
    $result2 = [];
    $result3 = [];
    $result4 = [];
    $result5 = [];
    $result6 = [];
    $cost_smena_sum = 0;
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

      $field_stanok = $node->field_stanok;
      $termin = $field_stanok->entity;

      if (is_object($termin)) {
        $field_name = $termin->name;
        $stanok = $field_name->value;
      }
      else {
        $stanok = "(станок не указан)";
      }

      foreach ($vyhod_pilom as $key => $array) {
        $vyhod_pilom[$key]['станок'] = $stanok;
        $poroda = $array['порода'];
        $pilomat = $array['пиломатериал'];
        $sort = $array['сорт'];
        $vysota = $array['высота'];
        $shirina = $array['ширина'];
        $dlina = $array['длина'];
        $raspil[$poroda][$pilomat]['сорт' . $sort][] = $array['кубатура'];
        $raspil2[$poroda][$pilomat]['сорт' . $sort][] = $array['количество'];
        $raspil3[$poroda][$pilomat]['сорт' . $sort][$vysota][$shirina][$dlina][] = $array['количество'];
        $raspil4[$poroda][$pilomat]['сорт' . $sort][$vysota][$shirina][$dlina][] = $array['кубатура'];
        $raspil5[$stanok]['сорт' . $sort][] = $array['кубатура'];
        $raspil6[$stanok]['сорт' . $sort][] = $array['количество'];
      }
    }
    foreach ($raspil as $por => $bez_porodi) {
      foreach ($bez_porodi as $pil => $bez_piloma) {
        foreach ($bez_piloma as $sor => $bez_sorta) {
          $summa_kub = 0;
          foreach ($bez_sorta as $key => $value) {
            $summa_kub = $summa_kub + $value;
          }
          $result[$por][$pil][$sor] = number_format($summa_kub, 3, ".", "");
        }
      }
    }
    foreach ($raspil2 as $por => $bez_porodi) {
      foreach ($bez_porodi as $pil => $bez_piloma) {
        foreach ($bez_piloma as $sor => $bez_sorta) {
          $summa_shtuk = 0;
          foreach ($bez_sorta as $key => $value) {
            $summa_shtuk = $summa_shtuk + $value;
          }
          $result2[$por][$pil][$sor] = number_format($summa_shtuk, 0, ",", " ");
        }
      }
    }
    foreach ($raspil3 as $por => $bez_porodi) {
      foreach ($bez_porodi as $pil => $bez_piloma) {
        foreach ($bez_piloma as $sor => $bez_sorta) {
          foreach ($bez_sorta as $dlin => $bez_dliny) {
            foreach ($bez_dliny as $shir => $bez_shirin) {
              foreach ($bez_shirin as $visot => $bez_visoty) {
                $summa_shtuk_2 = 0;
                foreach ($bez_visoty as $key => $value) {
                  $summa_shtuk_2 = $summa_shtuk_2 + $value;
                }
                $dlin = number_format($dlin, 0, ",", "");
                $shir = number_format($shir, 0, ",", "");
                $visot = number_format($visot, 0, ",", "");
                $result3[$por][$pil][$sor]["{$dlin}x{$shir}x{$visot}"] = number_format($summa_shtuk_2, 0, ",", " ");
              }
            }
          }
        }
      }
    }
    foreach ($raspil4 as $por => $bez_porodi) {
      foreach ($bez_porodi as $pil => $bez_piloma) {
        foreach ($bez_piloma as $sor => $bez_sorta) {
          foreach ($bez_sorta as $dlin => $bez_dliny) {
            foreach ($bez_dliny as $shir => $bez_shirin) {
              foreach ($bez_shirin as $visot => $bez_visoty) {
                $summa_kub_2 = 0;
                foreach ($bez_visoty as $key => $value) {
                  $summa_kub_2 = $summa_kub_2 + $value;
                }
                $dlin = number_format($dlin, 0, ",", "");
                $shir = number_format($shir, 0, ",", "");
                $visot = number_format($visot, 0, ",", "");
                $result4[$por][$pil][$sor]["{$dlin}x{$shir}x{$visot}"] = number_format($summa_kub_2, 3, ".", "");
              }
            }
          }
        }
      }
    }
    foreach ($raspil5 as $stan => $bez_stanka) {
      foreach ($bez_stanka as $sor => $bez_sorta) {
        $summa_kub_3 = 0;
        foreach ($bez_sorta as $key => $value) {
          $summa_kub_3 = $summa_kub_3 + $value;
        }
        $result5[$stan][$sor] = number_format($summa_kub_3, 3, ".", "");
      }
    }
    foreach ($raspil6 as $stan => $bez_stanka) {
      foreach ($bez_stanka as $sor => $bez_sorta) {
        $summa_shtuk_3 = 0;
        foreach ($bez_sorta as $key => $value) {
          $summa_shtuk_3 = $summa_shtuk_3 + $value;
        }
        $result6[$stan][$sor] = number_format($summa_shtuk_3, 0, ".", " ");
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
          $nachisleno = $node_oplata->field_oplata_nachisleno->value;
          $shtraf = $node_oplata->field_oplata_shtraf->value;
          $rashod = $node_oplata->field_oplata_rashod->value;
          $komment = $node_oplata->field_oplata_komment->value;

          if (!isset($team[$tid]['zarplata_itogo'])) {
            $team[$tid]['zarplata'][] = $zarplata;
            $team[$tid]['zarplata_itogo'] = 0;
          }
          if (!isset($team[$tid]['nachisleno_itogo'])) {
            $team[$tid]['nachisleno'][] = $nachisleno;
            $team[$tid]['nachisleno_itogo'] = 0;
          }
          if (!isset($team[$tid]['shtraf_itogo'])) {
            $team[$tid]['shtraf'][] = $shtraf;
            $team[$tid]['shtraf_itogo'] = 0;
          }
          if (!isset($team[$tid]['rashod_itogo'])) {
            $team[$tid]['rashod'][] = $rashod;
            $team[$tid]['rashod_itogo'] = 0;
          }
          $team[$tid]['zarplata_itogo'] = $team[$tid]['zarplata_itogo'] + $zarplata;
          $team[$tid]['zarplata_human'] = number_format($team[$tid]['zarplata_itogo'], 0, ",", " ");
          $team[$tid]['nachisleno_itogo'] = $team[$tid]['nachisleno_itogo'] + $nachisleno;
          $team[$tid]['nachisleno_human'] = number_format($team[$tid]['nachisleno_itogo'], 0, ",", " ");
          $team[$tid]['shtraf_itogo'] = $team[$tid]['shtraf_itogo'] + $shtraf;
          $team[$tid]['shtraf_human'] = number_format($team[$tid]['shtraf_itogo'], 0, ",", " ");
          $team[$tid]['rashod_itogo'] = $team[$tid]['rashod_itogo'] + $rashod;
          $team[$tid]['rashod_human'] = number_format($team[$tid]['rashod_itogo'], 0, ",", " ");
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
    ksort($result5);
    ksort($result6);
    $data = [
      'team' => $team,
      'zarpata_vsego' => number_format($zarpata_vsego, 0, ",", " "),
      'cost_smena_sum' => number_format($cost_smena_sum, 0, ",", " "),
      'stanok' => $stanok,
      'result' => $result,
      'result2' => $result2,
      'result3' => $result3,
      'result4' => $result4,
      'result5' => $result5,
      'result6' => $result6,
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
        $kolvo = $node_vyhod_pilom->field_pilom_kolvo->value;
        $pilom = $node_vyhod_pilom->field_pilom->entity->name->value;
        $visota = $node_vyhod_pilom->field_pilom_vysota->value;
        $shirina = $node_vyhod_pilom->field_pilom_shirina->value;
        $dlina = $node_vyhod_pilom->field_pilom_dlina->value;
        $vyhod_pilom[] = [
          'порода' => $poroda,
          'сорт' => $sort,
          'кубатура' => $kubat,
          'количество' => $kolvo,
          'пиломатериал' => $pilom,
          'высота' => $visota,
          'ширина' => $shirina,
          'длина' => $dlina,
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
    $query->condition('field_smena_date', $start_norm, '>=');
    $query->condition('field_smena_date', $end_norm, '<=');
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
