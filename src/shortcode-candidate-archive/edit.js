import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, BaseControl, Notice, Button } from '@wordpress/components';
import { Fragment, useState, useRef } from 'react';
import { createPortal } from '@wordpress/element';
import { Preview } from "./preview";

function Edit({ attributes, setAttributes }) {
    const { candidate_layout, preview } = attributes;
    const blockProps = useBlockProps();

    const pro_active = jobus_block_params.jobus_is_premium; // Check if Pro is active
    const [activeProNotice, setActiveProNotice] = useState(null);
    const [noticePosition, setNoticePosition] = useState({ top: 0, left: 0 });

    // Refs for each layout option
    const layoutRefs = useRef({});

    // Layout options
    const layoutOptions = [
        {
            label: __('Sidebar Filter Layout', 'jobus'),
            value: '1',
            img: jobus_block_params.jobus_image_dir + '/layout/candidate/archive-layout-1.png',
            pro: false
        },
        {
            label: __('Grid Layout', 'jobus'),
            value: '2',
            img: jobus_block_params.jobus_image_dir + '/layout/candidate/archive-layout-2.png',
            pro: true
        },
    ];

    // Show preview if preview mode
    if (preview) {
        return (
            <div className="block-preview">
                <Preview />
            </div>
        );
    }

    // Handle layout click
    const handleProClick = (option) => {
        if (option.pro && !pro_active) {
            if (layoutRefs.current[option.value]) {
                const rect = layoutRefs.current[option.value].getBoundingClientRect();
                setNoticePosition({
                    top: rect.top,
                    left: rect.left - 280 // Adjust distance from layout block
                });
                setActiveProNotice(option.value);
            }
            return;
        }
        setAttributes({ candidate_layout: option.value });
        setActiveProNotice(null);
    };

    return (
        <Fragment>
            <InspectorControls>
                <PanelBody
                    title={__('Candidate Archive Layout', 'jobus')}
                    initialOpen={true}
                >
                    <BaseControl label={__('Select Layout', 'jobus')}>
                        <div
                            className="jbs-layout-options"
                            style={{ display: 'flex', gap: '1rem', position: 'relative' }}
                        >
                            {layoutOptions.map(option => (
                                <div
                                    key={option.value}
                                    ref={el => layoutRefs.current[option.value] = el}
                                    className={`layout-option ${candidate_layout === option.value ? 'active' : ''} ${option.pro && !pro_active ? 'locked' : ''}`}
                                    onClick={() => handleProClick(option)}
                                    style={{
                                        position: 'relative',
                                        cursor: option.pro && !pro_active ? 'not-allowed' : 'pointer'
                                    }}
                                >
                                    <img
                                        src={option.img}
                                        alt={option.label}
                                    />
                                    
                                    <span className="layout-label" style={{ display: 'block', textAlign: 'center' }}>
                                        {option.label}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </BaseControl>
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                [jobus_candidate_archive]
            </div>

            {/* Floating Notice for Pro Feature */}
            {activeProNotice && layoutRefs.current[activeProNotice] && createPortal(
                <div
                    style={{
                        position: 'fixed',
                        top: noticePosition.top + 'px',
                        left: noticePosition.left + 'px',
                        width: '280px',
                        zIndex: 99999,
                    }}
                >
                    <Notice
                        status="info"
                        isDismissible
                        onRemove={() => setActiveProNotice(null)}
                        className="jbs-pro-notice"
                    >
                        <h4 style={{ margin: '0 0 0.5rem 0', fontWeight: 'bold' }}>
                            {__('Pro Feature', 'jobus')} 🔒
                        </h4>
                        <p style={{ marginBottom: '0.5rem' }}>
                            {__('This candidate layout is part of Jobus Pro. Upgrade today to unlock premium layouts and advanced customization options!', 'jobus')}
                        </p>
                        <Button
                            isPrimary
                            href={jobus_block_params.jobus_upgrade_url}
                            target="_blank"
                            style={{ marginTop: '0.25rem' }}
                            className="jobus-upgrade-button"
                        >
                            {__('Upgrade Now', 'jobus')}
                        </Button>
                    </Notice>
                </div>,
                document.body
            )}
        </Fragment>
    );
}

export default Edit;