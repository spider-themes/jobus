import { __ } from '@wordpress/i18n';
import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import {Preview} from "../shortcode-job-archive/preview";

function Edit( { attributes, setAttributes } ) {

    const { company_layout, preview } = attributes;
    const blockProps = useBlockProps();

    const layoutOptions = [
        { label: __('Layout 01', 'jobus'), value: '1' },
        { label: __('Layout 02', 'jobus'), value: '2' },
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
                    title={__('Filters', 'jobus')}
                    initialOpen={true}
                >
                    <SelectControl
                        label={__('Company Archive Layout', 'jobus')}
                        value={company_layout}
                        options={layoutOptions}
                        onChange={(value) => setAttributes({ company_layout: value })}
                    />

                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
                [jobus_company_archive]
            </div>
        </>
    );
}

export default Edit;
