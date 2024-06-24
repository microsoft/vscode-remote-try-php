<?php
/**
 * Zip AI - Token Calculator
 *
 * @package zip-ai
 */

namespace ZipAI\Classes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Token_Calculator Class.
 *
 * @since 1.0.0
 */
class Token_Calculator {
	/**
	 * Get the GPT encoded tokens.
	 *
	 * @param string $text The text to encode.
	 * @since 1.0.0
	 * @return array The encoded tokens.
	 */
	public static function gpt_encode( $text ) {
		$bpe_tokens = [];
		// If the text is empty, then abandon ship.
		if ( empty( $text ) ) {
			return $bpe_tokens;
		}
		// Load the characters.
		$raw_chars    = file_get_contents( ZIP_AI_DIR . 'assets/open-ai/json/characters.json' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$byte_encoder = json_decode( $raw_chars, true );
		// If the characters are empty, then abandon ship.
		if ( empty( $byte_encoder ) ) {
			return $bpe_tokens;
		}
		// Load the encoder.
		$rencoder = file_get_contents( ZIP_AI_DIR . 'assets/open-ai/json/encoder.json' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$encoder  = json_decode( $rencoder, true );
		// If the encoder is empty, then abandon ship.
		if ( empty( $encoder ) ) {
			return $bpe_tokens;
		}
		// Load the vocabulary.
		$bpe_file = file_get_contents( ZIP_AI_DIR . 'assets/open-ai/json/vocab.bpe' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		// If the vocabulary is empty, then abandon ship.
		if ( empty( $bpe_file ) ) {
			return $bpe_tokens;
		}
		// Match the text with the regex.
		preg_match_all( "#'s|'t|'re|'ve|'m|'ll|'d| ?\p{L}+| ?\p{N}+| ?[^\s\p{L}\p{N}]+|\s+(?!\S)|\s+#u", $text, $matches );
		// If the matches are empty, then abandon ship.
		if ( ! isset( $matches[0] ) || 0 === count( $matches[0] ) ) {
			return $bpe_tokens;
		}
		// Load the BPE merges.
		$lines           = preg_split( '/\r\n|\r|\n/', $bpe_file );
		$bpe_merges      = [];
		$bpe_merges_temp = array_slice( $lines, 1, count( $lines ), true );
		foreach ( $bpe_merges_temp as $bmt ) {
			$split_bmt = preg_split( '#(\s+)#', $bmt );
			$split_bmt = array_filter( $split_bmt, [ 'ZipAI\Classes\Token_Calculator', 'gpt_filter' ] );
			if ( count( $split_bmt ) > 0 ) {
				$bpe_merges[] = $split_bmt;
			}
		}
		// Load the BPE ranks.
		$bpe_ranks = self::gpt_dict_zip( $bpe_merges, range( 0, count( $bpe_merges ) - 1 ) );
		$cache     = [];
		// Loop through the matches.
		foreach ( $matches[0] as $token ) {
			$new_tokens = [];
			$chars      = [];
			$token      = self::gpt_utf8_encode( $token );
			// Either use mb_strlen to get the length of the token in UTF-8 or use str_split.
			// This is for backwards compatibility.
			if ( function_exists( 'mb_strlen' ) ) {
				$len = mb_strlen( $token, 'UTF-8' );
				for ( $i = 0; $i < $len; $i++ ) {
					$chars[] = mb_substr( $token, $i, 1, 'UTF-8' );
				}
			} else {
				$chars = str_split( $token );
			}
			$result_word = '';
			// Loop through the characters and encode them.
			foreach ( $chars as $char ) {
				if ( isset( $byte_encoder[ self::gpt_unichr( $char ) ] ) ) {
					$result_word .= $byte_encoder[ self::gpt_unichr( $char ) ];
				}
			}
			// Encode the BPE.
			$new_tokens_bpe = self::gpt_bpe( $result_word, $bpe_ranks, $cache );
			$new_tokens_bpe = explode( ' ', $new_tokens_bpe );
			// Loop through the BPE tokens and encode them.
			foreach ( $new_tokens_bpe as $x ) {
				if ( isset( $encoder[ $x ] ) ) {
					if ( isset( $new_tokens[ $x ] ) ) {
						$new_tokens[ wp_rand() . '---' . $x ] = $encoder[ $x ];
					} else {
						$new_tokens[ $x ] = $encoder[ $x ];
					}
				} else {
					if ( isset( $new_tokens[ $x ] ) ) {
						$new_tokens[ wp_rand() . '---' . $x ] = $x;
					} else {
						$new_tokens[ $x ] = $x;
					}
				}
			}
			// Loop through the new tokens and add them to the BPE tokens.
			foreach ( $new_tokens as $ninx => $nval ) {
				if ( isset( $bpe_tokens[ $ninx ] ) ) {
					$bpe_tokens[ wp_rand() . '---' . $ninx ] = $nval;
				} else {
					$bpe_tokens[ $ninx ] = $nval;
				}
			}
		}
		// Return the encoded BPE tokens.
		return $bpe_tokens;
	}

	/**
	 * Get the ranks of the BPE merges.
	 *
	 * @param array $x The BPE merges.
	 * @param array $y The range.
	 * @since 1.0.0
	 * @return array The ranks.
	 */
	public static function gpt_dict_zip( $x, $y ) {
		$result = [];
		$cnt    = 0;
		foreach ( $x as $i ) {
			if ( isset( $i[1] ) && isset( $i[0] ) ) {
				$result[ $i[0] . ',' . $i[1] ] = $cnt;
				$cnt++;
			}
		}
		return $result;
	}

	/**
	 * Get the UTF-8 character of the given string/token.
	 *
	 * @param string $str The string/token.
	 * @since 1.0.0
	 * @return string The UTF-8 character.
	 */
	public static function gpt_utf8_encode( $str ) {
		$str .= $str;
		$len  = strlen( $str );
		for ( $i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j ) {
			switch ( true ) {
				case $str[ $i ] < "\x80":
					$str[ $j ] = $str[ $i ];
					break;
				case $str[ $i ] < "\xC0":
					$str[ $j ]   = "\xC2";
					$str[ ++$j ] = $str[ $i ];
					break;
				default:
					$str[ $j ]   = "\xC3";
					$str[ ++$j ] = chr( ord( $str[ $i ] ) - 64 );
					break;
			}
		}

		return substr( $str, 0, $j );
	}

	/**
	 * Get the byte size of the given character.
	 *
	 * @param string $c The character.
	 * @since 1.0.0
	 * @return int The byte size.
	 */
	public static function gpt_unichr( $c ) {
		if ( ord( $c[0] ) >= 0 && ord( $c[0] ) <= 127 ) {
			return ord( $c[0] );
		}
		if ( ord( $c[0] ) >= 192 && ord( $c[0] ) <= 223 ) {
			return ( ord( $c[0] ) - 192 ) * 64 + ( ord( $c[1] ) - 128 );
		}
		if ( ord( $c[0] ) >= 224 && ord( $c[0] ) <= 239 ) {
			return ( ord( $c[0] ) - 224 ) * 4096 + ( ord( $c[1] ) - 128 ) * 64 + ( ord( $c[2] ) - 128 );
		}
		if ( ord( $c[0] ) >= 240 && ord( $c[0] ) <= 247 ) {
			return ( ord( $c[0] ) - 240 ) * 262144 + ( ord( $c[1] ) - 128 ) * 4096 + ( ord( $c[2] ) - 128 ) * 64 + ( ord( $c[3] ) - 128 );
		}
		if ( ord( $c[0] ) >= 248 && ord( $c[0] ) <= 251 ) {
			return ( ord( $c[0] ) - 248 ) * 16777216 + ( ord( $c[1] ) - 128 ) * 262144 + ( ord( $c[2] ) - 128 ) * 4096 + ( ord( $c[3] ) - 128 ) * 64 + ( ord( $c[4] ) - 128 );
		}
		if ( ord( $c[0] ) >= 252 && ord( $c[0] ) <= 253 ) {
			return ( ord( $c[0] ) - 252 ) * 1073741824 + ( ord( $c[1] ) - 128 ) * 16777216 + ( ord( $c[2] ) - 128 ) * 262144 + ( ord( $c[3] ) - 128 ) * 4096 + ( ord( $c[4] ) - 128 ) * 64 + ( ord( $c[5] ) - 128 );
		}
		if ( ord( $c[0] ) >= 254 && ord( $c[0] ) <= 255 ) {
			return 0;
		}

		return 0;
	}

	/**
	 * Get the encoded BPE tokens.
	 *
	 * @param string $token The token.
	 * @param array  $bpe_ranks The BPE ranks.
	 * @param array  $cache The cache.
	 */
	public static function gpt_bpe( $token, $bpe_ranks, &$cache ) {
		// Check if the token is in the cache.
		if ( array_key_exists( $token, $cache ) ) {
			return $cache[ $token ];
		}
		// Split the token into UTF-8 characters.
		$word     = self::gpt_split( $token );
		$init_len = count( $word );
		$pairs    = self::gpt_get_pairs( $word );
		// If there are no pairs, return the token.
		if ( ! $pairs ) {
			return $token;
		}
		// Loop through the pairs.
		while ( true ) {
			$min_pairs = [];
			// Get the minimum pair.
			foreach ( $pairs as $pair ) {
				if ( array_key_exists( $pair[0] . ',' . $pair[1], $bpe_ranks ) ) {
					$rank               = $bpe_ranks[ $pair[0] . ',' . $pair[1] ];
					$min_pairs[ $rank ] = $pair;
				} else {
					$min_pairs[ 10e10 ] = $pair;
				}
			}
			// Sort the minimum pairs.
			ksort( $min_pairs );
			$min_key = array_key_first( $min_pairs );
			// Loop through the minimum pairs.
			foreach ( $min_pairs as $mpi => $mp ) {
				if ( $mpi < $min_key ) {
					$min_key = $mpi;
				}
			}
			$bigram = $min_pairs[ $min_key ];
			// If the bigram is not in the BPE ranks, break.
			if ( ! array_key_exists( $bigram[0] . ',' . $bigram[1], $bpe_ranks ) ) {
				break;
			}
			$first       = $bigram[0];
			$second      = $bigram[1];
			$new_word    = [];
			$i           = 0;
			$word_length = count( $word );
			// Loop through the word.
			while ( $i < $word_length ) {
				// Get the index of the first bigram.
				$j = self::gpt_index_of( $word, $first, $i );
				// If the index is -1, add the rest of the word to the new word and break.
				if ( -1 === $j ) {
					$new_word = array_merge( $new_word, array_slice( $word, $i, null, true ) );
					break;
				}
				// If the index is not 0, add the rest of the word to the new word.
				if ( $i > $j ) {
					$slicer = [];
				} elseif ( 0 === $j ) {
					$slicer = [];
				} else {
					$slicer = array_slice( $word, $i, $j - $i, true );
				}
				$new_word = array_merge( $new_word, $slicer );
				// If the length of the new word is greater than the initial length, break.
				if ( count( $new_word ) > $init_len ) {
					break;
				}
				$i = $j;
				// If the next character is the second bigram, add the bigram to the new word - otherwise, add the character to the new word.
				if ( $word[ $i ] === $first && $i < count( $word ) - 1 && $word[ $i + 1 ] === $second ) {
					array_push( $new_word, $first . $second );
					$i = $i + 2;
				} else {
					array_push( $new_word, $word[ $i ] );
					++$i;
				}
			}
			// If the new word is the same as the old word, break.
			if ( $word === $new_word ) {
				break;
			}
			$word        = $new_word;
			$word_length = count( $word );
			// If the length of the word is 1, break - otherwise, get the pairs.
			if ( 1 === $word_length ) {
				break;
			} else {
				$pairs = self::gpt_get_pairs( $word );
			}
		}
		$word            = implode( ' ', $word );
		$cache[ $token ] = $word;

		// Return the word.
		return $word;
	}

	/**
	 * Split a string into UTF-8 characters.
	 *
	 * @param string $str The string.
	 * @param int    $len The length - default 1.
	 * @since 1.0.0
	 * @return array The UTF-8 characters.
	 */
	public static function gpt_split( $str, $len = 1 ) {
		$arr = [];
		// Handle backwards compatibility.
		if ( function_exists( 'mb_strlen' ) ) {
			$length = mb_strlen( $str, 'UTF-8' );
		} else {
			$length = strlen( $str );
		}

		// Loop through the string characters, forming an array of UTF-8 characters.
		for ( $i = 0; $i < $length; $i += $len ) {
			if ( function_exists( 'mb_substr' ) ) {
				$arr[] = mb_substr( $str, $i, $len, 'UTF-8' );
			} else {
				$arr[] = substr( $str, $i, $len );
			}
		}

		return $arr;

	}

	/**
	 * Get the pairs of a word.
	 *
	 * @param string $word The word.
	 * @since 1.0.0
	 * @return array The pairs.
	 */
	public static function gpt_get_pairs( $word ) {
		$pairs       = [];
		$prev_char   = $word[0];
		$word_length = count( $word );
		for ( $i = 1; $i < $word_length; $i++ ) {
			$char      = $word[ $i ];
			$pairs[]   = [ $prev_char, $char ];
			$prev_char = $char;
		}

		return $pairs;
	}

	/**
	 * Get the index of a value in an array.
	 *
	 * @param array  $arrax The array.
	 * @param string $search_element The value to search for.
	 * @param int    $from_index The index to start searching from.
	 * @since 1.0.0
	 * @return int The index.
	 */
	public static function gpt_index_of( $arrax, $search_element, $from_index ) {
		$index = 0;
		foreach ( $arrax as $index => $value ) {
			if ( $index < $from_index ) {
				$index++;

				continue;
			}
			if ( $value === $search_element ) {
				return $index;
			}
			$index++;
		}

		return -1;
	}

	/**
	 * Filter a variable.
	 *
	 * @param mixed $var The variable.
	 * @since 1.0.0
	 * @return bool Whether the variable is not null, false, or empty.
	 */
	public static function gpt_filter( $var ) {
		return ! in_array( $var, [ null, false, '' ], true );
	}
}
