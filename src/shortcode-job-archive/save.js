import { useBlockProps, RichText } from '@wordpress/block-editor';


function Save( props ) {
    const { job_layout  } = props.attributes;
    const blockProps = useBlockProps.save();

    let is_docs_layout = job_layout ? 'docs_layout="'+job_layout+'"' : '';

    return (
        <>
            <div { ...blockProps }>
                [job_archive_page {is_docs_layout}]
            </div>
        </>
    );
}


export default Save;
