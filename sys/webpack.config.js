var path = require('path')
var webpack = require('webpack')


module.exports = {
  entry: "./flexyadmin/assets/js/main.js",
  output: {
    path: __dirname + '/flexyadmin/assets/dist/',
    publicPath:'/flexyadmin/assets/dist/',
    filename: "bundle.js"
  },
  
  module: {
      rules: [
        {
          test: /\.vue$/,
          loader: 'vue',
          options: {
            // vue-loader options go here
          }
        },
        {
          test: /\.js$/,
          loader: 'babel',
          exclude: /node_modules/
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
  
  
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue.js'
    }
  }
  
};