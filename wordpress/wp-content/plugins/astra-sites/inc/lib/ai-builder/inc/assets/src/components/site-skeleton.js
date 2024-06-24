const SiteSkeleton = ( { className } ) => {
	return (
		<div className={ `site-loading-skeleton ${ className }` }>
			<div className="grid grid-cols-12 gap-4">
				<div className="col-span-4">
					<div className="p-12 flex">
						<div className="w-72 h-12 bg-gray-300 animate-pulse"></div>
					</div>
				</div>
				<div className="col-span-8">
					<div className="p-12 flex justify-end gap-4">
						<div className="w-24 h-12 bg-gray-300 animate-pulse"></div>
						<div className="w-24 h-12 bg-gray-300 animate-pulse"></div>
						<div className="w-24 h-12 bg-gray-300 animate-pulse"></div>
						<div className="w-24 h-12 bg-gray-300 animate-pulse"></div>
						<div className="w-48 h-12 bg-gray-300 animate-pulse"></div>
					</div>
				</div>
				<div className="col-span-6">
					<div className="p-4 md:p-8 lg:p-12 flex flex-col h-full">
						<div className="h-48 bg-gray-300 animate-pulse"></div>
						<div className="my-4">
							<div className="h-5 bg-gray-300 animate-pulse"></div>
						</div>
						<div className="my-4">
							<div className="h-5 bg-gray-300 animate-pulse"></div>
						</div>
						<div className="my-12">
							<div className="w-60 h-16 bg-gray-300 animate-pulse"></div>
						</div>
					</div>
				</div>
				<div className="col-span-6">
					<div className="p-4 md:p-8 lg:p-12 flex justify-end gap-4">
						<div className="w-64 h-40 bg-gray-300 animate-pulse"></div>
					</div>
				</div>
				<div className="col-span-12">
					<div className="p-12 flex justify-evenly gap-4">
						<div className="w-full h-40 bg-gray-300 animate-pulse"></div>
						<div className="w-full h-40 bg-gray-300 animate-pulse"></div>
						<div className="w-full h-40 bg-gray-300 animate-pulse"></div>
					</div>
				</div>
			</div>
		</div>
	);
};

export default SiteSkeleton;
