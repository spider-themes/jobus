import { useBlockProps } from '@wordpress/block-editor';
function Save( props ) {

    const { company_layout } = props.attributes;

    const blockProps = useBlockProps.save();

    // Use template literals to properly interpolate the job_layout value
    const shortcode = `[jobus_company_archive company_layout="${company_layout}"]`;

    return (
        <>
            <div { ...blockProps }>
                { shortcode }
            </div>
        </>
    );
}


export default Save;
