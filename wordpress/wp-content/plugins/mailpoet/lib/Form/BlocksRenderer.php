<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FormEntity;
use MailPoet\Form\Block\Checkbox;
use MailPoet\Form\Block\Column;
use MailPoet\Form\Block\Columns;
use MailPoet\Form\Block\Date;
use MailPoet\Form\Block\Divider;
use MailPoet\Form\Block\Heading;
use MailPoet\Form\Block\Html;
use MailPoet\Form\Block\Image;
use MailPoet\Form\Block\Paragraph;
use MailPoet\Form\Block\Radio;
use MailPoet\Form\Block\Segment;
use MailPoet\Form\Block\Select;
use MailPoet\Form\Block\Submit;
use MailPoet\Form\Block\Text;
use MailPoet\Form\Block\Textarea;

class BlocksRenderer {
  /** @var Checkbox */
  private $checkbox;

  /** @var Date */
  private $date;

  /** @var Divider */
  private $divider;

  /** @var Html */
  private $html;

  /** @var Image */
  private $image;

  /** @var Radio */
  private $radio;

  /** @var Segment */
  private $segment;

  /** @var Select */
  private $select;

  /** @var Submit */
  private $submit;

  /** @var Text */
  private $text;

  /** @var Textarea */
  private $textarea;

  /** @var Column */
  private $column;

  /** @var Columns */
  private $columns;

  /** @var Heading */
  private $heading;

  /** @var Paragraph */
  private $paragraph;

  public function __construct(
    Checkbox $checkbox,
    Column $column,
    Columns $columns,
    Date $date,
    Divider $divider,
    Html $html,
    Image $image,
    Heading $heading,
    Paragraph $paragraph,
    Radio $radio,
    Segment $segment,
    Select $select,
    Submit $submit,
    Text $text,
    Textarea $textarea
  ) {
    $this->checkbox = $checkbox;
    $this->column = $column;
    $this->columns = $columns;
    $this->date = $date;
    $this->divider = $divider;
    $this->html = $html;
    $this->image = $image;
    $this->radio = $radio;
    $this->segment = $segment;
    $this->select = $select;
    $this->submit = $submit;
    $this->text = $text;
    $this->textarea = $textarea;
    $this->heading = $heading;
    $this->paragraph = $paragraph;
  }

  public function renderBlock(array $block, array $formSettings, ?int $formId): string {
    $html = '';
    if ($formId) {
      $formSettings['id'] = $formId;
    }
    switch ($block['type']) {
      case FormEntity::HTML_BLOCK_TYPE:
        $html .= $this->html->render($block, $formSettings);
        break;

      case FormEntity::HEADING_BLOCK_TYPE:
        $html .= $this->heading->render($block);
        break;

      case FormEntity::IMAGE_BLOCK_TYPE:
        $html .= $this->image->render($block);
        break;

      case FormEntity::PARAGRAPH_BLOCK_TYPE:
        $html .= $this->paragraph->render($block);
        break;

      case FormEntity::DIVIDER_BLOCK_TYPE:
        $html .= $this->divider->render($block);
        break;

      case FormEntity::CHECKBOX_BLOCK_TYPE:
        $html .= $this->checkbox->render($block, $formSettings, $formId);
        break;

      case FormEntity::RADIO_BLOCK_TYPE:
        $html .= $this->radio->render($block, $formSettings, $formId);
        break;

      case FormEntity::SEGMENT_SELECTION_BLOCK_TYPE:
        $html .= $this->segment->render($block, $formSettings, $formId);
        break;

      case FormEntity::DATE_BLOCK_TYPE:
        $html .= $this->date->render($block, $formSettings, $formId);
        break;

      case FormEntity::SELECT_BLOCK_TYPE:
        $html .= $this->select->render($block, $formSettings);
        break;

      case FormEntity::TEXT_BLOCK_TYPE:
        $html .= $this->text->render($block, $formSettings);
        break;

      case FormEntity::TEXTAREA_BLOCK_TYPE:
        $html .= $this->textarea->render($block, $formSettings);
        break;

      case FormEntity::SUBMIT_BLOCK_TYPE:
        $html .= $this->submit->render($block, $formSettings);
        break;
    }
    return $html;
  }

  public function renderContainerBlock(array $block, string $content) {
    $html = '';
    switch ($block['type']) {
      case FormEntity::COLUMNS_BLOCK_TYPE:
        $html .= $this->columns->render($block, $content);
        break;

      case FormEntity::COLUMN_BLOCK_TYPE:
        $html .= $this->column->render($block, $content);
        break;
    }
    return $html;
  }
}
