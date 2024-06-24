import { PlusIcon, XMarkIcon } from '@heroicons/react/20/solid';
import { useState, useMemo } from '@wordpress/element';
import {
	FacebookIcon,
	InstagramIcon,
	LinkedInIcon,
	TwitterIcon,
	YouTubeIcon,
	GoogleIcon,
	YelpIcon,
} from '../../ui/icons';
import Dropdown from './dropdown';
import Input from './input';
import { socialMediaParser } from '../utils/helpers';
import { __, sprintf } from '@wordpress/i18n';

const getPlaceholder = ( socialMedia ) => {
	switch ( socialMedia ) {
		case 'Facebook':
		case 'Twitter':
		case 'Instagram':
		case 'LinkedIn':
		case 'YouTube':
			return sprintf(
				/* translators: %s: social media name */
				__( `Enter your %s account URL`, 'astra-sites' ),
				socialMedia
			);
		case 'Google Business':
			return __( 'Enter your Google Business URL', 'astra-sites' );
		case 'Yelp':
			return __( 'Enter your Yelp business URL', 'astra-sites' );
		default:
			return __( 'Enter your account URL', 'astra-sites' );
	}
};

const getSlugPlaceholder = ( socialMedia ) => {
	switch ( socialMedia ) {
		case 'Facebook':
		case 'Twitter':
		case 'Instagram':
		case 'LinkedIn':
			return `username`;
		case 'YouTube':
			return `channel-name`;
		case 'Google My Business':
		case 'Yelp':
			return 'business-name';
		default:
			return __( 'Enter your account URL', 'astra-sites' );
	}
};

const SocialMediaItem = ( { socialMedia, onRemove, onEdit } ) => {
	const [ isEditing, setIsEditing ] = useState( false );
	const [ editedSlug, setEditedSlug ] = useState( socialMedia.slug );

	const handleDoubleClick = () => {
		setEditedSlug( socialMedia.slug );
		setIsEditing( true );
	};

	const handleUpdateURL = ( slug = '' ) => {
		setIsEditing( false );
		if ( ! slug.trim() ) {
			setEditedSlug( socialMedia.slug );
		} else {
			try {
				// if the typed slug is a url, we need to send it as it is
				new URL( slug );
				onEdit( slug.trim() );
			} catch ( error ) {
				// if it's not a url, we need to add the prefix
				onEdit( socialMedia.prefix + slug.trim() );
			}
		}
	};

	const handleBlur = () => {
		handleUpdateURL( editedSlug );
	};

	const handleKeyDown = ( event ) => {
		if ( event.key === 'Enter' ) {
			event.preventDefault();
			handleUpdateURL( editedSlug );
		} else if ( event.key === 'Escape' ) {
			handleUpdateURL();
		}
	};

	const placeholder = getSlugPlaceholder( socialMedia.name );

	return (
		<div
			key={ socialMedia.id }
			className="relative h-[50px] pl-[23px] pr-[25px] rounded-[25px] bg-white flex items-center gap-3 shadow-sm"
			onDoubleClick={ handleDoubleClick }
		>
			{ ! isEditing && (
				<div
					role="button"
					className="absolute top-0 right-0 w-4 h-4 rounded-full flex items-center justify-center cursor-pointer bg-nav-inactive"
					onClick={ onRemove }
					tabIndex={ 0 }
					onKeyDown={ onRemove }
				>
					<XMarkIcon className="w-4 h-4 text-white" />
				</div>
			) }
			<socialMedia.icon className="shrink-0 text-nav-active inline-block" />
			{ isEditing ? (
				<Input
					ref={ ( node ) => {
						if ( node ) {
							node.focus();
						}
					} }
					name="socialMediaURL"
					inputClassName="!border-0 !bg-transparent !shadow-none focus:!ring-0 px-0 min-w-fit placeholder:!text-[0.9rem] rounded-none"
					value={ editedSlug }
					onChange={ ( e ) => {
						setEditedSlug( e.target.value );
					} }
					className="w-full"
					placeholder={ placeholder }
					noBorder
					onBlur={ handleBlur }
					onKeyDown={ handleKeyDown }
					prefixIcon={
						<p className="m-0 pr-2">{ socialMedia.prefix }</p>
					}
					enableAutoGrow
				/>
			) : (
				<p className="text-base font-medium text-body-text">
					{ socialMedia.url }
				</p>
			) }
		</div>
	);
};

const SocialMediaAdd = ( { list, onChange } ) => {
	const socialMediaList = [
		{
			name: 'Facebook',
			id: 'facebook',
			icon: FacebookIcon,
		},
		{
			name: 'Twitter',
			id: 'twitter',
			icon: TwitterIcon,
		},
		{
			name: 'Instagram',
			id: 'instagram',
			icon: InstagramIcon,
		},
		{
			name: 'LinkedIn',
			id: 'linkedin',
			icon: LinkedInIcon,
		},
		{
			name: 'YouTube',
			id: 'youtube',
			icon: YouTubeIcon,
		},
		{
			name: 'Google My Business',
			id: 'google',
			icon: GoogleIcon,
		},
		{
			name: 'Yelp',
			id: 'yelp',
			icon: YelpIcon,
		},
	];

	const [ selectedSocialMedia, setSelectedSocialMedia ] = useState( null );
	const [ socialMediaURL, setSocialMediaURL ] = useState( '' );

	const socialMediaHandles = {
		twitter: 'twitter.com/',
		facebook: 'facebook.com/',
		instagram: 'instagram.com/',
		linkedin: 'linkedin.com/in/',
		youtube: 'youtube.com/',
		google: 'google.com/maps/place/',
		yelp: 'yelp.com/biz/',
	};

	const validateSocialMediaURL = ( url, type ) => {
		if ( url === '' ) {
			return true;
		}
		return socialMediaParser.validate( type, url );
	};

	const getSocialMediaURL = ( LINK, SOCIAL_MEDIA_TYPE ) => {
		const socialMediaDomain =
			socialMediaHandles[ SOCIAL_MEDIA_TYPE?.toLowerCase() ];

		const matches = socialMediaParser.parse( LINK );

		// if no matches or if match is not the selected social media type, return it as it is
		if (
			Object.keys( matches ).length === 0 ||
			! matches[ SOCIAL_MEDIA_TYPE.toLowerCase() ]
		) {
			try {
				const domain = new URL(
					LINK.replace( `https://${ socialMediaDomain }` )
				).hostname;
				return [ LINK, '', `https://${ domain }/` ];
			} catch {
				// means it's not a valid URL, we can continue
			}
		}

		try {
			const slug = LINK.replace( socialMediaDomain, '' )
				.replace( `https://`, '' )
				.replace( 'http://', '' )
				.replace( 'www.', '' );

			const fullUrl = `https://${ socialMediaDomain }${ slug }`;

			return [ fullUrl, slug, `https://${ socialMediaDomain }` ];
		} catch ( error ) {
			return LINK;
		}
	};

	const filterList = ( socialMediaItemList ) => {
		if ( list.length === 0 ) {
			return socialMediaItemList;
		}
		const addedSocialMediaIds = list.map( ( sm ) => sm.id );
		return socialMediaItemList.filter(
			( sm ) => ! addedSocialMediaIds.includes( sm.id )
		);
	};

	const handleEnterLink = ( type ) => {
		if (
			! (
				typeof socialMediaURL === 'string' && !! socialMediaURL?.trim()
			)
		) {
			return;
		}
		const [ link, slug, prefix ] = getSocialMediaURL(
			socialMediaURL.trim(),
			type
		);
		const newList = [
			...list,
			{
				...selectedSocialMedia,
				url: link,
				slug,
				prefix,
				valid: validateSocialMediaURL( link, type ),
			},
		];
		onChange( newList );
		setSelectedSocialMedia( null );
		setSocialMediaURL( '' );
	};

	const handleEditLink = ( id, value ) => {
		const newList = list.map( ( sm ) => {
			if ( sm.id === id ) {
				const url = getSocialMediaURL( value, id )[ 0 ];
				return {
					...sm,
					url,
					valid: validateSocialMediaURL( url, id ),
				};
			}
			return sm;
		} );
		onChange( newList );
	};

	const updatedList = useMemo( () => {
		return list.map( ( sm ) => {
			const [ url, slug, prefix ] = getSocialMediaURL( sm.url, sm.id );
			const valid = validateSocialMediaURL( url, sm.id );
			return {
				...sm,
				url,
				slug,
				prefix,
				valid,
				icon: socialMediaList.find( ( item ) => item.id === sm.id )
					?.icon,
			};
		} );
	}, [ list ] );

	const socialMediaRender = () => {
		if ( selectedSocialMedia ) {
			const placeholderText = selectedSocialMedia
				? getPlaceholder( selectedSocialMedia.name )
				: __( 'Enter your account URL', 'astra-sites' );
			return (
				<div className="h-[50px] w-[520px] rounded-[25px] bg-white flex items-center">
					<Input
						name="socialMediaURL"
						value={ socialMediaURL }
						onChange={ ( e ) => {
							setSocialMediaURL( e.target.value );
						} }
						ref={ ( node ) => {
							if ( node ) {
								node.focus();
							}
						} }
						inputClassName="!pr-10 !pl-11 !border-0 !bg-transparent !shadow-none focus:!ring-0"
						className="w-full"
						placeholder={ placeholderText }
						noBorder
						prefixIcon={
							<div className="absolute left-4 flex items-center">
								<selectedSocialMedia.icon className="text-nav-active inline-block" />
							</div>
						}
						onBlur={ ( event ) => {
							event.preventDefault();
							handleEnterLink( selectedSocialMedia.id );
						} }
						onKeyDown={ ( event ) => {
							if ( event.key === 'Enter' ) {
								event.preventDefault();
								handleEnterLink( selectedSocialMedia.id );
							} else if ( event.key === 'Escape' ) {
								setSelectedSocialMedia( null );
								setSocialMediaURL( '' );
							}
						} }
						suffixIcon={
							<div
								className="absolute -top-4 right-0"
								onClick={ () => {
									setSelectedSocialMedia( null );
									setSocialMediaURL( '' );
								} }
								role="button"
								tabIndex={ 0 }
								onKeyDown={ () => {
									setSelectedSocialMedia( null );
									setSocialMediaURL( '' );
								} }
							>
								<div className="w-4 h-4 rounded-full flex items-center justify-center bg-app-inactive-icon cursor-pointer bg-nav-inactive">
									<XMarkIcon className="w-4 h-4 text-white" />
								</div>
							</div>
						}
					/>
				</div>
			);
		}
		if ( filterList( socialMediaList ).length ) {
			return (
				<Dropdown
					width="60"
					contentClassName="p-4 bg-white [&>:first-child]:pb-2.5 [&>:last-child]:pt-2.5 [&>:not(:first-child,:last-child)]:py-2.5 !divide-y !divide-border-primary divide-solid divide-x-0"
					trigger={
						<div className="p-3 rounded-full flex items-center justify-center bg-white cursor-pointer border border-border-primary border-solid shadow-small">
							<PlusIcon className="w-6 h-6 text-accent-st" />
						</div>
					}
					placement="top-start"
				>
					{ filterList( socialMediaList ).map( ( item, index ) => (
						<Dropdown.Item
							as="div"
							role="none"
							key={ index }
							className="only:!py-0"
							onClick={ () => setSelectedSocialMedia( item ) }
						>
							<button
								onClick={ () => null }
								type="button"
								className="w-full flex items-center text-sm font-normal text-left py-2 px-2 leading-5 hover:bg-background-secondary focus:outline-none transition duration-150 ease-in-out space-x-2 rounded bg-transparent border-0 cursor-pointer"
							>
								<item.icon className="text-nav-inactive inline-block" />
								<span className="text-body-text">
									{ item.name }
								</span>
							</button>
						</Dropdown.Item>
					) ) }
				</Dropdown>
			);
		}
		return '';
	};

	return (
		<div>
			<div className="text-base font-medium leading-[21px] mb-5 text-heading-text">
				{ __( 'Social Media', 'astra-sites' ) }
			</div>

			<div className="flex items-start gap-4 flex-wrap">
				{ updatedList?.length > 0 && (
					<div className="flex items-start gap-4 flex-wrap">
						{ updatedList.map( ( sm ) => (
							<div key={ sm.id }>
								<SocialMediaItem
									socialMedia={ sm }
									onRemove={ () => {
										onChange(
											updatedList.filter(
												( item ) => item.id !== sm.id
											)
										);
									} }
									onEdit={ ( url ) =>
										handleEditLink( sm.id, url )
									}
								/>
								{ ! sm.valid && (
									<div className="p-3">
										<p className="!m-0 !p-0 !text-alert-error !text-sm">
											{ sprintf(
												/* translators: %s: social media name */
												__(
													'This might not be a valid %s URL',
													'astra-sites'
												),
												sm.name
											) }
										</p>
									</div>
								) }
							</div>
						) ) }
					</div>
				) }

				{ socialMediaRender() }
			</div>
		</div>
	);
};

export default SocialMediaAdd;
