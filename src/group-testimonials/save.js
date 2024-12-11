import {InnerBlocks, useBlockProps} from "@wordpress/block-editor";
function Save({attributes}) {
    const { uniqueId } = attributes;
    const blockProps = useBlockProps.save({className: 'company-review-slider'});

    return (
        <div {...blockProps}>
            <InnerBlocks.Content />
        </div>
    );
}

export default Save;