<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * tbl_menu - Uitbreiding om samengesteld menu's te maken, zie site/config/data/tbl_menu.php
 *
 * TODO:
 * - language split
 * - portfolio sites
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

Class Core_tbl_menu extends Data_Core {

  /**
   * Bewaar het menu
   */
  private $_menu                = array();

  private $title_field          = 'str_title';

  /**
   * Instellingen voor menu
   */
  private $_menu_caching        = TRUE;
  private $_menu_compact_caching= FALSE;
  private $_menu_config         = NULL;
  private $_menu_config_default = array(
                                    array(
                                      'type'  => 'table',
                                      'table' => 'tbl_menu',
                                    ),
                                  );


  public function __construct() {
    parent::__construct();
    $this->config->load('menu',true);

    $languages = $this->config->get_item('languages');
    if ( $languages and count($languages)>1 ) {
      $this->title_field = $this->title_field .= '_'.$languages[0];
    }

    $this->_menu_caching          = $this->config->get_item(array('menu','caching'));
    $this->_menu_compact_caching  = $this->config->get_item(array('menu','compact_caching'),false);
    $this->_compact_cache_fields  = array_merge( array('id','order','self_parent','uri','full_uri',$this->title_field,'full_title','str_module','_table','b_visible','b_restricted') ,$this->config->get_item(array('menu','compact_cache_extra_fields'),array()) );
    $this->_menu_config           = $this->config->get_item(array('menu','menu'));
    if (empty($this->_menu_config)) {
      $this->_menu_config = $this->_menu_config_default;
    }

  }


  /**
   * Geeft alle tabellen die gebruikt worden voor het menu
   *
   * @return     array  The menu tables.
   */
  public function get_menu_tables() {
    $tables = array();
    foreach ($this->_menu_config as $key => $item) {
      if (isset($item['table'])) array_unshift($tables,$item['table']);
    }
    return $tables;
  }


  /**
   * Maak het menu en geef dat als database array terug
   *
   * @return array
   * @author Jan den Besten
   */
  public function get_menu_result() {
    $menu_result = FALSE;
    if ($this->_menu_caching) {
      $cache_name = 'data_result_'.$this->settings['table'].'_menu_result';
      $menu_result = $this->get_cached_result( $cache_name );
    }
    if (!$menu_result) {
      $menu_result = $this->_create_menu_result();
      if ($this->_menu_caching) {
        if ( $this->_menu_compact_caching ) {
          foreach ($menu_result as $key => $item) {
            $menu_result[$key] = array_keep_keys($item,$this->_compact_cache_fields);
            $menu_result[$key]['_compact'] = true;
          }
        }
        $this->cache_result($menu_result,$cache_name);
      }
    }
    // trace_($this->title_field);
    // trace_($menu_result);
    // trace_( array_column($menu_result, 'full_title','full_uri') );
    // trace_( array_column($menu_result, 'uri','full_uri') );
    return $menu_result;
  }


  /**
   * Geeft submenu van (samengesteld) menu
   *
   * @param string $uri ['']
   * @return array
   * @author Jan den Besten
   */
  public function get_sub_items( $uri='') {
    $items = $this->get_menu_result();
    if (!empty($uri)) $items = filter_by_prefix( $items, $uri.'/' );
    return $items;
  }


  /**
   * Geeft één item uit (samengesteld) menu
   *
   * @param string $uri
   * @return array
   * @author Jan den Besten
   */
  public function get_menu_item( $uri ) {
    $items = $this->get_menu_result();
    $item = el($uri,$items,FALSE);
    if ( el('_compact',$item,false) and isset($item['_table']) ) {
      $complete = $this->data->table($item['_table'])->get_row($item['id']);
      $item = array_merge($complete,$item);
    }
    return $item;
  }

  /**
   * Geeft volgend item uit (samengesteld) menu
   *
   * @param string $uri
   * @return array
   * @author Jan den Besten
   */
  public function get_next_menu_item( $uri ) {
    $items = $this->get_menu_result();
    reset($items);
    $current = current($items);
    while( $current and  $current['full_uri']!==$uri) {
      $current = next($items);
    }
    if ($current) {
      $next = next($items);
      return $next;
    }
    return false;
  }


  /**
   * Geeft vorig item uit (samengesteld) menu
   *
   * @param string $uri
   * @return array
   * @author Jan den Besten
   */
  public function get_prev_menu_item( $uri ) {
    $items = $this->get_menu_result();
    reset($items);
    $current = current($items);
    while( $current and  $current['full_uri']!==$uri) {
      $current = next($items);
    }
    if ($current) {
      $prev = prev($items);
      return $prev;
    }
    return false;
  }




  /**
   * Geeft veld uit één item uit (samengesteld) menu
   *
   * @param string $uri
   * @return array
   * @author Jan den Besten
   */
  public function get_menu_field( $uri, $field ) {
    $item = $this->get_menu_item($uri);
    return el($field,$item,FALSE);
  }

  /**
   * Geeft eerste onderliggende item van een parent
   *
   * @param string $uri
   * @return array
   * @author Jan den Besten
   */
  public function get_first_child( $uri='', $visible = false ) {
    $items = $this->get_menu_result();
    if ($uri!='') $items = find_row_by_value($items,$uri.'/','full_uri',0);
    if ($items) {
      reset($items);
      if ($visible) {
        $child = current($items);
        while (!el('b_visible',$child,true)) {
          $child = next($items);
        }
      }
      else {
        $child = current($items);
      }
      return $child;
    }
    return false;
  }



  /**
   * Maak het menu aan de hand van de 'merged_menu' instellingen in site/config/tbl_menu
   *
   * Resultaat is een array met als key de 'full_uri'
   *
   * @return array
   * @author Jan den Besten
   */
  private function _create_menu_result() {
    if (!empty($this->_menu)) return $this->_menu;

    // Voeg menu samen
    foreach ($this->_menu_config as $menu_item) {
      switch ($menu_item['type']) {

        case 'item':
          $this->_add_menu_item($menu_item);
          break;

        case 'items':
          $this->_add_menu_items($menu_item);
          break;

        case 'table':
          $this->_add_menu_table($menu_item);
          break;

        case 'grouped':
          $this->_add_grouped_table($menu_item);
          break;


        case 'model':
          $this->_add_menu_from_model($menu_item);
          break;
      }
    }

    // - Reorder merged menu
    // - Zorg dat children b_visible en b_restricted overnemen (als nodig)
    // - Language split als nodig
    $order = 0;
    $max_order = count($this->_menu);

    $b_visible    = array();
    $b_restricted = array();

    // Voorbereiding voor languages
    $languages = $this->config->get_item(array('menu','languages'));
    if ($languages) {
      $language_fields = $this->config->get_item(array('menu','language_fields'));
      $extra_lang_menu = array();
      foreach ($languages as $lang_key => $lang) {
        $extra_lang_menu[$lang] = array();
        // Parent
        $extra_lang_menu[$lang][$lang] = array(
          'order'             => $lang_key * ($max_order + 1),
          'full_uri'          => $lang,
          'uri'               => $lang,
          '_lang'             => $lang,
          'str_title'         => $lang,
        );
      }
    }

    foreach ($this->_menu as $key => $item) {

      // Visible & Restricted
      $item['b_visible']    = el('b_visible',$item,true);
      $item['b_restricted'] = el('b_restricted',$item,false);

      // Test if parent is visible/restricted
      $uri      = $item['uri'];
      $full_uri = $item['full_uri'];
      if ( $full_uri!=$item['uri'] ) {
        $parent_uri = remove_suffix($item['full_uri'],'/');
        if ($parent_uri!='') {
          if (!el($parent_uri,$b_visible,true))     $item['b_visible'] = false;
          if (el($parent_uri,$b_restricted,false))  $item['b_restricted'] = true;
        }
      }
      $b_visible[$full_uri] = $item['b_visible'];
      $b_restricted[$full_uri] = $item['b_restricted'];

      // Add Order
      $item['order'] = $order++;

      // More languages?
      if ($languages) {
        // $this->load->model('create_uri');
        $base_lang = current($languages);
        // lang_fields in normale item
        foreach ($language_fields as $lang_field) {
          if (isset($item[$lang_field.'_'.$lang])) {
            $item[$lang_field] = $item[$lang_field.'_'.$lang];
            if (empty($item[$lang_field])) $item[$lang_field] = $item[$lang_field.'_'.$base_lang];
          }
        }
        // Extra items
        foreach ($languages as $lang_key => $lang) {
          $lang_item = $item;
          $full_uri = $lang.'/'.el('full_uri',$lang_item,$lang_item['uri']);
          $lang_item['full_uri'] = $full_uri;
          $lang_item['_lang'] = $lang;
          $lang_item['order'] += $lang_key * ($max_order + 1);
          // (full_)uri
          // if ($lang !== $base_lang) {
          //   if (isset($lang_item['_table'])) {
          //     $this->create_uri->set_table($lang_item['_table']);
          //     $original_uri = $lang_item['uri'];
          //     $lang_uri = $this->create_uri->create($lang_item,$lang);
          //     $lang_item['uri'] = $lang_uri;
          //     $full_uri = preg_replace('/\b'.$original_uri.'\b/uU', $lang_uri, $full_uri);
          //     $lang_item['full_uri'] = $full_uri;
          //   }
          // }
          foreach ($language_fields as $lang_field) {
            // multi lang fields
            if (isset($lang_item[$lang_field.'_'.$lang])) {
              $lang_item[$lang_field] = $lang_item[$lang_field.'_'.$lang];
              if (empty($lang_item[$lang_field])) $lang_item[$lang_field] = $lang_item[$lang_field.'_'.$base_lang];
            }
          }
          // Add lang item
          // trace_($lang_item);
          $extra_lang_menu[$lang][$full_uri] = $lang_item;
        }
      }

      // Add (normal) item
      $this->_menu[$key] = $item;

    }

    // Merge language menus
    if ($languages) {
      $this->_menu = array();
      foreach ($extra_lang_menu as $lang => $lang_menu) {
        $this->_menu = array_merge($this->_menu,$lang_menu);
      }
    }

    // Add '_level'
    foreach ($this->_menu as $key => $item) {
      $this->_menu[$key]['_level'] = substr_count($item['full_uri'],'/');
    }

    return $this->_menu;
  }

  /**
   * Op welke plaats komt het (sub)menu(item)?
   *
   * @param string $item
   * @return array
   * @author Jan den Besten
   */
  private function _determine_menu_item_places($item) {
    $place = el('place',$item,FALSE);

    // Standaard aan het einde
    if (empty($place) or $place===FALSE) {
      $keys = array_keys($this->_menu);
      end($keys);
      $found_uris = current($keys);
    }
    else {

      // place = [field => value]
      if (is_array($place)) {
        $place_field = key($place);
        $place_value = $place[$place_field];
        $key = find_row_by_value($this->_menu,$place_value,$place_field);
        $found_uris = array_keys($key);
      }

      // place =  uri
      else {
        $keys = filter_by_key( $this->_menu, $place );
        end($keys);
        $found_uris = key($keys);
      }

    }


    if (!is_array($found_uris)) $found_uris = array($found_uris);


    $result = array();
    foreach ($found_uris as $key => $found_uri) {
      $pre_uri   = '';
      $pre_title = '';
      if (isset($this->_menu[$found_uri]) and $place) {
        $pre_uri   = el('full_uri',$this->_menu[$found_uri], el('uri',$this->_menu[$found_uri],'') );
        $pre_title = el('full_title',$this->_menu[$found_uri], el($this->title_field,$this->_menu[$found_uri],'') );
      }
      $result[] = array(
        'key'       => $found_uri,
        'pre_uri'   => $pre_uri,
        'pre_title' => $pre_title,
      );
    }

    return $result;
  }


  /**
   * Voeg één item toe aan menu
   *
   * @param array $item
   * @return void
   * @author Jan den Besten
   */
  private function _add_menu_item($item,$places=FALSE) {
    if (!$places) $places = $this->_determine_menu_item_places($item);
    if (!is_array($places)) $places = array($places);

    foreach ($places as $place) {
      if (!isset($item['full_title'])) $item['full_title'] = $item[$this->title_field];
      if ($place['pre_uri']) {
        $item['full_uri'] = $place['pre_uri'].'/'.$item['uri'];
        $menu_item = array( $item['full_uri'] => $item );
      }
      else {
        $menu_item = array( $item['uri'] => $item );
      }

      if ($place['key'])
        $this->_menu = array_add_after( $this->_menu, $place['key'], $menu_item );
      else
        $this->_menu = $menu_item;
    }
  }


  /**
   * Voeg meerdere items toe aan menu
   *
   * @param array $item
   * @return void
   * @author Jan den Besten
   */
  private function _add_menu_items($item) {
    $places = $this->_determine_menu_item_places($item);
    $nr = 1;
    foreach ($item['items'] as $sub_item) {
      if (isset($item['visible_limit']) and $nr>$item['visible_limit']) $sub_item['b_visible'] = FALSE;
      $this->_add_menu_item($sub_item,$places);
      $nr++;
    }
  }


  /**
   * Voeg hele tabel toe aan het menu
   *
   * @param array $item
   * @return void
   * @author Jan den Besten
   */
  private function _add_menu_table($item) {
    $table = $item['table'];

    $this->data->table($table);
    // Set result_key to full_uri
    $result_key = $this->data->get_setting('result_key');
    if ($this->data->field_exists('self_parent')) {
      $this->data->tree('full_uri','uri');
      $this->data->tree('full_title',$this->title_field);
      $this->data->set_result_key('full_uri');
    }
    else {
      $this->data->set_result_key('uri');
    }

    // get data
    if (isset($item['where']))    $this->data->where($item['where']);
    if (isset($item['order_by'])) $this->data->order_by($item['order_by']);
    $data_items = $this->data->get_result( el('limit',$item,NULL), el('offset',$item,0) );

    foreach ($data_items as $key => $row) {
      $data_items[$key]['_table'] = $table;

      if (isset($item['item'])) {
        $data_items[$key] = array_merge($data_items[$key],$item['item']);
      }

      if (isset($item['inherit'])) {
        foreach($item['inherit'] as $inherit) {
          if ( !isset($row[$inherit]) or empty($row[$inherit]) ) {
            $parent_uri = remove_suffix($row['full_uri'],'/');
            if (isset($data_items[$parent_uri])) {
              $data_items[$key][$inherit] = $data_items[$parent_uri][$inherit];
            }
          }
        }
      }
    }

    // restore
    $this->data->set_result_key($result_key);
    $this->data->table('tbl_menu');

    // Add
    $places = $this->_determine_menu_item_places($item);
    foreach ($places as $place) {
      $items = $data_items;
      $nr = 1;
      if ($place['pre_uri']) {
        foreach ($items as $key => $row) {
          unset($items[$key]);
          if (isset($item['item'])) $row = array_merge($row,$item['item']);
          $full_uri         = $place['pre_uri'].'/'.el('full_uri',$row, el('uri',$row));
          $row['full_uri']  = $full_uri;
          if (isset($item['visible_limit']) and $nr>$item['visible_limit']) $row['b_visible'] = FALSE;
          if (isset($place['pre_title'])) $row['full_title'] = $place['pre_title'].' / '.el('full_title',$row, el($this->title_field,$row));
          $items[$full_uri] = $row;
          $nr++;
        }
      }

      if ($place['key']) {
        $this->_menu = array_add_after( $this->_menu, $place['key'], $items );
      }
      else
        $this->_menu = $items;
    }
  }

/**
   * Voeg tabel gegroupeerd als sub-pagina's toe aan het menu
   *
   * @param array $item
   * @return void
   * @author Jan den Besten
   */
  private function _add_grouped_table($item) {
    $table = $item['table'];

    $this->data->table($table);

    // Set result_key to full_uri
    $result_key = $this->data->get_setting('result_key');
    if ($this->data->field_exists('self_parent')) {
      $this->data->tree('full_uri','uri');
      $this->data->tree('full_title',$this->title_field);
      $this->data->set_result_key('full_uri');
    }
    else {
      $this->data->set_result_key('uri');
    }

    // Add Grouped data
    foreach ($this->_menu as $menu_item) {
      $place = false;
      foreach ($item['place'] as $key => $value) {
        if ($menu_item[$key]==$value) $place = el('full_uri',$menu_item,el('uri',$menu_item));
      }
      if ($place!==false) {
        foreach ($item['grouped_by'] as $key => $field) {
          $this->data->where($field,$menu_item[$key]);
        }
        if (isset($item['where']))    $this->data->where($item['where']);
        if (isset($item['order_by'])) $this->data->order_by($item['order_by']);
        $data_items = $this->data->get_result( el('limit',$item,NULL), el('offset',$item,0) );

        // Add
        $nr=1;
        $items = array();
        foreach ($data_items as $key => $row) {
          if (isset($item['item'])) $row = array_merge($row,$item['item']);
          $full_uri         = $place.'/'.el('full_uri',$row, el('uri',$row));
          $row['full_uri']  = $full_uri;
          $row['full_title']= $menu_item['full_title'].'/'.el('full_title',$row, el($this->title_field,$row));
          $row['_table']    = $table;
          $row['self_parent'] = $menu_item['id'];
          if (isset($item['visible_limit']) and $nr>$item['visible_limit']) $row['b_visible'] = FALSE;

          if (isset($item['inherit'])) {
            foreach($item['inherit'] as $inherit) {
              if ( !isset($row[$inherit]) or empty($row[$inherit]) ) {
                $parent_uri = remove_suffix($row['full_uri'],'/');
                if (isset($this->_menu[$parent_uri])) {
                  $row[$inherit] = $this->_menu[$parent_uri][$inherit];
                }
              }
            }
          }

          $items[$full_uri] = $row;
          $nr++;
        }
        $this->_menu      = array_add_after( $this->_menu, $place, $items );
      }
    }

    // restore
    $this->data->set_result_key($result_key);
    $this->data->table('tbl_menu');
  }


/**
   * Voeg menu items toe dmv extern model
   *
   * @param array $item
   * @return void
   * @author Jan den Besten
   */
  private function _add_menu_from_model($item) {
    $method = get_suffix($item['model'],'.');
    $model = get_prefix($item['model'],'.');
    $this->load->model($model,$model);

    // Places
    $this->data->table('tbl_menu');
    $places = $this->_determine_menu_item_places($item);

    // Get and Add items
    foreach ($places as $place) {
      $nr=1;
      $items = $this->$model->$method($item,$place);
      if ($place['pre_uri']) {
        foreach ($items as $key => $row) {
          unset($items[$key]);
          if (isset($item['item'])) $row = array_merge($row,$item['item']);
          $full_uri         = $place['pre_uri'].'/'.el('full_uri',$row, el('uri',$row));
          $row['full_uri']  = $full_uri;
          if (isset($place['pre_title'])) $row['full_title'] = $place['pre_title'].' / '.el('full_title',$row, el($this->title_field,$row));;
          if (isset($item['visible_limit']) and $nr>$item['visible_limit']) $row['b_visible'] = FALSE;
          $items[$full_uri] = $row;
          $nr++;
        }
      }
      $this->data->table('tbl_menu');

      if ($place['key']) {
        $this->_menu = array_add_after( $this->_menu, $place['key'], $items );
      }
      else
        $this->_menu = $items;
    }
  }



}
