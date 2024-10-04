import { __ } from '@wordpress/i18n';
import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import {Fragment} from "react";
import {Preview} from "./preview";

function Edit( { attributes, setAttributes } ) {

    const { candidate_layout, preview } = attributes;
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
        <Fragment>

            <InspectorControls>
                <PanelBody
                    title={__('Filters', 'jobus')}
                    initialOpen={true}
                >
                    <SelectControl
                        label={__('Candidate Archive Layout', 'jobus')}
                        value={candidate_layout}
                        options={layoutOptions}
                        onChange={(value) => setAttributes({ candidate_layout: value })}
                    />

                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
                [jobly_candidate_archive]
            </div>
        </Fragment>
    );
}

export default Edit;
