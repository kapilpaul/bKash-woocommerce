const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

const config = {
	entry: {
		app: './assets/src/admin/index.js',
		upgrade: './assets/src/upgrade/index.js'
	},
	output: {
		path: path.resolve( __dirname, './assets/js/' ),
		filename: '[name].js'
	},
	devServer: {
		contentBase: path.join( __dirname, 'dist' ),
		headers: {
			'Access-Control-Allow-Origin': '*',
			'Access-Control-Allow-Methods':
				'GET, POST, PUT, DELETE, PATCH, OPTIONS',
			'Access-Control-Allow-Headers':
				'X-Requested-With, content-type, Authorization'
		}
	},
	externals: {
		'@wordpress/api-fetch': [ 'wp', 'apiFetch' ]
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				use: 'babel-loader',
				exclude: /node_modules/
			},
			{
				test: /\.css$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'postcss-loader'
				]
			},
			{
				test: /\.scss$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'postcss-loader',
					'sass-loader'
				]
			},
			{
				test: /\.svg$/,
				use: 'file-loader'
			},
			{
				test: /\.png$/,
				use: [
					{
						loader: 'url-loader',
						options: {
							mimetype: 'image/png'
						}
					}
				]
			},
			{
				test: /\.gif$/,
				use: [
					{
						loader: 'url-loader',
						options: {
							mimetype: 'image/gif'
						}
					}
				]
			}
		]
	},
	resolve: {
		extensions: [ '.js', '.jsx' ],
		alias: {
			'react-dom': '@hot-loader/react-dom'
		},
		fallback: {
			path: require.resolve( 'path-browserify' )
		}
	},
	optimization: {
		runtimeChunk: 'single',
		splitChunks: {
			cacheGroups: {
				vendor: {
					test: /[\\/]node_modules[\\/]/,
					name: 'vendors',
					chunks: 'all'
				}
			}
		}
	}
};

module.exports = ( env, argv ) => {
	let cssFileName = '../css/[name].css';

	if ( argv.hot ) {

		// Cannot use 'contenthash' when hot reloading is enabled.
		config.output.filename = '[name].js';
		cssFileName = '[name].css';
	}

	config.plugins = [
		new MiniCssExtractPlugin( {
			filename: cssFileName
		} )
	];

	return config;
};
