var webpack = require('webpack');
var precss = require('precss');
var autoprefixer = require('autoprefixer');
var nested = require('postcss-nested');
var postcssImport = require('postcss-import');
var lost = require('lost');
var customProperties = require('postcss-custom-properties');
require('dotenv').config();

module.exports = {
  entry: "./src/main.jsx",
  output: {
    path: __dirname,
    filename: "bundle.js"
  },
  module: {
    loaders: [{
      test: /\.jsx?$/,
      exclude: /node_modules/,
      loader: 'babel',
      query: {
        presets: ['react', 'es2015', 'stage-2']
      }
    }, {
      test: /\.css$/,
      loader: "style-loader!css-loader!postcss-loader"
    }, {
      test: /\.(jpe?g|png|gif|svg)$/i,
      loaders: [
        'file?hash=sha512&digest=hex&name=[hash].[ext]',
        'image-webpack?bypassOnDebug&optimizationLevel=7&interlaced=false'
      ]
    }]
  },
  postcss: function () {
    return [
      precss,
      postcssImport,
      customProperties,
      nested,
      lost(),
      autoprefixer({
        browsers: ['last 1 version']
      })
    ];
  },
  resolve: {
    extensions: ['', '.js', '.jsx', '.styl', '.css']
  },
  plugins: [
    new webpack.DefinePlugin({
      'API_URL': JSON.stringify(process.env.API_URL)
    }),
  ]
};
