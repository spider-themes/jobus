import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUploadCheck, MediaUpload, TextControl, TextareaControl, SelectControl } from '@wordpress/block-editor';
import { Fragment } from 'react';

// Testimonial Item Component
const TestimonialItem = ({ testimonial }) => (
    <div className="item">
        <div className="feedback-block-four">
            <div className="d-flex align-items-center">
                <ul className="style-none d-flex rating">
                    {Array.from({ length: testimonial.rating }, (_, index) => (
                        <li key={index}><a href="#" tabIndex="0"><i className="bi bi-star-fill"></i></a></li>
                    ))}
                </ul>
                <div className="review-score"><span className="fw-500 text-dark">{testimonial.rating}</span> out of 5</div>
            </div>
            <blockquote>{testimonial.review_content}</blockquote>
            <div className="d-flex align-items-center">
                <div className="ms-3">
                    <div className="name fw-500 text-dark">{testimonial.author_name}</div>
                    <span className="opacity-50">{testimonial.location}</span>
                </div>
            </div>
        </div>
    </div>
);

registerBlockType('jobly/testimonials', {
    title: __('Testimonials', 'jobly'),
    icon: 'testimonial',
    category: 'common',
    attributes: {
        testimonials: {
            type: 'array',
            default: [
                {
                    author_name: __('Arif Rahman', 'jobly'),
                    location: __('Bangladesh', 'jobly'),
                    review_content: '',
                    rating: 5,
                },
            ],
        },
    },
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();

        return (
            <Fragment>
                <div {...blockProps}>

                    <TextControl
                        label={__('Author Name', 'jobly')}
                        value={attributes.testimonials[0].author_name}
                        onChange={(val) => setAttributes({ 'testimonials[0].author_name': val })}
                    />
                    <TextControl
                        label={__('Location', 'jobly')}
                        value={attributes.testimonials[0].location}
                        onChange={(val) => setAttributes({ 'testimonials[0].location': val })}
                    />
                    <TextareaControl
                        label={__('Review Content', 'jobly')}
                        value={attributes.testimonials[0].review_content}
                        onChange={(val) => setAttributes({ 'testimonials[0].review_content': val })}
                    />
                    <SelectControl
                        label={__('Select Rating', 'jobly')}
                        value={attributes.testimonials[0].rating}
                        options={[
                            { label: '1', value: 1 },
                            { label: '2', value: 2 },
                            { label: '3', value: 3 },
                            { label: '4', value: 4 },
                            { label: '5', value: 5 },
                        ]}
                        onChange={(val) => setAttributes({ 'testimonials[0].rating': val })}
                    />
                </div>
            </Fragment>
        );
    },
    save: ({ attributes }) => {
        const blockProps = useBlockProps.save();

        return (
            <div {...blockProps}>
                <h3>Company Reviews</h3>
                <div className="company-review-slider">
                    {attributes.testimonials.map((testimonial, index) => (
                        <TestimonialItem key={index} testimonial={testimonial} />
                    ))}
                </div>
            </div>
        );
    },
});