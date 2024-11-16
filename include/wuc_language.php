<?php
/**
 * "Website Under Construction" PHP script from HTMLPIE.COM :)
 * © HTMLPIE.COM . All rights reserved.
 *
 * @file
 * UI language.
 *
 * @version 2.1
 *
 */

  /**
   * Scans the /HPWUC/language folder.
   * @param  string $type Output type.
   * @return srray        A nested array of the available languages.
   */
  function wuc_language_scan($type = 'list') {
    // Creating a list of available language files.
    $language_files = $language_list = array();
    $language_directory = dirname(__FILE__) .'/../language/';
    if (!file_exists($language_directory)) {
      echo 'Caution: Cannot find the language folder (/HPWUC/language).';
      die();
    } elseif ($handle = opendir($language_directory)) {
      while (($file = readdir($handle)) !== false) {
        $ext = strtolower(substr($file, strrpos($file, '.') + 1));
        if ($file != "." && $file != ".." && $ext == 'php') {
          if (strpos($file, 'wuc_language') !== FALSE){
            $language_name = '';
            if ($f = fopen($language_directory . $file, 'r')) {
              while ($line = fgets($f)) {
                if (preg_match('@name\s+([^\.-]+)@', $line, $matches)) {
                  $language_name = $matches[1];
                  break;
                } else {
                  $language_name = 'N\A';
                }
              }
              fclose($f);
            }
            $language_code = explode('.', $file);
            $language_code = $language_code[1];
            $language_list[] = $language_code;
            $language_files[] = array(
              'code' => $language_code,
              'name' => $language_name,
            );
          }
        }
      }
      if (empty($language_list)) {
        echo 'Caution: Language folder (/HPWUC/language) does not contain any proper language file.';
        die();
      }
      closedir($handle);
    }
    if ($type == 'list') {
      $output = $language_list;
    } else {
      $output = $language_files;
    }
    return $output;
  }

  /**
   * Generates a HTML list of available languages.
   * @param  string  $type How the language should be named.
   * @return string        HTML output.
   */
  function wuc_language_list($type = 'name') {
    // Creating the language menu;
    $output = array();
    $language_files = wuc_language_scan('files');
    if (WUC_C_LANGUAGE_MENU === TRUE && count($language_files) > 1) {
      $output[] = '<h3 class="element_hidden">'. WUC_L_CHOOSE_LANGUAGE .'</h3>';
      $output[] = '<ul class="language_menu clearfix">';
      asort($language_files);
      $language = wuc_language();
      foreach ($language_files as $item) {
        $output[] = '<li'. (($language == $item['code']) ? ' class="active"' : '') .'><a href="'. $_SERVER['PHP_SELF'] . (($item['code'] != 'inc') ? '?l='. $item['code'] : '' ) .'" title="'. $item['name'] .'">'. (($type == 'name') ? $item['name'] : $item['code']) .'</a></li>';
      }
      $output[] = '</ul>';

      return implode("\n", $output);
    }
  }

  /**
   * Sets the active UI language.
   * @return string The language code.
   */
  function wuc_language() {
    // Setting a default language.
    $language = 'en';
    $language_list = wuc_language_scan('list');
    if (isset($_GET['l']) && in_array($_GET['l'], $language_list)) {
      $language = $_GET['l'];
    }
    elseif (isset($_POST['l']) && in_array($_POST['l'], $language_list)) {
      $language = $_POST['l'];
    }
    elseif (isset($_SESSION['wuc_language']) && in_array($_SESSION['wuc_language'], $language_list)) {
      $language = $_SESSION['wuc_language'];
    }
    elseif (in_array(trim(WUC_C_LANGUAGE_DEFAULT), $language_list)) {
      $language = trim(WUC_C_LANGUAGE_DEFAULT);
    }
    elseif (WUC_C_LANGUAGE_DETECT === TRUE && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
      $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
      foreach ($langs as $value){
        $l = substr($value, 0, 2);
        if (in_array($l, $language_list)){
          $language = $l;
        }
      }
    }
    $_SESSION['wuc_language'] = $language;
    return $language;
  }

  // Adding the language file.
  require_once(dirname(__FILE__) .'/../language/wuc_language.'. wuc_language() .'.php');