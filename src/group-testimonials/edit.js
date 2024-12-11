import {InnerBlocks, useBlockProps} from "@wordpress/block-editor";
import {Fragment} from "@wordpress/element";
function Edit({attributes, setAttributes}) {

    const blockProps = useBlockProps({className: 'company-review-slider'});

    return (
        <Fragment>
            <div {...blockProps}>


                <InnerBlocks
                    allowedBlocks={['jobly/testimonials-item']}
                    template={[
                        ['jobly/testimonials-item'],
                    ]}
                />

            </div>
        </Fragment>
    );

}

export default Edit;