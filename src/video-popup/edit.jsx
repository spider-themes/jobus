// jobly/blocks/video-popup/edit.jsx
import { TextControl, CheckboxControl, MediaUpload, InspectorControls, RichText } from '@wordpress/block-editor';
import { ColorPalette, useBlockProps } from '@wordpress/components';

const Edit = (props) => {

    const { attributes, setAttributes } = props;

    const onChangeBGColor = hex

    return (

        <div {...useBlockProps()}>
            <InspectorControls key="setting">
                <div id="gutenpride-controls">
                    <fieldset>
                        <legend className="blocks-base-control__label">
                            {__('Background color', 'gutenpride')}
                        </legend>
                        <ColorPalette // Element Tag for Gutenberg standard colour selector
                                onChange={onChangeBGColor} // onChange event callback
                        />
                    </fieldset>
                    <fieldset>
                        <legend className="blocks-base-control__label">
                            {__('Text color', 'gutenpride')}
                        </legend>
                        <ColorPalette // Element Tag for Gutenberg standard colour selector
                            onChange={onChangeTextColor} // onChange event callback
                        />
                    </fieldset>
                </div>
            </InspectorControls>
            <TextControl
                value={attributes.message}
                onChange={(val) => setAttributes({message: val})}
                style={{
                    backgroundColor: attributes.bg_color,
                    color: attributes.text_color,
                }}
            />
        </div>

    );
};

export default Edit;