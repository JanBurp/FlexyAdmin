var webpack           = require('webpack');
var path              = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var LiveReloadPlugin  = require('webpack-livereload-plugin');

module.exports = [
{
  name    : 'js',
  entry   : "./flexyadmin/assets/js/main.js",
  output  : {
    path              : __dirname + '/flexyadmin/assets/dist/',
    publicPath        : '/flexyadmin/assets/dist/',
    filename          : "bundle.js",
    devtoolLineToLine : true,
    pathinfo          : true,
  },
  module  : {
      rules : [
        // eslint
        {
          enforce : 'pre',
          test    : /.vue$/,
          loader  : 'eslint',
          exclude : /node_modules/
        },
        // vue loader
        {
          test    : /\.vue$/,
          loader  : 'vue',
          options : {}
        },
        // babel
        {
          test    : /\.js$/,
          loader  : 'babel',
          exclude : /node_modules/
        }
        
      ]
  },
  plugins: [
  ],
  
  resolve: {
    // alias: {'vue$': 'vue/dist/vue.js'},
    alias: {'vue$': 'vue/dist/vue.common.js'}
  }
}];

module.exports.push(
{
  name: 'scss',
  entry: "./flexyadmin/assets/scss/flexyadmin.scss",
  output: {
    path: __dirname + '/flexyadmin/assets/dist/',
    publicPath:'/flexyadmin/assets/dist/',
    filename: "bundle.css",
  },
  module: {
      rules: [
        // sass,css
        {
          test: /\.scss$/,
          // loaders: ['css?sourceMap','sass?sourceMap']
          loader: ExtractTextPlugin.extract({
            loader: "css-loader?sourceMap!sass-loader?sourceMap"
          })
        }
        // fonts
        // {
        //   test: /\.(eot|svg|ttf|woff|woff2)$/,
        //   loader: 'file?name=[name].[ext]'
        // }
        // {
        //   test: /\.(png|jpg|gif|svg)$/,
        //   loader: 'file',
        //   options: {
        //     name: '[name].[ext]'
        //   }
        // }
      ]
  },
  devtool: "source-map",
  plugins: [
    new ExtractTextPlugin({
      filename: "flexyadmin.css",
      disable: false,
      allChunks: true
    }),
    new LiveReloadPlugin()
  ]
}
);



if (process.env.NODE_ENV === 'watch') {
  module.exports[0].plugins = (module.exports.plugins || []).concat([
    
    new LiveReloadPlugin()

  ])
}

if (process.env.NODE_ENV === 'production') {
  module.exports[0].plugins = (module.exports.plugins || []).concat([
    
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        warnings: false
      }
    }),
    
    new webpack.LoaderOptionsPlugin({
      minimize: true
    })
  ])
}

