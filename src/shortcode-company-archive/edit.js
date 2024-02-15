import { __ } from '@wordpress/i18n';
import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import {Preview} from "../shortcode-job-archive/preview";

function Edit( { attributes, setAttributes } ) {

    const { company_layout, preview } = attributes;
    const blockProps = useBlockProps();

    const layoutOptions = [
        { label: __('Layout 01', 'jobly'), value: '1' },
        { label: __('Layout 02', 'jobly'), value: '2' },
    ];

    // Preview image for this block
    if ( preview ) {
        return (
            <div className="block-preview">
                <Preview/>
            </div>
        )
    }

    return (
        <>
            <InspectorControls>
                <PanelBody
                    title={__('Filters', 'jobly')}
                    initialOpen={true}
                >
                    <SelectControl
                        label={__('Company Archive Layout', 'jobly')}
                        value={company_layout}
                        options={layoutOptions}
                        onChange={(value) => setAttributes({ company_layout: value })}
                    />

                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
                [jobly_company_archive]
            </div>
        </>
    );
}

export default Edit;
