import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, FormTokenField } from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import ServerSideRender from '@wordpress/server-side-render';

function Edit( { attributes, setAttributes } ) {
	const { categories = [] } = attributes;
	const blockProps = useBlockProps();
	const categoryMap = window.jobus_block_params?.job_categories || {};
	const slugByName = {};

	Object.keys( categoryMap ).forEach( ( slug ) => {
		slugByName[ categoryMap[ slug ] ] = slug;
	} );

	const selectedNames = categories
		.map( ( slug ) => categoryMap[ slug ] )
		.filter( Boolean );

	return (
		<Fragment>
			<InspectorControls>
				<PanelBody title={ __( 'Categories', 'jobus' ) } initialOpen={ true }>
					<FormTokenField
						label={ __( 'Job categories', 'jobus' ) }
						value={ selectedNames }
						suggestions={ Object.values( categoryMap ) }
						onChange={ ( tokens ) => {
							const slugs = tokens
								.map( ( token ) => slugByName[ token ] )
								.filter( Boolean );
							setAttributes( { categories: slugs } );
						} }
						help={ __( 'Leave empty to list every category that has open jobs.', 'jobus' ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<ServerSideRender
					block="jobus/job-category-tabs"
					attributes={ attributes }
				/>
			</div>
		</Fragment>
	);
}

export default Edit;
