import {MediaUpload, MediaUploadCheck, useBlockProps} from "@wordpress/block-editor";
import {Fragment} from "@wordpress/element";
import {SelectControl, TextareaControl, TextControl} from "@wordpress/components";
import {__} from "@wordpress/i18n";

function Edit({ attributes, setAttributes }) {

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
}

export default Edit;