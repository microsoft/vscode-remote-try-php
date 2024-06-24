import { Fragment, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { ChevronDownIcon } from '@heroicons/react/24/outline';
import DropdownList from './dropdown-list';

const imageDir = aiBuilderVars.imageDir;

const PageBuilderDropdown = () => {
	const buildersList = [
		{
			id: 'gutenberg',
			title: __( 'Block Editor', 'ai-builder' ),
			image: `${ imageDir }block-editor.svg`,
		},
		{
			id: 'elementor',
			title: __( 'Elementor', 'ai-builder' ),
			image: `${ imageDir }elementor.svg`,
		},
		{
			id: 'beaver-builder',
			title: __( 'Beaver Builder', 'ai-builder' ),
			image: `${ imageDir }beaver-builder.svg`,
		},
		{
			id: 'ai-builder',
			title: __( 'AI Website Builder', 'ai-builder' ),
			image: `${ imageDir }ai-builder.svg`,
		},
	];
	const [ selectedBuilder, setSelectedBuilder ] = useState(
		buildersList.at( -1 )
	);

	return (
		<DropdownList
			by="id"
			value={ selectedBuilder }
			onChange={ ( value ) => {
				if ( value.id === buildersList.at( -1 ).id ) {
					return;
				}
				setSelectedBuilder( value );
				window.location = `${ aiBuilderVars.adminUrl }themes.php?page=starter-templates&builder=${ value.id }`;
			} }
		>
			<div className="relative">
				<DropdownList.Button className="flex items-center justify-between gap-2 min-w-[190px] w-fit py-[28px] px-[20px] border-y-0 border-r-0 border-l border-border-primary shadow-none bg-transparent rounded-none text-sm text-zip-body-text cursor-pointer">
					<div className="flex items-center gap-2">
						<img
							className="w-5 h-5"
							src={ selectedBuilder.image }
							alt={ selectedBuilder.title }
						/>
						<span className="truncate">
							{ selectedBuilder.title }
						</span>
					</div>
					<ChevronDownIcon className="w-5 h-5 text-zip-body-text" />
				</DropdownList.Button>
				<DropdownList.Options className="mt-0 p-0 rounded-t-none bg-white shadow-[1px_2px_5px_1px_rgba(0,0,0,0.15)]">
					{ buildersList.map( ( builder ) => (
						<DropdownList.Option
							key={ builder.id }
							as={ Fragment }
							value={ builder }
							className="py-3 px-2 hover:bg-[#F9FAFB] cursor-pointer"
						>
							<div className="flex items-center gap-2 text-sm font-normal">
								<img
									className="w-5 h-5"
									src={ builder.image }
									alt={ builder.title }
								/>
								<span>{ builder.title }</span>
							</div>
						</DropdownList.Option>
					) ) }
				</DropdownList.Options>
			</div>
		</DropdownList>
	);
};

export default PageBuilderDropdown;
