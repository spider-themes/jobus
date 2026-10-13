import { useBlockProps } from '@wordpress/block-editor';
import {Fragment} from "react";

function Save( props ) {

    const { job_layout, padding_top, padding_bottom } = props.attributes;
    const currentLayout = job_layout || '1';
    const blockProps = useBlockProps.save();

    const shortcode = `[jobus_job_archive job_layout="${currentLayout}"]`;

    const formatPadding = (val) => {
        if (!val && val !== 0) return '';
        const str = String(val).trim();
        return isNaN(Number(str)) ? str : `${str}px`;
    };

    const pt = formatPadding(padding_top);
    const pb = formatPadding(padding_bottom);
    const hasPadding = Boolean(pt || pb);

    const customStyles = hasPadding ? `
        .wp-block-jobus-shortcode-job-archive .jbs-job-classic,
        .wp-block-jobus-shortcode-job-archive .jbs-job-topbar,
        .wp-block-jobus-shortcode-job-archive .jbs-job-popup {
            ${pt ? `padding-top: ${pt} !important;` : ''}
            ${pb ? `padding-bottom: ${pb} !important;` : ''}
        }
    ` : '';

    return (
        <Fragment>
            <div { ...blockProps }>
                { hasPadding && <style dangerouslySetInnerHTML={{ __html: customStyles }} /> }
                { shortcode }
            </div>
        </Fragment>
    );
}

export default Save;
