import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import {TextControl} from "@wordpress/components";

registerBlockType('jobly/group-testimonials', {

    edit: ({ attributes, setAttributes }) => {

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
    },

    save: ({ attributes }) => {

        const { uniqueId } = attributes;
        const blockProps = useBlockProps.save({className: 'company-review-slider'});

        return (
            <div {...blockProps}>
                <InnerBlocks.Content />
            </div>
        );
    },
});