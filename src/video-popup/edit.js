import {useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck} from "@wordpress/block-editor";
import {Panel, PanelBody, PanelRow, ColorPalette, TextControl, TabPanel } from "@wordpress/components";
import {Fragment} from "react";
import {__} from "@wordpress/i18n";

function Edit({ attributes, setAttributes }) {
    const onChangeBGColor = (hexColor) => {
        setAttributes({ icon_bg_color: hexColor });
    };

    const onChangeTextColor = (hexColor) => {
        setAttributes({ icon_color: hexColor });
    };

    const blockProps = useBlockProps({
        className: 'video-post jbs-d-flex jbs-align-items-center jbs-justify-content-centerjbs-mb-50',
        style: { backgroundImage: `url(${attributes.backgroundImage})`, backgroundRepeat: 'no-repeat', backgroundSize: 'cover', backgroundPosition: 'center center' },
    });

    return (
        <Fragment>
            <div className="company-details">
                <div className="details-post-data">

                    <div {...blockProps}>

                        <a href={attributes.videoUrl}
                           className="fancybox rounded-circle video-icon tran3s jbs-text-center"
                           data-fancybox="" // Add your custom attribute here
                           style={{backgroundColor: attributes.icon_bg_color}}>
                            <i className="bi bi-play-fill" style={{color: attributes.icon_color}}></i>
                        </a>

                        <MediaUploadCheck>
                            <MediaUpload
                                onSelect={(media) => setAttributes({ backgroundImage: media.url })}
                                allowedTypes={['image']}
                                value={attributes.backgroundImage}
                                render={({ open }) => (
                                    <Fragment>
                                        <button onClick={open}>{__('Upload Background Image', 'jobus')}</button>
                                        {attributes.backgroundImage && (
                                            <button onClick={() => setAttributes({ backgroundImage: '' })}>
                                                {__('Remove Image', 'jobus')}
                                            </button>
                                        )}
                                    </Fragment>
                                )}
                            />
                        </MediaUploadCheck>
                        <TextControl
                            label={__('Video URL', 'jobus')}
                            value={attributes.videoUrl}
                            onChange={(val) => setAttributes({videoUrl: val})}
                        />
                    </div>

                </div>
            </div>


        </Fragment>
    );
}

export default Edit;