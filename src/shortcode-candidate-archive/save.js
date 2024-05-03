import { useBlockProps } from '@wordpress/block-editor';
import {Fragment} from "react";


function Save( props ) {

    const { candidate_layout } = props.attributes;
    const blockProps = useBlockProps.save();
    const shortcode = `[jobly_candidate_archive candidate_layout="${candidate_layout}"]`;

    return (
        <Fragment>
            <div { ...blockProps }>
                { shortcode }
            </div>
        </Fragment>
    );
}


export default Save;
