var path = require('path')
var webpack = require('webpack')
// var ExtractTextPlugin = require('extract-text-webpack-plugin')

module.exports = {
  entry: "./flexyadmin/assets/js/main.js",
  output: {
    path: __dirname + '/flexyadmin/assets/dist/',
    publicPath:'/flexyadmin/assets/dist/',
    filename: "bundle.js"
  },
  
  module: {
      rules: [
        
        // eslint
        {
          enforce: 'pre',
          test: /.vue$/,
          loader: 'eslint',
          exclude: /node_modules/
        },
        // vue loader
        {
          test: /\.vue$/,
          loader: 'vue',
          options: {
            // vue-loader options go here
          }
        },
        // babel
        {
          test: /\.js$/,
          loader: 'babel',
          exclude: /node_modules/
        },
        
        // sass,css
        {
          test: /\.scss$/,
          loaders: [ 'style', 'css?sourceMap', 'sass?sourceMap' ]
          // loader: ExtractTextPlugin.extract({
          //   fallbackLoader: "style-loader",
          //   loader: "css-loader!sass-loader"
          // })
          // loader: ExtractTextPlugin.extract(
          //   'css?sourceMap!sass?sourceMap'
          // )
        },
        // fonts
        {
          test: /\.(eot|svg|ttf|woff|woff2)$/,
          loader: 'file?name=[name].[ext]'
        }
        
        // {
        //   test: /\.(png|jpg|gif|svg)$/,
        //   loader: 'file',
        //   options: {
        //     name: '[name].[ext]?[hash]'
        //   }
        // }
      ]
  },
  
  plugins: [
    // new ExtractTextPlugin('../dist/bundle.css'),
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        warnings: false
      }
    }),
  ],
  
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue.js'
    }
  }
  
};