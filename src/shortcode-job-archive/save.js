import { useBlockProps } from '@wordpress/block-editor';
import {Fragment} from "react";


function Save( props ) {

    const { job_layout } = props.attributes;
    const blockProps = useBlockProps.save();
    const shortcode = `[jobus_job_archive job_layout="${job_layout}"]`;

    return (
        <Fragment>
            <div { ...blockProps }>
                { shortcode }
            </div>
        </Fragment>
    );
}


export default Save;
