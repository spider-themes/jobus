const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const fs = require( 'fs' );
const path = require( 'path' );
const webpack = require( 'webpack' );

const scssDir = path.resolve( __dirname, 'assets/scss' );

const getNestedStyleEntries = ( baseDir, prefix ) => {
	const entries = {};

	if ( ! fs.existsSync( baseDir ) ) {
		return entries;
	}

	const walk = ( currentDir ) => {
		fs.readdirSync( currentDir, { withFileTypes: true } ).forEach( ( entry ) => {
			if ( entry.name.startsWith( '.' ) ) {
				return;
			}

			const absolutePath = path.join( currentDir, entry.name );

			if ( entry.isDirectory() ) {
				walk( absolutePath );
				return;
			}

			if ( ! entry.isFile() || path.extname( entry.name ) !== '.scss' || entry.name.startsWith( '_' ) ) {
				return;
			}

			const relativePath = path.relative( baseDir, absolutePath ).split( path.sep ).join( '/' );
			entries[ `${ prefix }/${ relativePath.replace( /\.scss$/, '' ) }` ] = absolutePath;
		} );
	};

	walk( baseDir );

	return entries;
};

const styleEntries = getNestedStyleEntries( scssDir, 'css' );
const styleEntryNames = Object.keys( styleEntries );
const basePlugins = ( defaultConfig.plugins || [] ).filter(
	( plugin ) => plugin?.constructor?.name !== 'RtlCssPlugin'
);

class CleanupStyleArtifactsPlugin {
	apply( compiler ) {
		compiler.hooks.thisCompilation.tap( 'CleanupStyleArtifactsPlugin', ( compilation ) => {
			compilation.hooks.processAssets.tap(
				{
					name: 'CleanupStyleArtifactsPlugin',
					stage: compiler.webpack.Compilation.PROCESS_ASSETS_STAGE_SUMMARIZE,
				},
				() => {
					styleEntryNames.forEach( ( entryName ) => {
						[
							`${ entryName }.js`,
							`${ entryName }.js.map`,
							`${ entryName }.js.LICENSE.txt`,
							`${ entryName }.asset.php`,
						].forEach( ( assetName ) => {
							if ( compilation.getAsset( assetName ) ) {
								compilation.deleteAsset( assetName );
							}
						} );
					} );
				}
			);
		} );
	}
}

const matchesStyleRule = ( rule ) => {
	if ( ! rule?.test ) {
		return false;
	}

	if ( rule.test instanceof RegExp ) {
		return [ 'file.css', 'file.pcss', 'file.scss', 'file.sass' ].some( ( file ) => rule.test.test( file ) );
	}

	return /css|pcss|s[ac]ss/i.test( String( rule.test ) );
};

const enableStyleSourceMaps = ( rules = [] ) =>
	rules.map( ( rule ) => {
		if ( ! Array.isArray( rule.use ) || ! matchesStyleRule( rule ) ) {
			return rule;
		}

		return {
			...rule,
			use: rule.use.map( ( use ) => {
				if ( typeof use === 'string' || ! use?.loader ) {
					return use;
				}

				const options = { ...( use.options || {} ) };

				if (
					use.loader.includes( '/css-loader/' ) ||
					use.loader.includes( '/postcss-loader/' ) ||
					use.loader.includes( '/sass-loader/' )
				) {
					options.sourceMap = true;
				}

				if ( use.loader.includes( '/css-loader/' ) ) {
					options.url = false;
				}

				if ( use.loader.includes( 'postcss-loader' ) && options.postcssOptions ) {
					options.postcssOptions = {
						...options.postcssOptions,
						sourceMap: true,
					};
				}

				return {
					...use,
					options,
				};
			} ),
		};
	} );

module.exports = {
	...defaultConfig,
	devtool: 'source-map',
	module: {
		...( defaultConfig.module || {} ),
		rules: enableStyleSourceMaps( defaultConfig.module?.rules || [] ),
	},
	plugins: [
		...basePlugins,
		new CleanupStyleArtifactsPlugin(),
		new webpack.SourceMapDevToolPlugin( {
			filename: '[file].map[query]',
			test: /\.css($|\?)/i,
			append: '\n/*# sourceMappingURL=[url] */',
		} ),
	],
	entry: {
		...defaultConfig.entry(),
		...styleEntries,
	},
};
