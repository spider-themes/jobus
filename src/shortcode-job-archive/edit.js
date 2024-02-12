import { __ } from '@wordpress/i18n';
import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

function Edit( { attributes, setAttributes } ) {
    const { job_layout } = attributes;
    const blockProps = useBlockProps();

    const layoutOptions = [
        { label: __('Layout 01', 'jobly'), value: '1' },
        { label: __('Layout 02', 'jobly'), value: '2' },
        { label: __('Layout 03', 'jobly'), value: '3' },
    ];

    return (
        <>
            <InspectorControls>
                <PanelBody
                    title={__('Filters', 'jobly')}
                    initialOpen={true}
                >
                    <SelectControl
                        label={__('Job Archive Layout', 'jobly')}
                        selected={job_layout}
                        options={layoutOptions}
                        onChange={(value) => setAttributes({ job_layout: value })}
                    />

                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
                [job_archive_page]
            </div>
        </>
    );
}

export default Edit;
