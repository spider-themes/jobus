import { useBlockProps } from '@wordpress/block-editor';


function Save( props ) {

    const { company_archive_layout } = props.attributes;

    const blockProps = useBlockProps.save();

    // Use template literals to properly interpolate the job_layout value
    const shortcode = `[company_page company_layout="${company_archive_layout}"]`;

    return (
        <>
            <div { ...blockProps }>
                { shortcode }
            </div>
        </>
    );
}


export default Save;
