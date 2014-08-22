<?php

/**
 * @file
 * Contains \Drupal\markdown\Plugin\Filter\Markdown.
 */

namespace Drupal\markdown\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\FilterProcessResult;
use Michelf\MarkdownExtra;

/**
 * Provides a filter for markdown.
 *
 * @Filter(
 *   id = "markdown",
 *   module = "markdown",
 *   title = @Translation("Markdown"),
 *   description = @Translation("Allows content to be submitted using Markdown, a simple plain-text syntax that is filtered into valid HTML."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 * )
 */
class Markdown extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $library = libraries_detect('php-markdown');

    $form['markdown_wrapper'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Markdown'),
    );
    $links = array(
      'Markdown PHP Lib Version: ' . l($library['version'], $library['vendor url']),
    );
    $form['markdown_wrapper']['markdown_status'] = array(
      '#title' => $this->t('Versions'),
      '#theme' => 'item_list',
      '#items' => $links,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    if (!empty($text)) {
      libraries_load('php-markdown', 'markdown-extra');
      $text = MarkdownExtra::defaultTransform($text);
    }

    return new FilterProcessResult($text);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      return t('Quick Tips:<ul>
      <li>Two or more spaces at a line\'s end = Line break</li>
      <li>Double returns = Paragraph</li>
      <li>*Single asterisks* or _single underscores_ = <em>Emphasis</em></li>
      <li>**Double** or __double__ = <strong>Strong</strong></li>
      <li>This is [a link](http://the.link.example.com "The optional title text")</li>
      </ul>For complete details on the Markdown syntax, see the <a href="http://daringfireball.net/projects/markdown/syntax">Markdown documentation</a> and <a href="http://michelf.com/projects/php-markdown/extra/">Markdown Extra documentation</a> for tables, footnotes, and more.');
    }
    else {
      return t('You can use <a href="@filter_tips">Markdown syntax</a> to format and style the text. Also see <a href="@markdown_extra">Markdown Extra</a> for tables, footnotes, and more.', array('@filter_tips' => url('filter/tips'), '@markdown_extra' => 'http://michelf.com/projects/php-markdown/extra/'));
    }
  }

}
