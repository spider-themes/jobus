// jobly/blocks/video-popup/index.js
import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, ColorPalette, MediaUpload, MediaUploadCheck, InspectorControls } from '@wordpress/block-editor';
import { TextControl, Panel, PanelBody, PanelRow } from '@wordpress/components';
import { Fragment } from 'react';

registerBlockType('jobly/video-popup', {
    apiVersion: 3,
    title: __('Jobly Video Popup', 'jobly'), // Updated block title
    icon: 'universal-access-alt',
    category: 'jobly-blocks',

    attributes: {
        icon_bg_color: { type: 'string', default: '#FF4646' },
        icon_color: { type: 'string', default: '#ffffff' },
        videoUrl: { type: 'string', default: '#' }, // New attribute for video URL
        backgroundImage: { type: 'string', default: '#' }, // New attribute for background image
    },

    edit: ({ attributes, setAttributes }) => {
        const onChangeBGColor = (hexColor) => {
            setAttributes({ icon_bg_color: hexColor });
        };

        const onChangeTextColor = (hexColor) => {
            setAttributes({ icon_color: hexColor });
        };

        const blockProps = useBlockProps({
            className: 'video-post d-flex align-items-center justify-content-center mb-50',
            style: { backgroundImage: `url(${attributes.backgroundImage})`, backgroundRepeat: 'no-repeat', backgroundSize: 'cover', backgroundPosition: 'center center' },
        });

        return (
            <Fragment>

                <InspectorControls>

                    <Panel>
                        <PanelBody title={__('Icon Controls', 'jobly')}>

                            <PanelRow>
                                <ColorPalette
                                    label={__('Icon Color', 'jobly')}
                                    onChange={onChangeTextColor}
                                    value={attributes.icon_color}
                                />
                            </PanelRow>

                            <PanelRow>
                                <ColorPalette
                                    label={__('Background Color', 'jobly')}
                                    onChange={onChangeBGColor}
                                    value={attributes.icon_bg_color}
                                />
                            </PanelRow>

                        </PanelBody>
                    </Panel>

                </InspectorControls>

                <div className="company-details">
                    <div className="details-post-data">

                        <div {...blockProps}>

                            <a href={attributes.videoUrl}
                               className="fancybox rounded-circle video-icon tran3s text-center"
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
                                            <button onClick={open}>{__('Upload Background Image', 'jobly')}</button>
                                            {attributes.backgroundImage && (
                                                <button onClick={() => setAttributes({ backgroundImage: '' })}>
                                                    {__('Remove Image', 'jobly')}
                                                </button>
                                            )}
                                        </Fragment>
                                    )}
                                />
                            </MediaUploadCheck>
                            <TextControl
                                label={__('Video URL', 'jobly')}
                                value={attributes.videoUrl}
                                onChange={(val) => setAttributes({videoUrl: val})}
                            />
                        </div>

                    </div>
                </div>


            </Fragment>
        );
    },

    save: ({attributes}) => {
        const blockProps = useBlockProps.save({
            className: 'video-post d-flex align-items-center justify-content-center mb-50',
            style: {
                backgroundImage: `url(${attributes.backgroundImage})`,
                backgroundRepeat: 'no-repeat',
                backgroundSize: 'cover',
                backgroundPosition: 'center center'
            },
        });

        return (
            <div {...blockProps}>
                <a href={attributes.videoUrl} className="fancybox rounded-circle video-icon tran3s text-center" style={
                    {
                        backgroundColor: attributes.icon_bg_color,
                    }
                }>
                    <i className="bi bi-play-fill" style={{color: attributes.icon_color}}></i>
                </a>
            </div>
        );
    },
});