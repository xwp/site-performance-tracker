/**
 * External dependencies
 */

// TerserPlugin is bundled in Webpack 5.
// eslint-disable-next-line import/no-extraneous-dependencies
const TerserPlugin = require( 'terser-webpack-plugin' );
// path is a native Node module
// eslint-disable-next-line import/no-extraneous-dependencies
const path = require( 'path' );
const WebpackBar = require( 'webpackbar' );

const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

const config = {
	srcDir: '/js/src/',
	distDirModern: 'js/dist/module/',
	distDirLegacy: 'js/dist/nomodule/',
};

config.modernJsEntries = {
	'web-vitals-analytics': config.srcDir + 'web-vitals-analytics.js',
};

config.legacyJsEntries = {};

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

/**
 * Remove the default `DependencyExtractionWebpackPlugin` instance from
 * plugins and instantiate a new one with our own options.
 */
defaultConfig.plugins = defaultConfig.plugins.filter(
	( plugin ) => plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
);
defaultConfig.plugins.push( new DependencyExtractionWebpackPlugin( {
	outputFilename: '[name].asset.php',
} ) );

const sharedConfig = {
	optimization: {
		minimizer: [
			new TerserPlugin( {
				terserOptions: {
					output: {
						comments: /translators:/i,
					},
				},
				extractComments: false,
			} ),
		],
	},
	plugins: [ ...defaultConfig.plugins ],
};

const configureBabelLoader = ( browserlist ) => {
	return {
		test: /\.js$/,
		use: {
			loader: 'babel-loader',
			options: {
				babelrc: false,
				exclude: [ /core-js/, /regenerator-runtime/ ],
				presets: [
					[
						'@babel/preset-env',
						{
							loose: true,
							modules: false,
							// debug: true,
							corejs: 3,
							useBuiltIns: 'usage',
							targets: {
								browsers: browserlist,
							},
						},
					],
				],
				plugins: [ '@babel/plugin-syntax-dynamic-import' ],
			},
		},
	};
};

const modernConfig = {
	output: {
		path: path.join( __dirname, config.distDirModern ),
		filename: `[name].[chunkhash].js`,
		chunkFilename: `[name].[chunkhash].js`,

		// Needed for [chunkhash] length to match the hard-coded
		// settings in `DependencyExtractionWebpackPlugin`.
		hashDigestLength: 32,
	},
	module: {
		rules: [
			configureBabelLoader( [
				// The last two versions of each browser, excluding versions
				// that don't support <script type="module">.
				'last 2 Chrome versions',
				'not Chrome < 60',
				'last 2 Safari versions',
				'not Safari < 10.1',
				'last 2 iOS versions',
				'not iOS < 10.3',
				'last 2 Firefox versions',
				'not Firefox < 54',
				'last 2 Edge versions',
				'not Edge < 15',
			] ),
		],
	},
};

const modernJs = {
	...defaultConfig,
	...sharedConfig,
	...modernConfig,
	entry: config.modernJsEntries,
	plugins: [
		...sharedConfig.plugins,
		// Display nice progress bar while building or watching.
		new WebpackBar( {
			name: 'ModernJs',
			color: '#36f271',
		} ),
	],
};

module.exports = [ modernJs ];
