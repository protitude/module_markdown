<?php

/**
 * @file
 * Contains \Drupal\markdown\Plugin\Filter\Markdown.
 */

namespace Drupal\markdown\Plugin\Filter;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter for markdown.
 *
 * @Filter(
 *   id = "markdown",
 *   module = "markdown",
 *   title = @Translation("Markdown"),
 *   description = @Translation("Allows content to be submitted using Markdown, a simple plain-text syntax that is filtered into valid XHTML."),
 *   type = FILTER_TYPE_TRANSFORM_REVERSIBLE
 * )
 */
class Markdown extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a Markdown instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, array &$form_state) {
    $this->moduleHandler->loadInclude('markdown', 'php', 'markdown');

    $settings['markdown_wrapper'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Markdown'),
    );
    $links = array(
      'Markdown PHP Version: <a href="http://michelf.com/projects/php-markdown/">' . MARKDOWN_VERSION . '</a>',
      'Markdown Extra Version: <a href="http://michelf.com/projects/php-markdown/">' . MARKDOWNEXTRA_VERSION . '</a>',
    );
    $settings['markdown_wrapper']['markdown_status'] = array(
      '#title' => $this->t('Versions'),
      '#type' => 'item',
      '#markup' => theme('item_list', array('items' => $links)),
    );

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode, $cache, $cache_id) {
    if (!empty($text)) {
      $this->moduleHandler->loadInclude('markdown', 'php', 'markdown');
      $text = Markdown($text);
    }

    return $text;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      return $this->t('Quick Tips:<ul>
      <li>Two or more spaces at a line\'s end = Line break</li>
      <li>Double returns = Paragraph</li>
      <li>*Single asterisks* or _single underscores_ = <em>Emphasis</em></li>
      <li>**Double** or __double__ = <strong>Strong</strong></li>
      <li>This is [a link](http://the.link.example.com "The optional title text")</li>
      </ul>For complete details on the Markdown syntax, see the <a href="http://daringfireball.net/projects/markdown/syntax">Markdown documentation</a> and <a href="http://michelf.com/projects/php-markdown/extra/">Markdown Extra documentation</a> for tables, footnotes, and more.');
    }
    else {
      return $this->t('You can use <a href="@filter_tips">Markdown syntax</a> to format and style the text. Also see <a href="@markdown_extra">Markdown Extra</a> for tables, footnotes, and more.', array('@filter_tips' => url('filter/tips'), '@markdown_extra' => 'http://michelf.com/projects/php-markdown/extra/'));
    }
  }

}
