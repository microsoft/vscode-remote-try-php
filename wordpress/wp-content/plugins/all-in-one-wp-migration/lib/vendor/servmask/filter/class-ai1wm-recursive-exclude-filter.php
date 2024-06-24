<?php
/**
 * Copyright (C) 2014-2023 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

class Ai1wm_Recursive_Exclude_Filter extends RecursiveFilterIterator {

	protected $exclude = array();

	public function __construct( RecursiveIterator $iterator, $exclude = array() ) {
		parent::__construct( $iterator );
		if ( is_array( $exclude ) ) {
			foreach ( $exclude as $path ) {
				$this->exclude[] = ai1wm_replace_forward_slash_with_directory_separator( $path );
			}
		}
	}

	#[\ReturnTypeWillChange]
	public function accept() {
		if ( in_array( ai1wm_replace_forward_slash_with_directory_separator( $this->getInnerIterator()->getSubPathname() ), $this->exclude ) ) {
			return false;
		}

		if ( in_array( ai1wm_replace_forward_slash_with_directory_separator( $this->getInnerIterator()->getPathname() ), $this->exclude ) ) {
			return false;
		}

		if ( in_array( ai1wm_replace_forward_slash_with_directory_separator( $this->getInnerIterator()->getPath() ), $this->exclude ) ) {
			return false;
		}

		if ( strpos( $this->getInnerIterator()->getSubPathname(), "\n" ) !== false ) {
			return false;
		}

		if ( strpos( $this->getInnerIterator()->getSubPathname(), "\r" ) !== false ) {
			return false;
		}

		return true;
	}

	#[\ReturnTypeWillChange]
	public function getChildren() {
		return new self( $this->getInnerIterator()->getChildren(), $this->exclude );
	}
}
