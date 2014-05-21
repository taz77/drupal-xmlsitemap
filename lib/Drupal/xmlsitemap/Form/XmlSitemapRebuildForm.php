<?php

/**
 * @file
 * Contains \Drupal\xmlsitemap\Form\XmlSitemapRebuildForm.
 */

namespace Drupal\xmlsitemap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Component\Utility\MapArray;
use Drupal\Component\Utility\UrlHelper;

/**
 * Configure xmlsitemap settings for this site.
 */
class XmlSitemapRebuildForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'xmlsitemap_admin_rebuild';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    if (!$_POST && !\Drupal::config('xmlsitemap.settings')->get('rebuild_needed')) {
      if (!\Drupal::config('xmlsitemap.settings')->get('regenerate_needed')) {
        drupal_set_message(t('Your sitemap is up to date and does not need to be rebuilt.'), 'error');
      }
      else {
        $_REQUEST += array('destination' => 'admin/config/search/xmlsitemap');
        drupal_set_message(t('A rebuild is not necessary. If you are just wanting to regenerate the XML sitemap files, you can <a href="@link-cron">run cron manually</a>.', array('@link-cron' => url('admin/reports/status/run-cron', array('query' => drupal_get_destination())))), 'warning');
      }
    }

    // Build a list of rebuildable link types.
    module_load_include('generate.inc', 'xmlsitemap');
    //$rebuild_types = xmlsitemap_get_rebuildable_link_types();
    $rebuild_types = array_combine(array(), array());
    $form['entities'] = array(
      '#type' => 'select',
      '#title' => t("Select which link types you would like to rebuild"),
      '#description' => t('If no link types are selected, the sitemap files will just be regenerated.'),
      '#multiple' => TRUE,
      '#options' => $rebuild_types,
      '#default_value' => \Drupal::config('xmlsitemap.settings')->get('rebuild_needed') || !\Drupal::config('xmlsitemap.settings')->get('developer_mode') ? $rebuild_types : array(),
      '#access' => \Drupal::config('xmlsitemap.settings')->get('developer_mode'),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
    // Check that the chunk size will not create more than 1000 chunks.

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    // Save any changes to the frontpage link.
    xmlsitemap_link_save(array('type' => 'frontpage', 'id' => 0, 'loc' => ''));

    parent::submitForm($form, $form_state);
  }

}
