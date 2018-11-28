var webpack           = require('webpack');
var path              = require('path');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var LiveReloadPlugin  = require('webpack-livereload-plugin');
// var CompressionPlugin = require("compression-webpack-plugin");
var OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

module.exports = [];


// Main JS
module.exports.push(
{
  name    : 'js',
  entry   : "./flexyadmin/assets/js/main.js",
  output  : {
    path              : __dirname + '/flexyadmin/assets/dist/',
    publicPath        : '/flexyadmin/assets/dist/',
    filename          : "main.build.js",
    devtoolLineToLine : true,
    pathinfo          : true,
  },
  module  : {
      rules : [
        // eslint
        {
          enforce : 'pre',
          test    : /.vue$/,
          loader  : 'eslint-loader',
          exclude : /node_modules/
        },
        // vue loader
        {
          test    : /\.vue$/,
          loader  : 'vue-loader',
          options : {}
        },
        // babel
        {
          test    : /\.js$/,
          loader  : 'babel-loader',
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
});


// Login JS
module.exports.push(
{
  name    : 'js',
  entry   : "./flexyadmin/assets/js/login.js",
  output  : {
    path              : __dirname + '/flexyadmin/assets/dist/',
    publicPath        : '/flexyadmin/assets/dist/',
    filename          : "login.build.js",
    devtoolLineToLine : true,
    pathinfo          : true,
  },
  module  : {
      rules : [
        // eslint
        {
          enforce : 'pre',
          test    : /.vue$/,
          loader  : 'eslint-loader',
          exclude : /node_modules/
        },
        // vue loader
        {
          test    : /\.vue$/,
          loader  : 'vue-loader',
          options : {}
        },
        // babel
        {
          test    : /\.js$/,
          loader  : 'babel-loader',
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
});


// Styling (scss,css,fonts)
module.exports.push(
{
  name: 'scss',
  entry: "./flexyadmin/assets/scss/flexyadmin.scss",
  output: {
    path: __dirname + '/flexyadmin/assets/dist/',
    publicPath:'',
    filename: "flexyadmin.css",
  },
  module: {
    rules: [
      // sass,css
      {
        test: /\.scss$/,
        loader: ExtractTextPlugin.extract(['css-loader', 'sass-loader'])
      },
      // fonts
      {
        test: /\.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
        loader: 'file-loader?name=fonts/[name].[ext]'
      },
    ]
  },
  devtool: "source-map",
  plugins: [
    new ExtractTextPlugin({
      filename: "flexyadmin.css",
      disable: false,
      allChunks: true
    }),
    new LiveReloadPlugin(),
  ]
});


// WATCH
if (process.env.NODE_ENV === 'watch') {
  // main.bundle.js
  module.exports[0].plugins = (module.exports.plugins || []).concat([
    new LiveReloadPlugin(),
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: true
    }),
  ]);
}



// BUILD
if (process.env.NODE_ENV === 'production') {

  // main.bundle.js
  module.exports[0].plugins = (module.exports.plugins || []).concat([
    
    new webpack.DefinePlugin({
      'process.env': {
        'NODE_ENV': JSON.stringify('production')
      }
    }),
    new webpack.optimize.AggressiveMergingPlugin(),
    new webpack.optimize.OccurrenceOrderPlugin(),
    new webpack.optimize.UglifyJsPlugin({
      mangle:true,
      comments: false, // remove comments
      compress: {
        booleans      : true,
        comparisons   : true,
        conditionals  : true,
        dead_code     : true,
        dead_code     : true, // big one--strip code that will never execute
        drop_console  : true, // strips console statements
        drop_debugger : true,
        evaluate      : true,
        evaluate      : true,
        if_return     : true,
        join_vars     : true,
        pure_getters  : true,
        screw_ie8     : true,
        sequences     : true,
        sequences     : true,
        unsafe        : true,
        unsafe_comps  : true,
        unused        : true,
        warnings      : false, // good for prod apps so users can't peek behind curtain
      },
      exclude: [/\.min\.js$/gi] // skip pre-minified libs
    }),

    new webpack.LoaderOptionsPlugin({
      minimize: true
    }),

    // new CompressionPlugin({
    //   // asset: "[path].gz[query]",
    //   // algorithm: "gzip",
    //   test: /\.js/,
    //   // threshold: 10240,
    //   // minRatio: 0
    // }),

  ]);

  // Ook voor login.bundle.js
  module.exports[1].plugins = module.exports[0].plugins;


  // *.css
  module.exports[2].plugins = (module.exports.plugins || []).concat([
    new ExtractTextPlugin("flexyadmin.css"),
    new OptimizeCssAssetsPlugin({
      assetNameRegExp: /\.*\.css$/g,
      cssProcessor: require('cssnano'),
      cssProcessorOptions: { discardComments: {removeAll: true } },
      canPrint: false,
    }),
  ]);

}

