const NODE_ENV = process.env.NODE_ENV || 'development';
const webpack = require('webpack');

module.exports = {
	devtool: NODE_ENV == 'development' ? 'source-map' : '',
	entry: "./src/index",
	output: {
		filename: "build.js"
	},
	plugins: [],
	module: {
		loaders: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader'
			},
			{
				test: /\.css$/,
				loader: [
					'style-loader',
					'css-loader'
				]
			}
		]
	},

}

if (NODE_ENV != 'development') {
	module.exports.plugins.push(	
		new webpack.optimize.UglifyJsPlugin({
			compress: {
				warnings:     false,
				drop_console: true, 
				unsafe:       true
			}
		})
	);
}
