import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, BaseControl, Notice, Button, TextControl, BoxControl } from '@wordpress/components';
import { Fragment, useState, useRef } from 'react';
import { createPortal } from '@wordpress/element';
import { Preview } from "./preview";

function Edit({ attributes, setAttributes }) {
    const { job_layout, preview, padding_top, padding_bottom } = attributes;
    const blockProps = useBlockProps();

    const pro_active = jobus_block_params.jobus_is_premium; // Check if Pro is active
    const [activeProNotice, setActiveProNotice] = useState(null);
    const [noticePosition, setNoticePosition] = useState({ top: 0, left: 0 });

    const currentLayout = job_layout || '1';

    // Refs for each layout option
    const layoutRefs = useRef({});

    // Layout options
    const layoutOptions = [
        {
            label: __('Sidebar Filter Layout', 'jobus'),
            value: '1',
            img: jobus_block_params.jobus_image_dir + '/layout/job/archive-layout-1.png',
            pro: false
        },
        {
            label: __('List Layout', 'jobus'),
            value: '2',
            img: jobus_block_params.jobus_image_dir + '/layout/job/archive-layout-2.png',
            pro: true
        },
        {
            label: __('Grid Layout', 'jobus'),
            value: '3',
            img: jobus_block_params.jobus_image_dir + '/layout/job/archive-layout-3.png',
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

    // Handle click on layout option
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
        setAttributes({ job_layout: option.value });
        setActiveProNotice(null);
    };

    const shortcode = `[jobus_job_archive job_layout="${currentLayout}"]`;

    return (
        <Fragment>
            <InspectorControls>
                <PanelBody
                    title={__('Filters', 'jobus')}
                    initialOpen={true}
                >
                    <BaseControl label={__('Select Layout', 'jobus')}>
                        <div className="jbs-layout-options" style={{ display: 'flex', flexDirection: 'column', gap: '1rem', position: 'relative' }}>
                            {layoutOptions.map(option => {
                                const isSelected = currentLayout === option.value;
                                const isLocked = option.pro && !pro_active;
                                return (
                                    <div
                                        key={option.value}
                                        ref={el => layoutRefs.current[option.value] = el}
                                        className={`jbs-layout-option layout-option ${isSelected ? 'active' : ''} ${isLocked ? 'jbs-locked locked' : ''}`}
                                        onClick={() => handleProClick(option)}
                                        style={{
                                            position: 'relative',
                                            cursor: isLocked ? 'not-allowed' : 'pointer',
                                            border: isSelected ? '2px solid #007cba' : '1px solid #ddd',
                                            borderRadius: '4px',
                                            padding: '4px',
                                            background: isSelected ? '#f0f7fc' : '#fff',
                                            transition: 'all 0.2s ease',
                                        }}
                                    >
                                        <img src={option.img} alt={option.label} style={{ width: '100%', height: 'auto', display: 'block' }} />
                                        <span className="jbs-layout-label layout-label" style={{ display: 'block', textAlign: 'center', padding: '6px 0', fontWeight: isSelected ? '600' : '400', color: isSelected ? '#007cba' : 'inherit' }}>
                                            {option.label}
                                        </span>
                                    </div>
                                );
                            })}
                        </div>
                    </BaseControl>
                </PanelBody>
            </InspectorControls>

            {/* Styles Tab */}
            <InspectorControls group="styles">
                <PanelBody
                    title={__('Page Padding', 'jobus')}
                    initialOpen={true}
                >
                    {BoxControl ? (
                        <BoxControl
                            label={__('Padding', 'jobus')}
                            values={{
                                top: padding_top || '',
                                bottom: padding_bottom || '',
                            }}
                            onChange={(nextValues) => {
                                setAttributes({
                                    padding_top: nextValues?.top !== undefined ? nextValues.top : '',
                                    padding_bottom: nextValues?.bottom !== undefined ? nextValues.bottom : '',
                                });
                            }}
                            sides={['top', 'bottom']}
                            allowReset={true}
                        />
                    ) : (
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '10px' }}>
                            <TextControl
                                label={__('Padding Top', 'jobus')}
                                value={padding_top || ''}
                                placeholder="e.g. 100px"
                                onChange={(val) => setAttributes({ padding_top: val })}
                            />
                            <TextControl
                                label={__('Padding Bottom', 'jobus')}
                                value={padding_bottom || ''}
                                placeholder="e.g. 140px"
                                onChange={(val) => setAttributes({ padding_bottom: val })}
                            />
                        </div>
                    )}
                    <p style={{ color: '#757575', fontSize: '12px', marginTop: '12px', marginBottom: 0 }}>
                        {__('Overrides global archive page padding.', 'jobus')}
                    </p>
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                {shortcode}
            </div>

            {/* Portal Notice */}
            {activeProNotice && layoutRefs.current[activeProNotice] && createPortal(
                <div style={{
                    position: 'fixed',
                    top: noticePosition.top + 'px',
                    left: noticePosition.left + 'px',
                    width: '280px',
                    zIndex: 99999,
                }}>
                    <Notice
                        status="info"
                        isDismissible
                        onRemove={() => setActiveProNotice(null)}
                        className="jbs-pro-notice"
                    >
                        
                        <h4 style={{ margin: '0 0 0.5rem 0', fontWeight: 'bold' }}>
                            {__('Pro Feature', 'jobus')}
                            <span className="jbs-pro-lock" style={{ marginLeft: '0.25rem' }}>🔒</span>
                        </h4>

                        <p style={{ marginBottom: '0.5rem' }}>
                            {__('This layout is a Pro feature. Upgrade to Jobus Pro to unlock advanced layouts and premium styling options!', 'jobus')}
                        </p>
                        <Button
                            isPrimary
                            href={ jobus_block_params.jobus_upgrade_url }
                            target="_blank"
                            style={{ marginTop: '0.25rem' }}
                            className="jbs-upgrade-button"
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