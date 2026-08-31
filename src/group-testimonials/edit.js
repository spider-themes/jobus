import {InnerBlocks, useBlockProps} from "@wordpress/block-editor";
import {Fragment} from "@wordpress/element";

function Edit({attributes, setAttributes}) {

    const blockProps = useBlockProps({className: 'jbs-company-review-slider company-review-slider'});

    return (
        <Fragment>
            <div {...blockProps}>
                <InnerBlocks
                    allowedBlocks={['jobus/testimonials-item']}
                    template={[
                        ['jobus/testimonials-item'],
                    ]}
                />
            </div>
        </Fragment>
    );

}

export default Edit;