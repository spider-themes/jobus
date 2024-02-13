import { __ } from '@wordpress/i18n';
import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

function Edit( { attributes, setAttributes } ) {

    const { company_archive_layout } = attributes;
    const blockProps = useBlockProps();

    const layoutOptions = [
        { label: __('Layout 01', 'jobly'), value: '1' },
        { label: __('Layout 02', 'jobly'), value: '2' },
    ];

    return (
        <>
            <InspectorControls>
                <PanelBody
                    title={__('Filters', 'jobly')}
                    initialOpen={true}
                >
                    <SelectControl
                        label={__('Company Archive Layout', 'jobly')}
                        value={company_archive_layout}
                        options={layoutOptions}
                        onChange={(value) => setAttributes({ company_archive_layout: value })}
                    />

                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
                [company_page]
            </div>
        </>
    );
}

export default Edit;
