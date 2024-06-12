<?php
/**
 * Pinterest for WooCommerce Abstract Note.
 *
 * @package Pinterest_For_WooCommerce/Classes/
 * @version 1.1.0
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Notes\Collection;

use Automattic\WooCommerce\Admin\Notes\DataStore;
use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;
use Automattic\WooCommerce\Admin\Notes\NotesUnavailableException;

use stdClass;

defined( 'ABSPATH' ) || exit;

/**
 * AbstractNote class. This is a proxy that helps us using the WC Admin Note
 * class in the context of Pinterest For WooCommerce plugin.
 *
 * @since 1.1.0
 */
abstract class AbstractNote {

	const NOTE_NAME = '';

	/**
	 * Check if the note has been previously added.
	 *
	 * @throws NotesUnavailableException Throws exception when notes are unavailable.
	 */
	public static function note_exists() {

		/**
		 * Notes Data Store.
		 *
		 * @var DataStore $data_store
		 */
		$data_store = Notes::load_data_store();
		$note_ids   = $data_store->get_notes_with_name( static::NOTE_NAME );
		return ! empty( $note_ids );
	}

	/**
	 * Get the note entry.
	 *
	 * @since 1.1.0
	 * @return Note Pre-filled note.
	 */
	public function prepare_note() {
		$note = new Note();
		$this->fill_in_note_details( $note );
		return $note;
	}

	/**
	 * Use helper functions to prepare the note.
	 *
	 * @since 1.1.0
	 * @param Note $note The note that we are setting up.
	 *
	 * @return void
	 */
	protected function fill_in_note_details( $note ): void {
		$note->set_title( $this->get_note_title() );
		$note->set_content( $this->get_note_content() );
		$note->set_content_data( new stdClass() );
		$note->set_type( $this->get_type() );
		$note->set_layout( $this->get_layout() );
		$note->set_image( $this->get_image() );
		$note->set_name( $this->get_name() );
		$note->set_source( $this->get_slug() );
		$this->add_action( $note );
	}

	/**
	 * Get note type.
	 *
	 * @since 1.1.0
	 * @return string Note type.
	 */
	protected function get_type(): string {
		return Note::E_WC_ADMIN_NOTE_INFORMATIONAL;
	}

	/**
	 * Get the note's unique name.
	 *
	 * @since 1.1.0
	 * @return string Note name.
	 */
	protected function get_name(): string {
		return static::NOTE_NAME;
	}

	/**
	 * Get note layout style.
	 *
	 * @since 1.1.0
	 * @return string Note layout style.
	 */
	protected function get_layout() {
		return 'plain';
	}

	/**
	 * Get note image.
	 *
	 * @since 1.1.0
	 * @return string URL to note image.
	 */
	protected function get_image() {
		return '';
	}

	/**
	 * Add action to note if necessary.
	 *
	 * @since 1.1.0
	 * @param Note $note Note to which we want to add an action.
	 */
	protected function add_action( $note ): void {}

	/**
	 * Get note slug;
	 *
	 * @since 1.1.0
	 * @return string Note Slug.
	 */
	private function get_slug(): string {
		return PINTEREST_FOR_WOOCOMMERCE_PREFIX;
	}

	/**
	 * Get note title.
	 *
	 * @since 1.1.0
	 * @return string Note title.
	 */
	abstract protected function get_note_title(): string;

	/**
	 * Get note content.
	 *
	 * @since 1.1.0
	 * @return string Note content.
	 */
	abstract protected function get_note_content(): string;

	/**
	 * Check whether the note should be added.
	 *
	 * @since 1.1.0
	 * @return bool Should the note be added.
	 */
	abstract public static function should_be_added(): bool;

}
