<style>
    .wp-block-jobly-group-block {
        /* Default styles without border */
    }

    .wp-block-jobly-group-block.has-border {
        /* Apply border styles */
        border-width: var(--borderWidth, 1px);
        border-color: var(--borderColor, #000);
        border-style: solid;
    }

</style>



<?php
add_action( 'init', 'jobly_register_blocks' );

/**
 * Register blocks
 */
function jobly_register_blocks() {

    register_block_type('jobly/group-block', array(
        'attributes' => array(
            'customBorder' => array(
                'type'    => 'string',
                'default' => '',
            ),
        ),
        'render_callback' => 'jobly_render_block',
    ));

}

/**
 * Render block
 *
 * @param array $attributes
 * @param string $content
 * @return string
 */
function jobly_render_block( $attributes, $content ) {

    $customBorder = $attributes[ 'customBorder' ];

    $output = '<div class="group-block" style="border: ' . $customBorder . ';">' . $content . '</div>';

    return $output;
}



?>

<script type="text/javascript">

    'use strict'

    // Import the necessary modules
    import { registerBlockStyle } from '@wordpress/blocks';
    import { PanelBody, ToggleControl, ColorPicker, SelectControl } from '@wordpress/components';
    import { InspectorControls } from '@wordpress/editor';

    // Register block style variations
    registerBlockStyle('jobly/group-block', [
        {
            name: 'custom-border',
            label: 'Custom Border',
        },
        // Add more styles as needed
    ]);

    // Register your Group Block
    registerBlockType('jobly/group-block', {
        // ...

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { borderEnabled, borderColor, borderWidth } = attributes;

            return (
                <div className={props.className}>
                    <InspectorControls>
                        <PanelBody title="Custom Border Settings">
                            <ToggleControl
                                label="Enable Border"
                                checked={borderEnabled}
                                onChange={() => setAttributes({ borderEnabled: !borderEnabled })}
                            />
                            {borderEnabled && (
                                <div>
                                    <ColorPicker
                                        label="Border Color"
                                        color={borderColor}
                                        onChangeComplete={color => setAttributes({ borderColor: color.hex })}
                                    />
                                    <SelectControl
                                        label="Border Width"
                                        value={borderWidth}
                                        options={[
                                            { label: '1px', value: '1px' },
                                            { label: '2px', value: '2px' },
                                            { label: '3px', value: '3px' },
                                            // Add more options
                                        ]}
                                        onChange={value => setAttributes({ borderWidth: value })}
                                    />
                                </div>
                            )}
                        </PanelBody>
                    </InspectorControls>
                    {/* Block content */}
                </div>
            );
        },

        // ...
    });


</script>
