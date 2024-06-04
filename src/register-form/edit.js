import {InspectorControls, useBlockProps} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import {Fragment} from "react";
import {PanelBody, SelectControl} from "@wordpress/components";

function Edit({ attributes, setAttributes }) {
    const { candidate_username, candidate_email, candidate_pass, candidate_confirm_pass } = attributes;
    const blockProps = useBlockProps();

    return (

        <Fragment>

            <InspectorControls>
                <PanelBody
                    title={__('Filters', 'jobly')}
                    initialOpen={true}
                >
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                [jobly_register_form]
            </div>

        </Fragment>
    );
}

export default Edit;