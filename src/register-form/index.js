import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import './style.scss'

/**
 * Internal dependencies
 */
import Edit from './edit';
import Save from './save';

/**
 * Block Registration
 */
registerBlockType(metadata.name, {
    edit: Edit,
    save: Save,
});
