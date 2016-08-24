var webpack = require('webpack');
require('dotenv').config();

module.exports = {
  entry: "./src/main.jsx",
  output: {
    path: __dirname,
    filename: "bundle.js"
  },
  module: {
    loaders: [
      { test: /\.jsx?$/, exclude: /node_modules/, loader: 'babel', query: {
        presets: ['react', 'es2015', 'stage-2']
      }}
    ]
  },
  resolve: {
    extensions: ['', '.js', '.jsx', '.styl']
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'API_URL': JSON.stringify(process.env.API_URL)
      }
    }),
  ]
};
