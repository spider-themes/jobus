import { useBlockProps } from '@wordpress/block-editor';
import {Fragment} from "react";

function Save({ save }) {

    const blockProps = useBlockProps.save({className: 'registration-section position-relative pt-100 lg-pt-80 pb-150 lg-pb-80'});

    return (
        <Fragment>
            <div {...blockProps}>
                <div className="container">
                    [jobly_register_form]
                </div>
            </div>
        </Fragment>
    );
}

export default Save;