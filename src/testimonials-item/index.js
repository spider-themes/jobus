import { registerBlockType } from '@wordpress/blocks';
import {useBlockProps, InnerBlocks, MediaUpload, MediaUploadCheck} from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import {SelectControl, TextareaControl, TextControl} from "@wordpress/components";
import {__} from "@wordpress/i18n";

import './style.scss'

registerBlockType('jobly/testimonials-item', {


    edit: ({ attributes, setAttributes }) => {

        const blockProps = useBlockProps({className: 'item'});
        const {rating} = attributes;

        return (
            <Fragment>

                <div {...blockProps}>


                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={(media) => setAttributes({ authorImage: media.url })}
                            type="image"
                            render={({ open }) => (
                                <div>
                                    <button onClick={open}>Select Author Image</button>
                                    {attributes.authorImage && <img src={attributes.authorImage} alt="Author" />}
                                </div>
                            )}
                        />
                    </MediaUploadCheck>


                    <TextControl
                        label={__('Author Name', 'jobly')}
                        value={attributes.authorName}
                        onChange={(val) =>
                            setAttributes({ authorName: val })
                        }
                    />

                    <TextControl
                        label={__('Location', 'jobly')}
                        value={attributes.location}
                        onChange={(val) =>
                            setAttributes({ location: val })
                        }
                    />

                    <TextareaControl
                        label={__('Review Contents', 'jobly')}
                        value={attributes.reviewContents}
                        onChange={(val) =>
                            setAttributes({ reviewContents: val })
                        }
                    />

                    <SelectControl
                        label={__('Rating', 'jobly')}
                        value={attributes.rating}
                        options={[
                            { label: '1', value: 1 },
                            { label: '2', value: 2 },
                            { label: '3', value: 3 },
                            { label: '4', value: 4 },
                            { label: '5', value: 5 },
                        ]}
                        onChange={(val) =>
                            setAttributes({ rating: val })
                        }
                    />

                </div>
            </Fragment>
        )

    },

    save: ({ attributes }) => {

        const blockProps = useBlockProps.save({className: 'item'});
        const { authorName, location, reviewContents, rating, authorImage } = attributes;

        return (
            <div {...blockProps}>

                <div className="feedback-block-four">
                    <div className="d-flex align-items-center">
                        <ul className="style-none d-flex rating">
                            {Array.from({length: rating}, (_, index) => (
                                <li key={index}><a href="#" tabIndex="0"><i className="bi bi-star-fill"></i></a></li>
                            ))}
                        </ul>
                        <div className="review-score"><span className="fw-500 text-dark">{rating}</span> {__('out of 5', 'jobly')}</div>
                    </div>
                    <blockquote>{reviewContents}</blockquote>
                    <div className="d-flex align-items-center">
                        {authorImage && <img src={authorImage} alt="Author" className="author-img rounded-circle"/>}
                        <div className="ms-3">
                            <div className="name fw-500 text-dark">{authorName}</div>
                            <span className="opacity-50">{location}</span>
                        </div>
                    </div>
                </div>
            </div>
        );
    },


});