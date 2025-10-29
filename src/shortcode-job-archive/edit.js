import { __ } from '@wordpress/i18n';
import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import {Fragment} from "react";
import {Preview} from "./preview";

function Edit( { attributes, setAttributes } ) {

    const { job_layout, preview } = attributes;
    const blockProps = useBlockProps();

    const isProUnlocked = window.jobusProUnlocked;

    const layoutOptions = [
        { label: __('Layout 01', 'jobus'), value: '1' },
        { label: isProUnlocked ? __('Layout 02', 'jobus') : __('Layout 02 (Pro)', 'jobus'), value: '2', disabled: !isProUnlocked },
        { label: isProUnlocked ? __('Layout 03', 'jobus') : __('Layout 03 (Pro)', 'jobus'), value: '3', disabled: !isProUnlocked },
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
                        label={__('Job Archive Layout', 'jobus')}
                        value={job_layout}
                        options={layoutOptions}
                        className={ job_layout > 1 ? 'jbs-block-pro' : '' }
                        onChange={(value) => setAttributes({ job_layout: value })}
                    />
                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
                [jobus_job_archive]
            </div>
        </Fragment>
    );
}

export default Edit;
