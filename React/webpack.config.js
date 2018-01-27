const path = require('path');
module.exports = {
	devtool: 'source-map', 
	entry: "./src/index",
	output: {
		filename: "build.js"
	},
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
	}
}