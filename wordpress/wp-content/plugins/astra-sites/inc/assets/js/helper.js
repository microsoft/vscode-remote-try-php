'use strict';

function _defineProperty( obj, key, value ) {
	if ( key in obj ) {
		Object.defineProperty( obj, key, {
			value: value,
			enumerable: true,
			configurable: true,
			writable: true,
		} );
	} else {
		obj[ key ] = value;
	}
	return obj;
}

function _slicedToArray( arr, i ) {
	return (
		_arrayWithHoles( arr ) ||
		_iterableToArrayLimit( arr, i ) ||
		_nonIterableRest()
	);
}

function _nonIterableRest() {
	throw new TypeError(
		'Invalid attempt to destructure non-iterable instance'
	);
}

function _iterableToArrayLimit( arr, i ) {
	var _arr = [];
	var _n = true;
	var _d = false;
	var _e = undefined;
	try {
		for (
			var _i = arr[ Symbol.iterator ](), _s;
			! ( _n = ( _s = _i.next() ).done );
			_n = true
		) {
			_arr.push( _s.value );
			if ( i && _arr.length === i ) break;
		}
	} catch ( err ) {
		_d = true;
		_e = err;
	} finally {
		try {
			if ( ! _n && _i[ 'return' ] != null ) _i[ 'return' ]();
		} finally {
			if ( _d ) throw _e;
		}
	}
	return _arr;
}

function _arrayWithHoles( arr ) {
	if ( Array.isArray( arr ) ) return arr;
}

var PHP = {
	stdClass: function stdClass() {},
	stringify: function stringify( val ) {
		var hash = new Map( [
			[ Infinity, 'd:INF;' ],
			[ -Infinity, 'd:-INF;' ],
			[ NaN, 'd:NAN;' ],
			[ null, 'N;' ],
			[ undefined, 'N;' ],
		] );

		var utf8length = function utf8length( str ) {
			return str ? encodeURI( str ).match( /(%.)?./g ).length : 0;
		};

		var serializeString = function serializeString( s ) {
			var delim =
				arguments.length > 1 && arguments[ 1 ] !== undefined
					? arguments[ 1 ]
					: '"';
			return ''
				.concat( utf8length( s ), ':' )
				.concat( delim[ 0 ] )
				.concat( s )
				.concat( delim[ delim.length - 1 ] );
		};

		var ref = 0;

		function serialize( val ) {
			var canReference =
				arguments.length > 1 && arguments[ 1 ] !== undefined
					? arguments[ 1 ]
					: true;
			if ( hash.has( val ) ) return hash.get( val );
			ref += canReference;
			if ( typeof val === 'string' )
				return 's:'.concat( serializeString( val ), ';' );
			if ( typeof val === 'number' )
				return ''
					.concat( Math.round( val ) === val ? 'i' : 'd', ':' )
					.concat(
						( '' + val )
							.toUpperCase()
							.replace( /(-?\d)E/, '$1.0E' ),
						';'
					);
			if ( typeof val === 'boolean' ) return 'b:'.concat( +val, ';' );
			var a = Array.isArray( val ) || val.constructor === Object;
			hash.set( val, ''.concat( 'rR'[ +a ], ':' ).concat( ref, ';' ) );

			if ( typeof val.serialize === 'function' ) {
				return 'C:'
					.concat( serializeString( val.constructor.name ), ':' )
					.concat( serializeString( val.serialize(), '{}' ) );
			}

			var vals = Object.entries( val ).filter( function ( _ref ) {
				var _ref2 = _slicedToArray( _ref, 2 ),
					k = _ref2[ 0 ],
					v = _ref2[ 1 ];

				return typeof v !== 'function';
			} );
			return (
				( a
					? 'a'
					: 'O:'.concat( serializeString( val.constructor.name ) ) ) +
				':'.concat( vals.length, ':{' ).concat(
					vals
						.map( function ( _ref3 ) {
							var _ref4 = _slicedToArray( _ref3, 2 ),
								k = _ref4[ 0 ],
								v = _ref4[ 1 ];

							return (
								serialize(
									a && /^\d{1,16}$/.test( k ) ? +k : k,
									false
								) + serialize( v )
							);
						} )
						.join( '' ),
					'}'
				)
			);
		}

		return serialize( val );
	},
	// Provide in second argument the classes that may be instantiated
	//  e.g.  { MyClass1, MyClass2 }
	parse: function parse( str ) {
		var allowedClasses =
			arguments.length > 1 && arguments[ 1 ] !== undefined
				? arguments[ 1 ]
				: {};
		allowedClasses.stdClass = PHP.stdClass; // Always allowed.

		var offset = 0;
		var values = [ null ];
		var specialNums = {
			INF: Infinity,
			'-INF': -Infinity,
			NAN: NaN,
		};

		var kick = function kick( msg ) {
			var i =
				arguments.length > 1 && arguments[ 1 ] !== undefined
					? arguments[ 1 ]
					: offset;
			throw new Error(
				'Error at '
					.concat( i, ': ' )
					.concat( msg, '\n' )
					.concat( str, '\n' )
					.concat( ' '.repeat( i ), '^' )
			);
		};

		var read = function read( expected, ret ) {
			return expected ===
				str.slice( offset, ( offset += expected.length ) )
				? ret
				: kick(
						"Expected '".concat( expected, "'" ),
						offset - expected.length
				  );
		};

		function readMatch( regex, msg ) {
			var terminator =
				arguments.length > 2 && arguments[ 2 ] !== undefined
					? arguments[ 2 ]
					: ';';
			read( ':' );
			var match = regex.exec( str.slice( offset ) );
			if ( ! match )
				kick(
					'Exected '
						.concat( msg, ", but got '" )
						.concat(
							str
								.slice( offset )
								.match( /^[:;{}]|[^:;{}]*/ )[ 0 ],
							"'"
						)
				);
			offset += match[ 0 ].length;
			return read( terminator, match[ 0 ] );
		}

		function readUtf8chars( numUtf8Bytes ) {
			var terminator =
				arguments.length > 1 && arguments[ 1 ] !== undefined
					? arguments[ 1 ]
					: '';
			var i = offset;

			while ( numUtf8Bytes > 0 ) {
				var code = str.charCodeAt( offset++ );
				numUtf8Bytes -=
					code < 0x80
						? 1
						: code < 0x800 || code >> 11 === 0x1b
						? 2
						: 3;
			}

			return numUtf8Bytes
				? kick( 'Invalid string length', i - 2 )
				: read( terminator, str.slice( i, offset ) );
		}

		var create = function create( className ) {
			return ! className
				? {}
				: allowedClasses[ className ]
				? Object.create( allowedClasses[ className ].prototype )
				: new ( _defineProperty( {}, className, function () {} )[
						className
				  ] )();
		}; // Create a mock class for this name

		var readBoolean = function readBoolean() {
			return readMatch( /^[01]/, "a '0' or '1'", ';' );
		};

		var readInt = function readInt() {
			return +readMatch( /^-?\d+/, 'an integer', ';' );
		};

		var readUInt = function readUInt( terminator ) {
			return +readMatch( /^\d+/, 'an unsigned integer', terminator );
		};

		var readString = function readString() {
			var terminator =
				arguments.length > 0 && arguments[ 0 ] !== undefined
					? arguments[ 0 ]
					: '';
			return readUtf8chars( readUInt( ':"' ), '"' + terminator );
		};

		function readDecimal() {
			var num = readMatch(
				/^-?(\d+(\.\d+)?(E[+-]\d+)?|INF)|NAN/,
				'a decimal number',
				';'
			);
			return num in specialNums ? specialNums[ num ] : +num;
		}

		function readKey() {
			var typ = str[ offset++ ];
			return typ === 's'
				? readString( ';' )
				: typ === 'i'
				? readUInt( ';' )
				: kick(
						"Expected 's' or 'i' as type for a key, but got ${str[offset-1]}",
						offset - 1
				  );
		}

		function readObject( obj ) {
			for ( var i = 0, length = readUInt( ':{' ); i < length; i++ ) {
				obj[ readKey() ] = readValue();
			}

			return read( '}', obj );
		}

		function readArray() {
			var obj = readObject( {} );
			return Object.keys( obj ).some( function ( key, i ) {
				return key != i;
			} )
				? obj
				: Object.values( obj );
		}

		function readCustomObject( obj ) {
			if ( typeof obj.unserialize !== 'function' )
				kick(
					'Instance of '.concat(
						obj.constructor.name,
						' does not have an "unserialize" method'
					)
				);
			obj.unserialize( readUtf8chars( readUInt( ':{' ) ) );
			return read( '}', obj );
		}

		function readValue() {
			var typ = str[ offset++ ].toLowerCase();
			var ref = values.push( null ) - 1;
			var val =
				typ === 'n'
					? read( ';', null )
					: typ === 's'
					? readString( ';' )
					: typ === 'b'
					? readBoolean()
					: typ === 'i'
					? readInt()
					: typ === 'd'
					? readDecimal()
					: typ === 'a'
					? readArray() // Associative array
					: typ === 'o'
					? readObject( create( readString() ) ) // Object
					: typ === 'c'
					? readCustomObject( create( readString() ) ) // Custom serialized object
					: typ === 'r'
					? values[ readInt() ] // Backreference
					: kick( 'Unexpected type '.concat( typ ), offset - 1 );
			if ( typ !== 'r' ) values[ ref ] = val;
			return val;
		}

		var val = readValue();
		if ( offset !== str.length ) kick( 'Unexpected trailing character' );
		return val;
	},
};
