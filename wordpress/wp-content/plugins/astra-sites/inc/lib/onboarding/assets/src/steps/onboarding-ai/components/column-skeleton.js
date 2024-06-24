import DotsLoader from './dots-loader';
import { __ } from '@wordpress/i18n';

const ColumnSkeleton = () => {
	return (
		<div className="w-full relative overflow-visible">
			<div className="aspect-[164/179]" />
			<div className="h-14 w-full" />
			<div className="absolute inset-0 flex flex-col bg-white items-center border border-border-tertiary border-solid">
				<div className="w-full flex items-center p-4 space-x-5">
					<div
						data-placeholder
						className="h-5 w-10 rounded-full overflow-hidden relative bg-gray-200"
					/>
					<div className="w-full flex justify-between items-center gap-2">
						<div
							data-placeholder
							className="h-5 w-1/3 overflow-hidden relative bg-gray-200 rounded-md"
						/>
						<div
							data-placeholder
							className="h-5 w-1/3 overflow-hidden relative bg-gray-200 rounded-md"
						/>
						<div
							data-placeholder
							className="h-5 w-1/3 overflow-hidden relative bg-gray-200 rounded-md"
						/>
					</div>
				</div>
				<div
					data-placeholder
					className="flex items-center justify-center gap-2 h-52 w-full overflow-hidden relative bg-gray-200"
				>
					<DotsLoader />
					<p className="!text-base !font-normal !text-zip-app-heading select-none">
						{ __( 'Generating preview…', 'astra-sites' ) }
					</p>
				</div>

				<div className="w-full flex flex-col p-4 space-y-2">
					<div
						data-placeholder
						className="flex h-3 w-10/12 overflow-hidden relative bg-gray-200 rounded"
					/>
					<div
						data-placeholder
						className="flex h-3 w-10/12 overflow-hidden relative bg-gray-200 rounded"
					/>
					<div
						data-placeholder
						className="flex h-3 w-1/2 overflow-hidden relative bg-gray-200 rounded"
					/>
				</div>
				<div className="w-full h-px  overflow-hidden relative bg-gray-200 m-4" />
				<div className="flex justify-between items-center p-4 w-full gap-3">
					<div
						data-placeholder
						className="h-14 w-1/3 rounded-md overflow-hidden relative bg-gray-200"
					/>
					<div
						data-placeholder
						className="h-14 w-1/3 rounded-md overflow-hidden relative bg-gray-200"
					/>
					<div
						data-placeholder
						className="h-14 w-1/3 rounded-md overflow-hidden relative bg-gray-200"
					/>
				</div>
				<div className="flex justify-between items-end flex-1 w-full">
					<div
						data-placeholder
						className="h-5 w-full overflow-hidden relative bg-gray-200"
					/>
				</div>
			</div>
		</div>
	);
};

export default ColumnSkeleton;
